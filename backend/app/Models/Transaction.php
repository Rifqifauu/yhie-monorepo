<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        "program_registration_id",
        "reference_id",
        "amount",
        "payment_status",
        "payment_method",
        "transaction_receipt",
        "pg_transaction_id",
        "payment_url",
        "paid_at",
        "pg_response"
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
