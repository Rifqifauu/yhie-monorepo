<?php

namespace App\Http\Controllers;

use App\Services\PaymentGatewayService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayService $pgService,
        private TransactionService $transactionService
    ) {}

    /**
     * POST /api/webhooks/doku
     */
    public function handle(Request $request)
    {
        // 1. Ambil raw body dan headers
        $rawBody = $request->getContent();
        $headers = $request->headers->all();

        try {
            // 2. Validasi Signature Keamanan dari DOKU
            $isValid = $this->pgService->verifyNotificationSignature($headers, $rawBody);

            if (!$isValid) {
                Log::warning("DOKU Webhook: Invalid Signature", ['headers' => $headers]);
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            // 3. Eksekusi update database di Service
            $payload = json_decode($rawBody, true);
            $this->transactionService->handleDokuNotification($payload);

            // 4. SELALU kembalikan HTTP 200 OK agar DOKU tidak melakukan retry (mengirim ulang notifikasi)
            return response()->json(['message' => 'Notification processed successfully'], 200);

        } catch (\Throwable $e) {
            Log::error("DOKU Webhook Error: " . $e->getMessage());

            // Tetap return 500 jika ada error logic agar kita bisa trace di log
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
