<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            "payment_status" => "required|in:pending,completed,failed",
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
     * PUT /api/transactions/{transaction}
     */
    public function update(
        Request $request,
        Transaction $transaction,
    ): JsonResponse {
        $data = $request->validate([
            "amount" => "sometimes|numeric|min:0",
            "payment_status" => "sometimes|in:pending,completed,failed",
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
