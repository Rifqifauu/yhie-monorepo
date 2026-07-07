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
        Schema::table('program_registrations', function (Blueprint $table) {
            // Nullable = belum diprovisikan ke Moodle. Diisi setelah core_user_create_users
            // sukses, dipakai juga untuk cegah pembuatan akun Moodle dobel.
            $table->unsignedBigInteger('moodle_user_id')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_registrations', function (Blueprint $table) {
            $table->dropColumn('moodle_user_id');
        });
    }
};
