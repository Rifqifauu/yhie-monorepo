<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        "program_registration_id",
        "program_id",
        "external_user_id", // ID akun peserta di Moodle/SSO - nullable, belum final mekanismenya
        "certificate_number",
        "issued_at",
        "file_path",
    ];

    protected $casts = [
        "issued_at" => "date",
    ];

    public function programRegistration(): BelongsTo
    {
        return $this->belongsTo(ProgramRegistration::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
