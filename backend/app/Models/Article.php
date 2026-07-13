<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        "title_id",
        "title_en",
        "content_id",
        "content_en",
        "image",
        "slug_id",
        "slug_en",
        "is_published",
        "author_id",
        "category_id",
        "category_en",
    ];

    protected $casts = [
        "image" => "array",
        "is_published" => "boolean",
    ];

    public function author()
    {
        return $this->belongsTo(User::class, "author_id");
    }
}
