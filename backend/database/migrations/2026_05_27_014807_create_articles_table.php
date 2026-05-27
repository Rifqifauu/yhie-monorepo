<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("articles", function (Blueprint $table) {
            $table->id();
            $table->string("title_id");
            $table->string("title_en");
            $table->text("content_id");
            $table->text("content_en");
            $table->json("image");
            $table->string("slug_id");
            $table->string("slug_en");
            $table->boolean("is_published")->default(false);
            $table->foreignId("author_id")->constrained("users");
            $table->string("category");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("articles");
    }
};
