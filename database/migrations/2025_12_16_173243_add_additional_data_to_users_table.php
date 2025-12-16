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
        Schema::table('users', function (Blueprint $table) {
            // JSON field untuk menyimpan data tambahan spesifik per role
            // Contoh untuk guru: nip, golongan, mata_pelajaran, dll
            // Contoh untuk kepala_sekolah: nip, golongan, masa_jabatan, dll
            $table->json('additional_data')->nullable()->after('image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('additional_data');
        });
    }
};
