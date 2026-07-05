<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * GET /api/transactions
     * Admin endpoint - paginated list
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query("search");

        try {
            $transactions = Transaction::with(["programRegistration.program"])
                ->orderBy("created_at", "desc")
                ->when($search, function ($query, $search) {
                    return $query->whereHas("programRegistration", function (
                        $q,
                    ) use ($search) {
                        $q->where("full_name", "like", "%{$search}%")
                            ->orWhere("email", "like", "%{$search}%")
                            ->orWhere("phone", "like", "%{$search}%");
                    });
                })
                ->paginate(15);

            return response()->json(
                [
                    "message" => "Transactions fetched successfully.",
                    "data" => $transactions,
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error fetching transactions: " . $e->getMessage());
            return response()->json(
                [
                    "message" => "Failed to fetch transactions.",
                ],
                500,
            );
        }
    }

    /**
     * POST /api/transactions
     * Create a transaction linked to a program registration.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "program_registration_id" =>
                "required|exists:program_registrations,id",
            "amount" => "required|numeric|min:0",
            "payment_status" => "required|in:pending,completed,failed,expired",
        ]);

        try {
            $transaction = Transaction::create($data);

            return response()->json(
                [
                    "message" => "Transaction created successfully.",
                    "data" => $transaction->load([
                        "programRegistration.program",
                    ]),
                ],
                201,
            );
        } catch (\Throwable $e) {
            Log::error("Error creating transaction: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to create transaction. Please try again later.",
                ],
                500,
            );
        }
    }

    /**
     * GET /api/transactions/{transaction}
     */
    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json(
            [
                "message" => "Transaction fetched successfully.",
                "data" => $transaction->load(["programRegistration.program"]),
            ],
            200,
        );
    }

    /**
     * GET /api/transactions/track/{referenceId}
     * Public - cek status invoice via nomor invoice (untuk halaman upload bukti transfer).
     */
    public function showByReference(string $referenceId): JsonResponse
    {
        $transaction = Transaction::where(
            "reference_id",
            $referenceId,
        )->first();

        if (!$transaction) {
            return response()->json(["message" => "Invoice not found."], 404);
        }

        return response()->json(
            [
                "message" => "Transaction fetched successfully.",
                "data" => $this->invoicePayload($transaction),
            ],
            200,
        );
    }

    /**
     * Bentuk data invoice yang aman untuk endpoint PUBLIK.
     * Hanya field yang dibutuhkan halaman invoice; tidak membocorkan data
     * pribadi pendaftar (KTP, foto, email, telepon, alamat).
     */
    private function invoicePayload(Transaction $transaction): array
    {
        $transaction->loadMissing("programRegistration.program");
        $registration = $transaction->programRegistration;
        $program = $registration?->program;

        return [
            "reference_id" => $transaction->reference_id,
            "amount" => $transaction->amount,
            "payment_status" => $transaction->payment_status,
            "transaction_receipt" => $transaction->transaction_receipt,
            "program_registration" => $registration
                ? [
                    "full_name" => $registration->full_name,
                    "program" => $program
                        ? [
                            "title_id" => $program->title_id,
                            "title_en" => $program->title_en,
                        ]
                        : null,
                ]
                : null,
        ];
    }

    /**
     * POST /api/transactions/track/{referenceId}/receipt
     * Public - upload bukti transfer manual selama invoice masih pending.
     */
    public function uploadReceipt(
        Request $request,
        string $referenceId,
    ): JsonResponse {
        $data = $request->validate([
            "receipt" => "required|image|mimes:jpeg,png,jpg,webp|max:2048",
        ]);

        $transaction = Transaction::where(
            "reference_id",
            $referenceId,
        )->first();

        if (!$transaction) {
            return response()->json(["message" => "Invoice not found."], 404);
        }

        if ($transaction->payment_status !== "pending") {
            return response()->json(
                ["message" => "Invoice is no longer awaiting payment."],
                422,
            );
        }

        try {
            // Hapus bukti lama agar file tidak menumpuk saat pendaftar upload ulang.
            if ($transaction->transaction_receipt) {
                Storage::disk("public")->delete(
                    str_replace("/storage/", "", $transaction->transaction_receipt),
                );
            }

            $transaction->update([
                "transaction_receipt" =>
                    "/storage/" .
                    $data["receipt"]->store("receipts", "public"),
            ]);

            return response()->json(
                [
                    "message" =>
                        "Receipt uploaded successfully. Please wait for admin verification.",
                    "data" => $this->invoicePayload($transaction->fresh()),
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error uploading transaction receipt: " . $e->getMessage());
            return response()->json(
                ["message" => "Failed to upload receipt."],
                500,
            );
        }
    }

    /**
     * PUT /api/transactions/{transaction}
     */
    public function update(
        Request $request,
        Transaction $transaction,
    ): JsonResponse {
        $data = $request->validate([
            "amount" => "sometimes|numeric|min:0",
            "payment_status" => "sometimes|in:pending,completed,failed,expired",
        ]);

        try {
            $transaction->update($data);

            return response()->json(
                [
                    "message" => "Transaction updated successfully.",
                    "data" => $transaction
                        ->fresh()
                        ->load(["programRegistration.program"]),
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error updating transaction: " . $e->getMessage());
            return response()->json(
                [
                    "message" =>
                        "Failed to update transaction. Please try again later.",
                ],
                500,
            );
        }
    }

    /**
     * DELETE /api/transactions/{transaction}
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        try {
            $transaction->delete();

            return response()->json(
                [
                    "message" => "Transaction deleted successfully.",
                ],
                200,
            );
        } catch (\Throwable $e) {
            Log::error("Error deleting transaction: " . $e->getMessage());
            return response()->json(
                [
                    "message" => "Failed to delete transaction.",
                ],
                500,
            );
        }
    }
}
