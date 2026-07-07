<?php

namespace App\Mail;

use App\Models\ProgramRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MoodleAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProgramRegistration $registration,
        public string $username,
        public string $password,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Akun Kelas Online (Moodle) Anda Sudah Siap",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: "emails.moodle-account",
            with: [
                "fullName" => $this->registration->full_name,
                "username" => $this->username,
                "password" => $this->password,
                "moodleUrl" => config("services.moodle.url"),
            ],
        );
    }
}
