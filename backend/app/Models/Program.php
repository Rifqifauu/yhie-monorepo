<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        "title_id",
        "description_id",
        "title_en",
        "description_en",
        "image_path",
        "price_id",
        "price_en",
        "slug_id",
        "slug_en",
    ];

    protected $casts = [
        "price_id" => "float",
        "price_en" => "float",
    ];

    public function registrations()
    {
        return $this->hasMany(ProgramRegistration::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
