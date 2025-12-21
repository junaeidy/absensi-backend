<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nik', 20)->unique()->nullable()->comment('NIK KTP');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->string('education_level')->nullable();
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('nik');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
