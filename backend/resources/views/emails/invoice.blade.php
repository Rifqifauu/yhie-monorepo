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
            <p style="margin: 0 0 16px;">Assalamu'alaikum, {{ $fullName ?? 'Calon Peserta' }}</p>
            <p style="margin: 0 0 16px; color: #52525b;">
                Terima kasih telah mendaftar program <strong>{{ $programTitle ?? '-' }}</strong>. Berikut detail invoice Anda:
            </p>

            <div style="background: #f4f4f5; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: #71717a;">Nomor Invoice</p>
                <p style="margin: 0 0 16px; font-weight: bold; font-family: monospace;">{{ $referenceId }}</p>

                <p style="margin: 0 0 8px; font-size: 13px; color: #71717a;">Total Tagihan</p>
                <p style="margin: 0; font-weight: bold; font-size: 18px; color: #065f46;">
                    Rp {{ number_format($amount, 0, ',', '.') }}
                </p>
            </div>

            <p style="margin: 0 0 8px; color: #52525b;">Silakan transfer ke rekening berikut:</p>
            <div style="background: #f4f4f5; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <p style="margin: 0; font-weight: bold;">{{ $bankName }}</p>
                <p style="margin: 4px 0; font-family: monospace; font-size: 16px;">{{ $bankAccountNumber }}</p>
                <p style="margin: 0; font-size: 13px; color: #71717a;">a.n. {{ $bankAccountName }}</p>
            </div>

            <a href="{{ $invoiceUrl }}" style="display: block; text-align: center; background-color: #059669; color: #ffffff; text-decoration: none; padding: 12px; border-radius: 8px; font-weight: bold;">
                Lihat &amp; Upload Bukti Transfer
            </a>

            <p style="margin: 24px 0 0; font-size: 12px; color: #a1a1aa; text-align: center;">
                Email ini dikirim otomatis, mohon tidak membalas ke email ini.
            </p>
        </div>
    </div>
</body>
</html>
