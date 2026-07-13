<?php

namespace App\Services;

use App\Models\ProgramRegistration;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
     * 1. Membuat transaksi baru dan men-generate link DOKU
     */
     public function createTransactionWithPG(Transaction $transaction): Transaction
         {
             // Pastikan relasi programRegistration ada (untuk nama/email customer di DOKU)
             $transaction->loadMissing('programRegistration');
             $registration = $transaction->programRegistration;

             if (!$registration) {
                 throw new Exception("Program registration data is missing for this transaction.", 404);
             }

             // Update penanda bahwa transaksi ini sekarang menggunakan metode DOKU (opsional)
             $transaction->update(['payment_method' => 'doku_hosted']);

             // Siapkan payload untuk API DOKU menggunakan data transaksi yang sudah ada
             $dokuPayload = [
                 'amount' => $transaction->amount,
                 'invoice_number' => $transaction->reference_id, // Gunakan reference_id yang sudah ada
                 'customer_name' => $registration->full_name,
                 'customer_email' => $registration->email,
                 // Opsional: tambahkan callback_url di sini jika tidak di-set default di PaymentGatewayService
             ];

             // Tembak API DOKU via PaymentGatewayService
             $dokuResponse = $this->pgService->createDokuPayment($dokuPayload);

             // Cek apakah DOKU berhasil mengembalikan URL pembayaran
             if (isset($dokuResponse['response']['payment']['url'])) {
                 // Update transaksi dengan URL dan raw response dari DOKU
                 $transaction->update([
                     'payment_url' => $dokuResponse['response']['payment']['url'],
                     'pg_transaction_id' => $dokuResponse['response']['order']['invoice_number'] ?? null,
                     'pg_response' => json_encode($dokuResponse),
                 ]);
             } else {
                 // Jika gagal tembak API DOKU, lemparkan error tanpa mengubah status menjadi failed
                 // (Agar user masih bisa mencoba lagi atau bayar manual)
                 throw new Exception("Gagal membuat link pembayaran DOKU. Pastikan credential valid.", 500);
             }

             return $transaction->fresh()->load('programRegistration.program');
         }

    /**
     * 2. Menangani notifikasi webhook otomatis dari DOKU
     */
    public function handleDokuNotification(array $payload): Transaction
    {
        // Ambil nomor invoice dari payload DOKU
        $invoiceNumber = $payload['order']['invoice_number'] ?? null;

        if (!$invoiceNumber) {
            throw new Exception("Invoice number not found in payload", 400);
        }

        $transaction = Transaction::where('reference_id', $invoiceNumber)->first();

        if (!$transaction) {
            throw new Exception("Transaction not found for invoice: " . $invoiceNumber, 404);
        }

        // Jika transaksi sudah berstatus completed sebelumnya, abaikan (Idempotent)
        if ($transaction->payment_status === 'completed') {
            return $transaction;
        }

        // Cek status dari DOKU
        $transactionStatus = $payload['transaction']['status'] ?? '';

        if (in_array(strtolower($transactionStatus), ['success', 'settlement'])) {
            // Jika sukses dibayar
            $transaction->update([
                'payment_status' => 'completed',
                'paid_at' => now(),
                'pg_response' => json_encode($payload)
            ]);

            // Opsional: Langsung ubah status pendaftaran jadi approved
            // Anda bisa menggunakan Observers/Events di Laravel untuk memicu pembuatan Moodle
            $transaction->programRegistration()->update(['status' => 'approved']);

        } elseif (in_array(strtolower($transactionStatus), ['failed', 'expired'])) {
            // Jika gagal atau waktu pembayaran habis
            $transaction->update([
                'payment_status' => 'expired',
                'pg_response' => json_encode($payload)
            ]);
        }

        return $transaction;
    }

    /**
     * 3. Menangani logika upload bukti bayar manual (Fallback jika PG bermasalah / user pilih manual)
     */
    public function uploadReceipt(string $referenceId, $file): Transaction
    {
        $transaction = Transaction::where("reference_id", $referenceId)->first();

        if (!$transaction) {
            throw new Exception("Invoice not found.", 404);
        }

        if ($transaction->payment_status !== "pending") {
            throw new Exception("Invoice is no longer awaiting payment.", 422);
        }

        // Hapus file lama jika ada (menghindari penumpukan storage)
        if ($transaction->transaction_receipt) {
            Storage::disk("public")->delete(
                str_replace("/storage/", "", $transaction->transaction_receipt)
            );
        }

        // Simpan file gambar yang baru
        $path = $file->store("receipts", "public");

        $transaction->update([
            "transaction_receipt" => "/storage/" . $path,
            "payment_method" => "manual_transfer" // Pastikan status berubah ke manual jika user upload struk
        ]);

        return $transaction->fresh()->load("programRegistration.program");
    }
}
