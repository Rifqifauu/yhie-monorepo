<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Transaction $transaction)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice Pendaftaran - {$this->transaction->reference_id}",
        );
    }

    public function content(): Content
    {
        $registration = $this->transaction->programRegistration;

        return new Content(
            view: "emails.invoice",
            with: [
                "fullName" => $registration?->full_name,
                "programTitle" => $registration?->program?->title_id,
                "referenceId" => $this->transaction->reference_id,
                "amount" => $this->transaction->amount,
                "invoiceUrl" => rtrim(config("app.frontend_url"), "/") .
                    "/invoice/{$this->transaction->reference_id}",
                "bankName" => Setting::get("bank_name", "-"),
                "bankAccountNumber" => Setting::get("bank_account_number", "-"),
                "bankAccountName" => Setting::get("bank_account_name", "-"),
            ],
        );
    }
}
