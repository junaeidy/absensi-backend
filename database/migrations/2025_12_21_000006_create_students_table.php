<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()
                ->comment('Link to users table for login capability');
            $table->string('nis', 20)->unique()->comment('Nomor Induk Siswa');
            $table->string('nisn', 20)->unique()->nullable()->comment('NISN');
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->enum('gender', ['L', 'P'])->comment('L=Laki-laki, P=Perempuan');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('status', ['active', 'alumni', 'moved', 'dropped_out'])
                  ->default('active');
            $table->date('admission_date')->nullable();
            $table->date('graduation_date')->nullable();
            $table->string('previous_school')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('nis');
            $table->index('nisn');
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
