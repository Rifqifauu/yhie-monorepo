<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use App\Models\Transaction;
use App\Services\MailService;
use App\Services\MoodleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProgramRegistrationController extends Controller
{
    public function __construct(
        private MailService $mailService,
        private MoodleService $moodleService,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "program_id" => "required|exists:programs,id",
            "full_name" => "required|string|max:255",
            "email" => "required|email|max:255",
            "phone" => "required|string|max:30",
            "gender" => "required|in:male,female",
            "age" => "nullable|integer|min:1|max:150",
            "address" => "nullable|string|max:1000",
            "notes" => "nullable|string|max:1000",
            "amount" => "required|numeric|min:0", // Ambil nominal harga program dari frontend/database
            "id_card" => "required|image|mimes:jpeg,png,jpg,webp|max:2048",
            "photo" => "required|image|mimes:jpeg,png,jpg,webp|max:2048",
        ]);

        $data["id_card"] =
            "/storage/" . $request->file("id_card")->store("registrations", "public");
        $data["photo"] =
            "/storage/" . $request->file("photo")->store("registrations", "public");

        // Bungkus dengan DB::transaction demi keamanan data jikalau salah satu query fail
        DB::beginTransaction();

        try {
            // 1. Simpan Data Registrasi
            $registration = ProgramRegistration::create($data);

            // 2. Otomatis Buat Data Transaksi Awal (Pending)
            $transaction = $registration->transactions()->create([
                "reference_id" =>
                    "INV-" .
                    date("Ymd") .
                    "-" .
                    strtoupper(bin2hex(random_bytes(4))),
                "amount" => $data["amount"],
                "payment_status" => "pending",
                "payment_method" => "manual_transfer", // Nanti diganti dinamis kalau sudah pakai PG
            ]);

            DB::commit();

            // Email invoice - dikirim setelah commit, kegagalan kirim tidak menggagalkan pendaftaran
            $this->mailService->sendInvoice($transaction);

            return response()->json(
                [
                    "message" =>
                        "Registration and invoice created successfully.",
                    "data" => $registration->load(["program", "transactions"]),
                ],
                201,
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            // Bersihkan file yang sudah terlanjur tersimpan sebelum transaksi gagal.
            foreach (["id_card", "photo"] as $file) {
                Storage::disk("public")->delete(
                    str_replace("/storage/", "", $data[$file]),
                );
            }

            Log::error(
                "Error creating program registration: " . $e->getMessage(),
            );

            return response()->json(
                [
                    "message" =>
                        "Failed to submit registration. Please try again later.",
                ],
                500,
            );
        }
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->query("search");

        try {
            // Load program beserta histori transaksinya sekalian
            $registrations = ProgramRegistration::with([
                "program",
                "transactions",
            ])
                ->orderBy("created_at", "desc")
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where("full_name", "like", "%{$search}%")
                            ->orWhere("email", "like", "%{$search}%")
                            ->orWhere("phone", "like", "%{$search}%");
                    });
                })
                ->paginate(15);

            return response()->json(
                [
                    "message" => "Registrations fetched successfully.",
                    "data" => $registrations,
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error fetching registrations: " . $e->getMessage());
            return response()->json(
                [
                    "message" => "Failed to fetch registrations.",
                ],
                500,
            );
        }
    }

    public function show(ProgramRegistration $programRegistration): JsonResponse
    {
        return response()->json(
            [
                "message" => "Registration fetched successfully.",
                "data" => $programRegistration->load([
                    "program",
                    "transactions",
                ]),
            ],
            200,
        );
    }

    public function update(
        Request $request,
        ProgramRegistration $programRegistration,
    ): JsonResponse {
        $data = $request->validate([
            "full_name" => "sometimes|string|max:255",
            "email" => "sometimes|email|max:255",
            "phone" => "sometimes|string|max:30",
            "gender" => "sometimes|in:male,female",
            "age" => "nullable|integer|min:1|max:150",
            "address" => "nullable|string|max:1000",
            "notes" => "nullable|string|max:1000",
            "status" => "sometimes|in:pending,approved,rejected",
        ]);

        // Pendaftaran hanya boleh disetujui kalau pembayarannya sudah completed.
        if (
            ($data["status"] ?? null) === "approved" &&
            !$programRegistration
                ->transactions()
                ->where("payment_status", "completed")
                ->exists()
        ) {
            return response()->json(
                [
                    "message" =>
                        "Pendaftaran tidak bisa disetujui karena pembayaran belum selesai (completed).",
                ],
                422,
            );
        }

        $wasApproved = $programRegistration->status === "approved";

        try {
            $programRegistration->update($data);

            // Provisikan akun Moodle sekali saja, tepat saat status baru berubah jadi approved.
            if (
                $programRegistration->status === "approved" &&
                !$wasApproved &&
                !$programRegistration->moodle_user_id
            ) {
                $this->provisionMoodleAccount($programRegistration);
            }

            return response()->json(
                [
                    "message" => "Registration updated successfully.",
                    "data" => $programRegistration
                        ->fresh()
                        ->load(["program", "transactions"]),
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error updating registration: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to update registration. Please try again later.",
                ],
                500,
            );
        }
    }

    /**
     * Buat akun Moodle + kirim emailnya. Dibungkus try/catch supaya kegagalan
     * di sini (mis. Moodle API belum siap) tidak menggagalkan proses approve.
     */
    private function provisionMoodleAccount(
        ProgramRegistration $registration,
    ): void {
        try {
            $result = $this->moodleService->createUser($registration);

            $registration->update([
                "moodle_user_id" => $result["moodle_user_id"],
            ]);

            $this->mailService->sendMoodleAccount(
                $registration,
                $result["username"],
                $result["password"],
            );
        } catch (\Throwable $e) {
            Log::error("Error provisioning Moodle account: " . $e->getMessage());
        }
    }

    public function destroy(
        ProgramRegistration $programRegistration,
    ): JsonResponse {
        try {
            // Karena di migration pakai cascadeOnDelete, transaksi terkait otomatis ikut terhapus
            $programRegistration->delete();

            return response()->json(
                [
                    "message" => "Registration deleted successfully.",
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting registration: " . $e->getMessage());
            return response()->json(
                [
                    "message" => "Failed to delete registration.",
                ],
                500,
            );
        }
    }
}
