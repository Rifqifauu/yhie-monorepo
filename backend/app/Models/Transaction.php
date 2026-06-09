<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        "program_registration_id",
        "amount",
        "payment_status",
    ];

    protected $casts = [
        "amount" => "float",
    ];

    public function programRegistration(): BelongsTo
    {
        return $this->belongsTo(ProgramRegistration::class);
    }
}
