<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("program_registration_id")
                ->constrained("program_registrations")
                ->cascadeOnDelete();
            $table
                ->foreignId("program_id")
                ->constrained("programs")
                ->cascadeOnDelete();
            // ID akun peserta di sistem eksternal (Moodle / provider SSO - belum final).
            // Sengaja tanpa FK constraint karena identitasnya hidup di database lain.
            $table->unsignedBigInteger("external_user_id")->nullable();
            $table->string("certificate_number")->unique();
            $table->date("issued_at");
            $table->string("file_path")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
