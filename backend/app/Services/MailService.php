<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Mail\MoodleAccountMail;
use App\Models\ProgramRegistration;
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

    /**
     * Kirim akun Moodle (username & password) ke pendaftar yang baru disetujui.
     * Password tidak pernah dicatat ke log demi keamanan.
     */
    public function sendMoodleAccount(
        ProgramRegistration $registration,
        string $username,
        string $password,
    ): void {
        if (!$registration->email) {
            return;
        }

        try {
            Mail::to($registration->email)->send(
                new MoodleAccountMail($registration, $username, $password),
            );
        } catch (\Throwable $e) {
            Log::error("Error sending Moodle account email: " . $e->getMessage());
        }
    }
}
