<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadReceiptRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;


    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

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
                    return $query->whereHas("programRegistration", function ($q) use ($search) {
                        $q->where("full_name", "like", "%{$search}%")
                            ->orWhere("email", "like", "%{$search}%")
                            ->orWhere("phone", "like", "%{$search}%");
                    });
                })
                ->paginate(15);

            return response()->json([
                "message" => "Transactions fetched successfully.",
                // Gunakan resource collection untuk memformat data list/pagination
                "data" => TransactionResource::collection($transactions)->response()->getData(true),
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Error fetching transactions: " . $e->getMessage());
            return response()->json([
                "message" => "Failed to fetch transactions.",
            ], 500);
        }
    }

    /**
     * GET /api/transactions/{transaction}
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->loadMissing(["programRegistration.program"]);

        return response()->json([
            "message" => "Transaction fetched successfully.",
            "data" => new TransactionResource($transaction),
        ], 200);
    }

    /**
     * GET /api/transactions/track/{referenceId}
     * Public - cek status invoice via nomor invoice.
     */
    public function showByReference(string $referenceId): JsonResponse
    {
        $transaction = Transaction::with('programRegistration.program')
            ->where("reference_id", $referenceId)
            ->first();

        if (!$transaction) {
            return response()->json(["message" => "Invoice not found."], 404);
        }

        return response()->json([
            "message" => "Transaction fetched successfully.",
            "data" => new TransactionResource($transaction),
        ], 200);
    }

    /**
     * POST /api/transactions/search
     * Publik - cari invoice pakai kombinasi email & telepon.
     */
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            "email" => "required|email",
            "phone" => "required|string",
        ]);

        $transactions = Transaction::whereHas("programRegistration", function ($q) use ($data) {
                $q->where("email", $data["email"])->where("phone", $data["phone"]);
            })
            ->with(["programRegistration.program"])
            ->orderBy("created_at", "desc")
            ->get();

        return response()->json([
            "message" => "Transactions fetched successfully.",
            "data" => TransactionResource::collection($transactions),
        ], 200);
    }

    /**
     * POST /api/transactions/track/{referenceId}/receipt
     * Public - upload bukti transfer manual selama invoice masih pending.
     */
    public function uploadReceipt(UploadReceiptRequest $request, string $referenceId): JsonResponse
    {
        try {
            // Logika bisnis (hapus file lama, simpan file baru, update DB) dipindah ke Service
            $transaction = $this->transactionService->uploadReceipt($referenceId, $request->file('receipt'));

            return response()->json([
                "message" => "Receipt uploaded successfully. Please wait for admin verification.",
                "data" => new TransactionResource($transaction),
            ], 200);

        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $statusCode = ($statusCode >= 400 && $statusCode <= 599) ? $statusCode : 500;

            if ($statusCode === 500) {
                Log::error("Error uploading transaction receipt: " . $e->getMessage());
            }

            return response()->json([
                "message" => $statusCode === 500 ? "Failed to upload receipt." : $e->getMessage(),
            ], $statusCode);
        }
    }

    /**
     * POST /api/transactions/track/{referenceId}/generate-payment
     * Public - registran memilih bayar via DOKU dari halaman invoice
     * (transaksi bisa saja awalnya transfer manual). Pakai reference_id
     * (bukan id mentah) supaya tidak bisa ditebak/di-enumerate orang lain.
     */
    public function generatePayment(string $referenceId): JsonResponse
    {
        $transaction = Transaction::where("reference_id", $referenceId)->first();

        if (!$transaction) {
            return response()->json(["message" => "Invoice not found."], 404);
        }

        try {
            $transaction = $this->transactionService->generatePaymentUrl($transaction);

            return response()->json([
                "message" => "Payment link generated successfully.",
                "data" => new TransactionResource($transaction),
            ], 200);

        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $statusCode = ($statusCode >= 400 && $statusCode <= 599) ? $statusCode : 500;

            if ($statusCode === 500) {
                Log::error("Error generating PG payment link: " . $e->getMessage());
            }

            return response()->json([
                "message" => $statusCode === 500 ? "Failed to generate payment link." : $e->getMessage(),
            ], $statusCode);
        }
    }

    /**
     * PUT /api/transactions/{transaction}
     */
    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $data = $request->validate([
            "amount" => "sometimes|numeric|min:0",
            "payment_status" => "sometimes|in:pending,completed,failed,expired",
        ]);

        try {
            $transaction->update($data);
            $transaction->loadMissing(["programRegistration.program"]);

            return response()->json([
                "message" => "Transaction updated successfully.",
                "data" => new TransactionResource($transaction),
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Error updating transaction: " . $e->getMessage());
            return response()->json([
                "message" => "Failed to update transaction. Please try again later.",
            ], 500);
        }
    }

    /**
     * DELETE /api/transactions/{transaction}
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        try {
            $transaction->delete();

            return response()->json([
                "message" => "Transaction deleted successfully.",
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Error deleting transaction: " . $e->getMessage());
            return response()->json([
                "message" => "Failed to delete transaction.",
            ], 500);
        }
    }
}
