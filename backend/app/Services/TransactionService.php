<?php

namespace App\Services;

use App\Models\ProgramRegistration;
<<<<<<< HEAD
use App\Models\Transaction;
=======
use Illuminate\Http\UploadedFile;
>>>>>>> 40bd9949cc4c4fec508e77d1d42567e34980d6a8
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
    public function createTransactionWithPG(array $data): Transaction
    {
        // Ambil data pendaftar untuk dikirim ke DOKU
        $registration = ProgramRegistration::find($data['program_registration_id']);
        if (!$registration) {
            throw new Exception("Program registration not found.", 404);
        }

        // Buat referensi invoice unik (contoh: INV-202607-XXXX)
        $referenceId = 'INV-' . date('Ym') . '-' . strtoupper(Str::random(6));

        // Simpan transaksi awal ke database dengan status pending
        $transaction = Transaction::create([
            'program_registration_id' => $registration->id,
            'reference_id' => $referenceId,
            'amount' => $data['amount'],
            'payment_status' => 'pending',
            'payment_method' => 'doku_hosted', // Penanda bayar via PG
        ]);

        // Siapkan payload untuk API DOKU
        $dokuPayload = [
            'amount' => $transaction->amount,
            'invoice_number' => $transaction->reference_id,
            'customer_name' => $registration->full_name,
            'customer_email' => $registration->email,
<<<<<<< HEAD
            // Opsional: tambahkan callback_url di sini jika tidak di-set default di PaymentGatewayService
=======
            'customer_phone' => $registration->phone,
>>>>>>> 40bd9949cc4c4fec508e77d1d42567e34980d6a8
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
            // Jika gagal tembak API DOKU, update status transaksi menjadi failed
            $transaction->update(['payment_status' => 'failed']);
            throw new Exception("Gagal membuat link pembayaran DOKU. Pastikan credential valid.", 500);
        }

        return $transaction->fresh()->load('programRegistration.program');
    }

    /**
<<<<<<< HEAD
     * 2. Menangani notifikasi webhook otomatis dari DOKU
     */
    public function handleDokuNotification(array $payload): Transaction
    {
        // Ambil nomor invoice dari payload DOKU
        $invoiceNumber = $payload['order']['invoice_number'] ?? null;

        if (!$invoiceNumber) {
            throw new Exception("Invoice number not found in payload", 400);
=======
     * Proses notifikasi webhook dari DOKU, update status transaksi (idempotent).
     * Dipanggil oleh DokuWebhookController setelah signature terverifikasi.
     */
    public function handleDokuNotification(array $payload): Transaction
    {
        $invoiceNumber = $payload['order']['invoice_number'] ?? null;

        if (!$invoiceNumber) {
            throw new Exception("Invoice number not found in payload");
>>>>>>> 40bd9949cc4c4fec508e77d1d42567e34980d6a8
        }

        $transaction = Transaction::where('reference_id', $invoiceNumber)->first();

        if (!$transaction) {
<<<<<<< HEAD
            throw new Exception("Transaction not found for invoice: " . $invoiceNumber, 404);
        }

        // Jika transaksi sudah berstatus completed sebelumnya, abaikan (Idempotent)
=======
            throw new Exception("Transaction not found for invoice: " . $invoiceNumber);
        }

        // Jika transaksi sudah selesai sebelumnya, abaikan (idempotent)
>>>>>>> 40bd9949cc4c4fec508e77d1d42567e34980d6a8
        if ($transaction->payment_status === 'completed') {
            return $transaction;
        }

<<<<<<< HEAD
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
=======
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
>>>>>>> 40bd9949cc4c4fec508e77d1d42567e34980d6a8
            ]);
        }

        return $transaction;
    }

    /**
<<<<<<< HEAD
     * 3. Menangani logika upload bukti bayar manual (Fallback jika PG bermasalah / user pilih manual)
     */
    public function uploadReceipt(string $referenceId, $file): Transaction
    {
        $transaction = Transaction::where("reference_id", $referenceId)->first();
=======
     * Simpan bukti transfer manual untuk sebuah invoice yang masih pending.
     */
    public function uploadReceipt(string $referenceId, UploadedFile $file): Transaction
    {
        $transaction = Transaction::where('reference_id', $referenceId)->first();
>>>>>>> 40bd9949cc4c4fec508e77d1d42567e34980d6a8

        if (!$transaction) {
            throw new Exception("Invoice not found.", 404);
        }

<<<<<<< HEAD
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
=======
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
>>>>>>> 40bd9949cc4c4fec508e77d1d42567e34980d6a8
    }
}
