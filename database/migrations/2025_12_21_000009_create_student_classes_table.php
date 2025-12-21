<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')
                  ->references('id')
                  ->on('classes')
                  ->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'graduated', 'moved'])->default('active');
            $table->timestamps();

            // Unique: 1 siswa tidak boleh ada di 2 kelas dalam 1 tahun ajaran yang sama
            $table->unique(['student_id', 'academic_year_id']);
            
            // Index
            $table->index(['student_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_classes');
    }
};
