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
        Schema::create("program_registrations", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("program_id")
                ->constrained("programs")
                ->cascadeOnDelete();
            $table->string("full_name");
            $table->string("email");
            $table->string("phone", 30);
            $table->enum("gender", ["male", "female"]);
            $table->integer("age")->nullable();
            $table->text("address")->nullable();
            $table->text("notes")->nullable();
            $table
                ->enum("status", ["pending", "approved", "rejected"])
                ->default("pending");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("program_registrations");
    }
};
