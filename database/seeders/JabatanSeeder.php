<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            ['name' => 'Kepala Sekolah', 'description' => 'Pemimpin tertinggi sekolah'],
            ['name' => 'Wakil Kepala Sekolah', 'description' => 'Wakil kepala sekolah'],
            ['name' => 'Guru Mata Pelajaran', 'description' => 'Guru pengajar mata pelajaran'],
            ['name' => 'Guru BK', 'description' => 'Guru Bimbingan Konseling'],
            ['name' => 'Kepala TU', 'description' => 'Kepala Tata Usaha'],
            ['name' => 'Staff TU', 'description' => 'Staff Tata Usaha'],
            ['name' => 'Staff Keuangan', 'description' => 'Mengelola keuangan sekolah'],
            ['name' => 'Staff Perpustakaan', 'description' => 'Mengelola perpustakaan'],
            ['name' => 'Staff IT', 'description' => 'Mengelola sistem teknologi informasi'],
            ['name' => 'Admin', 'description' => 'Administrator sistem'],
        ];

        foreach ($jabatans as $jabatan) {
            \App\Models\Jabatan::updateOrCreate(
                ['name' => $jabatan['name']],
                $jabatan
            );
        }

        $this->command->info('10 jabatans created/updated successfully.');
    }
}
