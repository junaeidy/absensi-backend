<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['code' => 'MTK', 'name' => 'Matematika', 'description' => 'Pelajaran Matematika', 'is_active' => true],
            ['code' => 'IPA', 'name' => 'IPA (Ilmu Pengetahuan Alam)', 'description' => 'Fisika, Kimia, Biologi', 'is_active' => true],
            ['code' => 'IPS', 'name' => 'IPS (Ilmu Pengetahuan Sosial)', 'description' => 'Sejarah, Geografi, Ekonomi', 'is_active' => true],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'description' => 'Pelajaran Bahasa Inggris', 'is_active' => true],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia', 'description' => 'Pelajaran Bahasa Indonesia', 'is_active' => true],
            ['code' => 'PENJAS', 'name' => 'Pendidikan Jasmani', 'description' => 'Olahraga dan Kesehatan', 'is_active' => true],
            ['code' => 'SENI', 'name' => 'Seni Budaya', 'description' => 'Seni Musik, Tari, Rupa', 'is_active' => true],
            ['code' => 'PKWU', 'name' => 'Prakarya', 'description' => 'Prakarya dan Kewirausahaan', 'is_active' => true],
            ['code' => 'AGAMA', 'name' => 'Pendidikan Agama', 'description' => 'Pendidikan Agama Islam/Kristen/Katolik/Hindu/Buddha/Konghucu', 'is_active' => true],
            ['code' => 'PKN', 'name' => 'PKN (Pendidikan Kewarganegaraan)', 'description' => 'Pendidikan Kewarganegaraan', 'is_active' => true],
            ['code' => 'BK', 'name' => 'Bimbingan Konseling', 'description' => 'Bimbingan dan Konseling', 'is_active' => true],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
            $this->command->info("  → Subject: {$subject['code']} - {$subject['name']}");
        }

        $this->command->info('✅ Subjects seeded successfully!');
        $this->command->info('   Total: ' . Subject::count() . ' subjects');
    }
}
