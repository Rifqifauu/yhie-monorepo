<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        "program_registration_id",
        "reference_id", // Wajib diisi (Nomor Invoice unik, misal: INV-202606-0001)
        "amount",
        "payment_status", // pending, completed, failed, expired
        "payment_method", // Sekarang diisi 'manual_transfer', besok otomatis dari PG (e.g., 'gopay')
        "transaction_receipt", // Tempat nyimpan nama file foto bukti transfer manual
        "pg_transaction_id", // Kosongkan dulu (null), besok diisi ID dari Midtrans/Xendit
    ];

    protected $casts = [
        "amount" => "float",
    ];

    /**
     * Relasi balik ke ProgramRegistration
     */
    public function programRegistration(): BelongsTo
    {
        return $this->belongsTo(ProgramRegistration::class);
    }
}
