<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    protected $clientId;
    protected $secretKey;
    protected $baseUrl;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->clientId = config('services.doku.client_id');
        $this->secretKey = config('services.doku.secret_key');
        $this->baseUrl = config('services.doku.base_url');
    }

    /**
     * Membuat request pembayaran ke DOKU (Contoh: DOKU Checkout)
     * * @param array $orderData Data pesanan (amount, invoice_number, dll)
     * @return array
     */
    public function createDokuPayment(array $orderData)
    {
        $path = '/checkout/v1/payment';
        $url = $this->baseUrl . $path;

        $requestId = (string) Str::uuid();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $payload = [
            "order" => [
                "amount" => $orderData['amount'],
                "invoice_number" => $orderData['invoice_number'],
                "currency" => "IDR",
                "callback_url" => $orderData['callback_url'] ?? url('/payment/callback'),
                // Wajib menurut dokumentasi DOKU Checkout - tanpa ini halaman
                // checkout gagal render metode pembayaran (500 di sisi DOKU).
                "auto_redirect" => true,
            ],
            "payment" => [
                "payment_due_date" => 60 // Masa aktif pembayaran dalam menit
            ],
            "customer" => [
                "name" => $orderData['customer_name'] ?? 'Guest',
                "email" => $orderData['customer_email'] ?? 'guest@example.com',
                "phone" => $orderData['customer_phone'] ?? null,
            ]
        ];

        // Generate Signature (Wajib untuk API Jokul DOKU)
        $signature = $this->generateSignature($path, $requestId, $timestamp, $payload);

        // Kirim HTTP POST request
        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        return $response->json();
    }

    /**
     * Membuat HMAC-SHA256 Signature sesuai standar DOKU Jokul
     */
    protected function generateSignature($path, $requestId, $timestamp, $payload)
    {
        $body = json_encode($payload);

        // 1. Buat Digest dari body request
        $digest = base64_encode(hash('sha256', $body, true));

        // 2. Gabungkan komponen signature
        $rawSignature = "Client-Id:" . $this->clientId . "\n" .
                        "Request-Id:" . $requestId . "\n" .
                        "Request-Timestamp:" . $timestamp . "\n" .
                        "Request-Target:" . $path . "\n" .
                        "Digest:" . $digest;

        // 3. Hash dengan HMAC-SHA256 menggunakan Secret Key
        $signature = base64_encode(hash_hmac('sha256', $rawSignature, $this->secretKey, true));

        return "HMACSHA256=" . $signature;
    }
    public function verifyNotificationSignature(array $headers, string $rawBody): bool
        {
            $clientId = $headers['client-id'][0] ?? '';
            $requestId = $headers['request-id'][0] ?? '';
            $timestamp = $headers['request-timestamp'][0] ?? '';
            $dokuSignature = $headers['signature'][0] ?? '';

            // DOKU Webhook Target Path biasanya sesuai dengan URL endpoint Anda
            $requestTarget = '/api/webhooks/doku';

            $digest = base64_encode(hash('sha256', $rawBody, true));

            $rawSignature = "Client-Id:" . $clientId . "\n" .
                            "Request-Id:" . $requestId . "\n" .
                            "Request-Timestamp:" . $timestamp . "\n" .
                            "Request-Target:" . $requestTarget . "\n" .
                            "Digest:" . $digest;

            $expectedSignature = base64_encode(hash_hmac('sha256', $rawSignature, $this->secretKey, true));

            return "HMACSHA256=" . $expectedSignature === $dokuSignature;
        }
}
