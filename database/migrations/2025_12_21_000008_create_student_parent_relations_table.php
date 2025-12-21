<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_parent_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')
                  ->references('id')
                  ->on('student_parents')
                  ->cascadeOnDelete();
            $table->enum('relation_type', ['ayah', 'ibu', 'wali'])
                  ->comment('Tipe hubungan');
            $table->boolean('is_primary_contact')->default(false)
                  ->comment('Kontak utama untuk notifikasi');
            $table->timestamps();

            // Unique constraint: 1 siswa tidak boleh punya 2 ayah
            $table->unique(['student_id', 'relation_type']);
            
            // Index
            $table->index(['student_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_parent_relations');
    }
};
