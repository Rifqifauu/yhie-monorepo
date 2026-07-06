<?php

namespace App\Services;

use App\Models\ProgramRegistration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MoodleService
{
    /**
     * Buat akun user di Moodle via core_user_create_users.
     * Password digenerate di sini (Moodle tidak pernah mengembalikan password),
     * lalu dikirim ke Moodle saat pembuatan supaya bisa dipakai lagi untuk email.
     *
     * @return array{moodle_user_id: int, username: string, password: string}
     */
    public function createUser(ProgramRegistration $registration): array
    {
        $password = Str::password(12);
        [$firstName, $lastName] = $this->splitName($registration->full_name);

        $response = Http::asForm()->post(
            rtrim(config("services.moodle.url"), "/") . "/webservice/rest/server.php",
            [
                "wstoken" => config("services.moodle.token"),
                "wsfunction" => "core_user_create_users",
                "moodlewsrestformat" => "json",
                "users" => [
                    [
                        "username" => $this->sanitizeUsername($registration->email),
                        "password" => $password,
                        "firstname" => $firstName,
                        "lastname" => $lastName,
                        "email" => $registration->email,
                        "city" => "Indonesia",
                        "country" => "ID",
                    ],
                ],
            ],
        );

        $data = $response->json();

        if (!$response->successful() || isset($data["exception"])) {
            throw new \RuntimeException(
                $data["message"] ?? "Gagal membuat akun Moodle.",
            );
        }

        return [
            "moodle_user_id" => $data[0]["id"],
            "username" => $data[0]["username"],
            "password" => $password,
        ];
    }

    /**
     * Moodle wajib firstname & lastname terpisah, form pendaftaran kita cuma
     * punya full_name - split sederhana, kata pertama jadi firstname.
     */
    private function splitName(string $fullName): array
    {
        $parts = explode(" ", trim($fullName), 2);
        return [$parts[0], $parts[1] ?? $parts[0]];
    }

    /**
     * Default Moodle cuma terima huruf, angka, titik, strip, underscore, dan @
     * di username - email dengan karakter lain (mis. alias "+") harus dibuang
     * dulu supaya tidak ditolak Moodle sebagai "invalid parameter value".
     */
    private function sanitizeUsername(string $email): string
    {
        return preg_replace("/[^a-z0-9._@-]/", "", strtolower($email));
    }
}
