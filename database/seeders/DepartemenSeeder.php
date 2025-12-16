<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemens = [
            ['name' => 'Kepala Sekolah', 'description' => 'Departemen pimpinan sekolah'],
            ['name' => 'Kurikulum', 'description' => 'Departemen pengembangan kurikulum dan pembelajaran'],
            ['name' => 'Kesiswaan', 'description' => 'Departemen pengelolaan siswa dan kegiatan ekstrakurikuler'],
            ['name' => 'Tata Usaha', 'description' => 'Departemen administrasi dan tata usaha sekolah'],
            ['name' => 'Keuangan', 'description' => 'Departemen pengelolaan keuangan sekolah'],
            ['name' => 'Perpustakaan', 'description' => 'Departemen pengelolaan perpustakaan dan literasi'],
            ['name' => 'Bimbingan Konseling', 'description' => 'Departemen bimbingan dan konseling siswa'],
            ['name' => 'Teknologi Informasi', 'description' => 'Departemen pengelolaan sistem dan teknologi informasi'],
        ];

        foreach ($departemens as $departemen) {
            \App\Models\Departemen::updateOrCreate(
                ['name' => $departemen['name']],
                $departemen
            );
        }

        $this->command->info('8 departemens created/updated successfully.');
    }
}
