<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\ProgramRegistration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class TransactionService
{
    protected PaymentGatewayService $pgService;

    // Inject PaymentGatewayService ke dalam TransactionService
    public function __construct(PaymentGatewayService $pgService)
    {
        $this->pgService = $pgService;
    }

    /**
     * Membuat transaksi baru dan men-generate link DOKU
     */
    public function createTransactionWithPG(array $data): Transaction
    {
        // 1. Ambil data pendaftar untuk dikirim ke DOKU
        $registration = ProgramRegistration::find($data['program_registration_id']);
        if (!$registration) {
            throw new Exception("Program registration not found.", 404);
        }

        // 2. Buat referensi invoice unik (contoh: INV-202607-XXXX)
        $referenceId = 'INV-' . date('Ym') . '-' . strtoupper(Str::random(6));

        // 3. Simpan transaksi awal ke database (sebelum tembak DOKU)
        $transaction = Transaction::create([
            'program_registration_id' => $registration->id,
            'reference_id' => $referenceId,
            'amount' => $data['amount'],
            'payment_status' => 'pending',
            'payment_method' => 'doku_hosted', // Penanda ini bayar via PG
        ]);

        // 4. Siapkan payload untuk DOKU
        $dokuPayload = [
            'amount' => $transaction->amount,
            'invoice_number' => $transaction->reference_id,
            'customer_name' => $registration->full_name,
            'customer_email' => $registration->email,
        ];

        // 5. Tembak API DOKU via PaymentGatewayService
        $dokuResponse = $this->pgService->createDokuPayment($dokuPayload);

        // 6. Cek apakah DOKU berhasil mengembalikan URL pembayaran
        if (isset($dokuResponse['response']['payment']['url'])) {
            // Update transaksi dengan URL dan raw response dari DOKU
            $transaction->update([
                'payment_url' => $dokuResponse['response']['payment']['url'],
                'pg_response' => json_encode($dokuResponse),
            ]);
        } else {
            // Jika gagal tembak API DOKU, update status transaksi
            $transaction->update(['payment_status' => 'failed']);
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
            throw new Exception("Invoice number not found in payload");
        }

        $transaction = Transaction::where('reference_id', $invoiceNumber)->first();

        if (!$transaction) {
            throw new Exception("Transaction not found for invoice: " . $invoiceNumber);
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
    public function uploadReceipt(string $referenceId, UploadedFile $file): Transaction
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
        ]);

        return $transaction->fresh()->load('programRegistration.program');
    }
}
