<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Exception;

class TransactionService
{
    protected PaymentGatewayService $pgService;

    /**
     * Inject PaymentGatewayService ke dalam TransactionService
     */
    public function __construct(PaymentGatewayService $pgService)
    {
        $this->pgService = $pgService;
    }

    /**
     * Generate/refresh link pembayaran DOKU untuk transaksi yang SUDAH ada
     * (mis. awalnya transfer manual, lalu registran pilih bayar via DOKU dari
     * halaman invoice). Dipanggil publik lewat endpoint bertopeng reference_id,
     * BUKAN id mentah, supaya tidak bisa ditebak/di-enumerate orang lain
     * (lihat TransactionController::generatePayment).
     */
    public function generatePaymentUrl(
        Transaction $transaction,
        bool $failTransactionOnError = false,
    ): Transaction {
        if ($transaction->payment_status !== 'pending') {
            throw new Exception("Invoice is no longer awaiting payment.", 422);
        }

        $registration = $transaction->programRegistration
            ?? $transaction->load('programRegistration')->programRegistration;

        if (!$registration) {
            throw new Exception("Program registration data is missing for this transaction.", 404);
        }

        $dokuPayload = [
            'amount' => $transaction->amount,
            'invoice_number' => $transaction->reference_id,
            'customer_name' => $registration->full_name,
            'customer_email' => $registration->email,
            'customer_phone' => $registration->phone,
        ];

        $dokuResponse = $this->pgService->createDokuPayment($dokuPayload);

        if (isset($dokuResponse['response']['payment']['url'])) {
            $transaction->update([
                'payment_method' => 'doku_hosted',
                'payment_url' => $dokuResponse['response']['payment']['url'],
                'pg_transaction_id' => $dokuResponse['response']['order']['invoice_number'] ?? null,
                'pg_response' => json_encode($dokuResponse),
            ]);
        } else {
            // Kalau ini transaksi baru khusus PG (tidak ada opsi lain), gagal
            // total. Kalau ini transaksi transfer manual yang cuma "coba"
            // bayar pakai DOKU, biarkan tetap pending - transfer manual masih
            // bisa dipakai sebagai jalur pembayaran.
            if ($failTransactionOnError) {
                $transaction->update(['payment_status' => 'failed']);
            }
            throw new Exception("Gagal membuat link pembayaran DOKU: " . json_encode($dokuResponse), 500);
        }

        return $transaction->fresh()->load('programRegistration.program');
    }

    /**
     * Proses notifikasi webhook dari DOKU, update status transaksi (idempotent).
     * Dipanggil oleh DokuWebhookController setelah signature terverifikasi.
     */
    public function handleDokuNotification(array $payload): Transaction
    {
        $invoiceNumber = $payload['order']['invoice_number'] ?? null;

        if (!$invoiceNumber) {
            throw new Exception("Invoice number not found in payload", 400);
        }

        $transaction = Transaction::where('reference_id', $invoiceNumber)->first();

        if (!$transaction) {
            throw new Exception("Transaction not found for invoice: " . $invoiceNumber, 404);
        }

        // Jika transaksi sudah selesai sebelumnya, abaikan (idempotent)
        if ($transaction->payment_status === 'completed') {
            return $transaction;
        }

        $transactionStatus = $payload['transaction']['status'] ?? '';

        if (in_array(strtolower($transactionStatus), ['success', 'settlement'])) {
            $transaction->update([
                'payment_status' => 'completed',
                'paid_at' => now(),
                'pg_response' => json_encode($payload),
            ]);

            // Update status registrasi menjadi 'approved' agar bisa memicu
            // pembuatan akun Moodle secara otomatis.
            $transaction->programRegistration()->update(['status' => 'approved']);
        } elseif (in_array(strtolower($transactionStatus), ['failed', 'expired'])) {
            $transaction->update([
                'payment_status' => 'expired',
                'pg_response' => json_encode($payload),
            ]);
        }

        return $transaction;
    }

    /**
     * Simpan bukti transfer manual untuk sebuah invoice yang masih pending.
     */
    public function uploadReceipt(string $referenceId, $file): Transaction
    {
        $transaction = Transaction::where('reference_id', $referenceId)->first();

        if (!$transaction) {
            throw new Exception("Invoice not found.", 404);
        }

        if ($transaction->payment_status !== 'pending') {
            throw new Exception("Invoice is no longer awaiting payment.", 422);
        }

        // Hapus bukti transfer lama (kalau ini re-upload) sebelum simpan yang baru.
        if ($transaction->transaction_receipt) {
            Storage::disk('public')->delete(
                str_replace('/storage/', '', $transaction->transaction_receipt),
            );
        }

        $transaction->update([
            'transaction_receipt' => '/storage/' . $file->store('receipts', 'public'),
            'payment_method' => 'manual_transfer',
        ]);

        return $transaction->fresh()->load('programRegistration.program');
    }
}
