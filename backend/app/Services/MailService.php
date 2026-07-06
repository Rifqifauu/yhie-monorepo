<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Kirim email invoice ke pendaftar. Dibungkus try/catch supaya kegagalan
     * kirim email (mis. SMTP belum dikonfigurasi) tidak menggagalkan pendaftaran.
     */
    public function sendInvoice(Transaction $transaction): void
    {
        $transaction->loadMissing("programRegistration.program");
        $email = $transaction->programRegistration?->email;

        if (!$email) {
            return;
        }

        try {
            Mail::to($email)->send(new InvoiceMail($transaction));
        } catch (\Throwable $e) {
            Log::error("Error sending invoice email: " . $e->getMessage());
        }
    }
}
