<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        if (!$academicYear) {
            $this->command->error('❌ No active academic year found. Run AcademicYearSeeder first.');
            return;
        }

        // Tingkat kelas untuk SMP (ubah jika SMA: 10, 11, 12)
        $grades = [
            ['name' => '7', 'level' => 1, 'description' => 'Kelas 7 (Tingkat 1 SMP)'],
            ['name' => '8', 'level' => 2, 'description' => 'Kelas 8 (Tingkat 2 SMP)'],
            ['name' => '9', 'level' => 3, 'description' => 'Kelas 9 (Tingkat 3 SMP)'],
        ];

        foreach ($grades as $gradeData) {
            $grade = Grade::create($gradeData);
            $this->command->info("  → Grade {$gradeData['name']} created");

            // Buat kelas A, B, C untuk setiap tingkat
            foreach (['A', 'B', 'C'] as $className) {
                $fullName = $gradeData['name'] . $className;
                ClassModel::create([
                    'grade_id' => $grade->id,
                    'name' => $fullName,
                    'academic_year_id' => $academicYear->id,
                    'capacity' => 30,
                    'room' => "R-{$fullName}",
                ]);
                $this->command->info("    → Class {$fullName} created (Room R-{$fullName})");
            }
        }

        $this->command->info('✅ Grades & Classes seeded successfully!');
        $this->command->info('   Total: ' . Grade::count() . ' grades, ' . ClassModel::count() . ' classes');
    }
}
