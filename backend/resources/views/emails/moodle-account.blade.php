<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f5; padding: 24px; margin: 0;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e4e4e7;">
        <div style="background-color: #065f46; padding: 24px; text-align: center;">
            <h1 style="color: #ffffff; font-size: 18px; margin: 0;">Yayasan Hafiz Indonesia Emas</h1>
        </div>
        <div style="padding: 24px;">
            <p style="margin: 0 0 16px;">Assalamu'alaikum, {{ $fullName ?? 'Peserta' }}</p>
            <p style="margin: 0 0 16px; color: #52525b;">
                Selamat! Pendaftaran Anda sudah disetujui. Berikut akun kelas online (Moodle) untuk mengikuti program:
            </p>

            <div style="background: #f4f4f5; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: #71717a;">Username</p>
                <p style="margin: 0 0 16px; font-weight: bold; font-family: monospace;">{{ $username }}</p>

                <p style="margin: 0 0 8px; font-size: 13px; color: #71717a;">Password</p>
                <p style="margin: 0; font-weight: bold; font-family: monospace;">{{ $password }}</p>
            </div>

            <a href="{{ $moodleUrl }}" style="display: block; text-align: center; background-color: #059669; color: #ffffff; text-decoration: none; padding: 12px; border-radius: 8px; font-weight: bold;">
                Masuk ke Kelas Online
            </a>

            <p style="margin: 24px 0 0; font-size: 12px; color: #a1a1aa; text-align: center;">
                Segera ganti password setelah login pertama kali. Email ini dikirim otomatis, mohon tidak membalas ke email ini.
            </p>
        </div>
    </div>
</body>
</html>
