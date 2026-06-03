<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'title_id',
        'title_en',
        'description_id',
        'description_en',
        'image',
        'slug_id',
        'slug_en',
        'category',
    ];

    protected $casts = [
        'image' => 'array',
    ];
}
