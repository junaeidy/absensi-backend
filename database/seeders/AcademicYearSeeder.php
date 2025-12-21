<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // Tahun Ajaran 2024/2025
        $ay2024 = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-15',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        // Semester untuk 2024/2025
        Semester::create([
            'academic_year_id' => $ay2024->id,
            'name' => 'Ganjil',
            'start_date' => '2024-07-15',
            'end_date' => '2024-12-31',
            'is_active' => false, // Sudah lewat
        ]);

        Semester::create([
            'academic_year_id' => $ay2024->id,
            'name' => 'Genap',
            'start_date' => '2025-01-06',
            'end_date' => '2025-06-30',
            'is_active' => true, // Semester aktif saat ini
        ]);

        // Tahun Ajaran 2025/2026 (upcoming)
        $ay2025 = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-15',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        Semester::create([
            'academic_year_id' => $ay2025->id,
            'name' => 'Ganjil',
            'start_date' => '2025-07-15',
            'end_date' => '2025-12-31',
            'is_active' => false,
        ]);

        Semester::create([
            'academic_year_id' => $ay2025->id,
            'name' => 'Genap',
            'start_date' => '2026-01-06',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        $this->command->info('✅ Academic Years & Semesters seeded successfully!');
    }
}
