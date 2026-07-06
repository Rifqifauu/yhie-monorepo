<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\ProgramRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    /**
     * GET /api/certificates
     * Admin endpoint - paginated list
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query("search");

        try {
            $certificates = Certificate::with(["programRegistration", "program"])
                ->orderBy("created_at", "desc")
                ->when($search, function ($query, $search) {
                    return $query
                        ->where("certificate_number", "like", "%{$search}%")
                        ->orWhereHas("programRegistration", function ($q) use ($search) {
                            $q->where("full_name", "like", "%{$search}%");
                        });
                })
                ->paginate(15);

            return response()->json(
                [
                    "message" => "Certificates fetched successfully.",
                    "data" => $certificates,
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error fetching certificates: " . $e->getMessage());
            return response()->json(
                ["message" => "Failed to fetch certificates."],
                500,
            );
        }
    }

    /**
     * POST /api/certificates
     * Terbitkan sertifikat untuk sebuah pendaftaran (dipicu manual oleh admin).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "program_registration_id" =>
                "required|exists:program_registrations,id",
            // ID akun Moodle/SSO - nullable, diisi manual selama integrasinya belum ada
            "external_user_id" => "nullable|integer",
            "issued_at" => "sometimes|date",
        ]);

        try {
            $registration = ProgramRegistration::find(
                $data["program_registration_id"],
            );

            $certificate = Certificate::create([
                "program_registration_id" => $registration->id,
                "program_id" => $registration->program_id, // ambil dari registrasi, bukan dipercaya dari input client
                "external_user_id" => $data["external_user_id"] ?? null,
                "certificate_number" =>
                    "CERT-" .
                    date("Ymd") .
                    "-" .
                    strtoupper(bin2hex(random_bytes(4))),
                "issued_at" => $data["issued_at"] ?? now()->toDateString(),
            ]);

            return response()->json(
                [
                    "message" => "Certificate issued successfully.",
                    "data" => $certificate->load([
                        "programRegistration",
                        "program",
                    ]),
                ],
                201,
            );
        } catch (\Throwable $e) {
            Log::error("Error issuing certificate: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to issue certificate. Please try again later.",
                ],
                500,
            );
        }
    }

    /**
     * GET /api/certificates/{certificate}
     */
    public function show(Certificate $certificate): JsonResponse
    {
        return response()->json(
            [
                "message" => "Certificate fetched successfully.",
                "data" => $certificate->load(["programRegistration", "program"]),
            ],
            200,
        );
    }

    /**
     * GET /api/certificates/verify/{certificateNumber}
     * Publik - verifikasi keaslian sertifikat lewat nomor sertifikat.
     */
    public function verify(string $certificateNumber): JsonResponse
    {
        $certificate = Certificate::where(
            "certificate_number",
            $certificateNumber,
        )->first();

        if (!$certificate) {
            return response()->json(
                ["message" => "Certificate not found."],
                404,
            );
        }

        return response()->json(
            [
                "message" => "Certificate fetched successfully.",
                "data" => $this->verificationPayload($certificate),
            ],
            200,
        );
    }

    /**
     * Bentuk data verifikasi yang aman untuk endpoint PUBLIK.
     * Tidak membocorkan data pribadi pendaftar (email, telepon, alamat, dsb).
     */
    private function verificationPayload(Certificate $certificate): array
    {
        $certificate->loadMissing(["programRegistration", "program"]);
        $registration = $certificate->programRegistration;
        $program = $certificate->program;

        return [
            "certificate_number" => $certificate->certificate_number,
            "issued_at" => $certificate->issued_at,
            "full_name" => $registration?->full_name,
            "program" => $program
                ? [
                    "title_id" => $program->title_id,
                    "title_en" => $program->title_en,
                ]
                : null,
        ];
    }

    /**
     * PUT /api/certificates/{certificate}
     */
    public function update(
        Request $request,
        Certificate $certificate,
    ): JsonResponse {
        $data = $request->validate([
            "external_user_id" => "sometimes|nullable|integer",
            "issued_at" => "sometimes|date",
            "file_path" => "sometimes|nullable|string",
        ]);

        try {
            $certificate->update($data);

            return response()->json(
                [
                    "message" => "Certificate updated successfully.",
                    "data" => $certificate
                        ->fresh()
                        ->load(["programRegistration", "program"]),
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error updating certificate: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to update certificate. Please try again later.",
                ],
                500,
            );
        }
    }

    /**
     * DELETE /api/certificates/{certificate}
     */
    public function destroy(Certificate $certificate): JsonResponse
    {
        try {
            $certificate->delete();

            return response()->json(
                ["message" => "Certificate deleted successfully."],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting certificate: " . $e->getMessage());
            return response()->json(
                ["message" => "Failed to delete certificate."],
                500,
            );
        }
    }
}
