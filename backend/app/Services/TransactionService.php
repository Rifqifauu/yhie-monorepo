<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\ProgramRegistration;
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

    // ... method uploadReceipt() yang tadi tetap di sini ...
}
