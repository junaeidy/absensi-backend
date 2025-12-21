<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained()->cascadeOnDelete();
            $table->string('name', 20)->comment('7A, 7B, 8A, dst');
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->integer('capacity')->default(30);
            $table->string('room')->nullable();
            $table->timestamps();

            // Unique: tidak boleh ada 2 kelas 7A di tahun ajaran yang sama
            $table->unique(['name', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
