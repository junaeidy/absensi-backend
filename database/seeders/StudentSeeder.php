<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentClass;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $class7A = ClassModel::where('name', '7A')->first();
        $class7B = ClassModel::where('name', '7B')->first();
        $class8A = ClassModel::where('name', '8A')->first();

        if (!$academicYear || !$class7A) {
            $this->command->error('❌ Academic year or classes not found. Run AcademicYearSeeder and GradeSeeder first.');
            return;
        }

        // ========================================
        // STUDENT 1: Ahmad Rizki (Kelas 7A)
        // ========================================
        $this->command->info('Creating Student 1: Ahmad Rizki...');
        
        // Create user account for login
        $user1 = User::create([
            'name' => 'Ahmad Rizki',
            'email' => 'ahmad.rizki@student.school.id',
            'password' => Hash::make('password123'),
            'phone' => '08123456789',
            'role' => 'siswa',
            'image_url' => null,
        ]);

        $student1 = Student::create([
            'user_id' => $user1->id,  // Link ke user untuk login
            'nis' => '2024001',
            'nisn' => '0123456789001',
            'name' => 'Ahmad Rizki',
            'nickname' => 'Rizki',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-15',
            'religion' => 'Islam',
            'address' => 'Jl. Merdeka No. 123, Jakarta',
            'phone' => '08123456789',
            'email' => 'ahmad.rizki@student.school.id',
            'status' => 'active',
            'admission_date' => '2024-07-15',
        ]);

        // Parents for Student 1
        $parent1 = StudentParent::create([
            'name' => 'Budi Santoso',
            'nik' => '3174011234567890',
            'phone' => '08121234567',
            'email' => 'budi.santoso@email.com',
            'occupation' => 'PNS',
            'address' => 'Jl. Merdeka No. 123, Jakarta',
            'education_level' => 'S1',
            'monthly_income' => 8000000,
        ]);

        $parent2 = StudentParent::create([
            'name' => 'Siti Aminah',
            'nik' => '3174019876543210',
            'phone' => '08129876543',
            'email' => 'siti.aminah@email.com',
            'occupation' => 'Guru',
            'address' => 'Jl. Merdeka No. 123, Jakarta',
            'education_level' => 'S1',
            'monthly_income' => 6000000,
        ]);

        // Attach parents
        $student1->parents()->attach($parent1->id, [
            'relation_type' => 'ayah',
            'is_primary_contact' => true,
        ]);
        $student1->parents()->attach($parent2->id, [
            'relation_type' => 'ibu',
            'is_primary_contact' => false,
        ]);

        // Assign to class
        StudentClass::create([
            'student_id' => $student1->id,
            'class_id' => $class7A->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
        ]);

        $this->command->info("  ✅ Ahmad Rizki - NIS: 2024001 - Login: ahmad.rizki@student.school.id");

        // ========================================
        // STUDENT 2: Putri Maharani (Kelas 7A)
        // ========================================
        $this->command->info('Creating Student 2: Putri Maharani...');
        
        $user2 = User::create([
            'name' => 'Putri Maharani',
            'email' => 'putri.maharani@student.school.id',
            'password' => Hash::make('password123'),
            'phone' => '08567891234',
            'role' => 'siswa',
        ]);

        $student2 = Student::create([
            'user_id' => $user2->id,
            'nis' => '2024002',
            'nisn' => '0123456789002',
            'name' => 'Putri Maharani',
            'nickname' => 'Putri',
            'gender' => 'P',
            'birth_place' => 'Bandung',
            'birth_date' => '2010-08-20',
            'religion' => 'Islam',
            'address' => 'Jl. Sudirman No. 456, Bandung',
            'phone' => '08567891234',
            'email' => 'putri.maharani@student.school.id',
            'status' => 'active',
            'admission_date' => '2024-07-15',
        ]);

        $parent3 = StudentParent::create([
            'name' => 'Eko Prasetyo',
            'nik' => '3273011122334455',
            'phone' => '08131122334',
            'email' => 'eko.prasetyo@email.com',
            'occupation' => 'Wiraswasta',
            'address' => 'Jl. Sudirman No. 456, Bandung',
            'education_level' => 'SMA',
            'monthly_income' => 10000000,
        ]);

        $parent4 = StudentParent::create([
            'name' => 'Dewi Sartika',
            'nik' => '3273015544332211',
            'phone' => '08135544332',
            'email' => 'dewi.sartika@email.com',
            'occupation' => 'Ibu Rumah Tangga',
            'address' => 'Jl. Sudirman No. 456, Bandung',
            'education_level' => 'SMA',
            'monthly_income' => 0,
        ]);

        $student2->parents()->attach($parent3->id, [
            'relation_type' => 'ayah',
            'is_primary_contact' => true,
        ]);
        $student2->parents()->attach($parent4->id, [
            'relation_type' => 'ibu',
            'is_primary_contact' => false,
        ]);

        StudentClass::create([
            'student_id' => $student2->id,
            'class_id' => $class7A->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
        ]);

        $this->command->info("  ✅ Putri Maharani - NIS: 2024002 - Login: putri.maharani@student.school.id");

        // ========================================
        // STUDENT 3: Budi Hartono (Kelas 7B)
        // ========================================
        $this->command->info('Creating Student 3: Budi Hartono...');
        
        $user3 = User::create([
            'name' => 'Budi Hartono',
            'email' => 'budi.hartono@student.school.id',
            'password' => Hash::make('password123'),
            'phone' => '08234567890',
            'role' => 'siswa',
        ]);

        $student3 = Student::create([
            'user_id' => $user3->id,
            'nis' => '2024003',
            'nisn' => '0123456789003',
            'name' => 'Budi Hartono',
            'nickname' => 'Budi',
            'gender' => 'L',
            'birth_place' => 'Surabaya',
            'birth_date' => '2010-03-10',
            'religion' => 'Kristen',
            'address' => 'Jl. Ahmad Yani No. 789, Surabaya',
            'phone' => '08234567890',
            'email' => 'budi.hartono@student.school.id',
            'status' => 'active',
            'admission_date' => '2024-07-15',
        ]);

        $parent5 = StudentParent::create([
            'name' => 'Hartono Wijaya',
            'nik' => '3578011234567890',
            'phone' => '08141234567',
            'occupation' => 'Pengusaha',
            'address' => 'Jl. Ahmad Yani No. 789, Surabaya',
            'education_level' => 'S1',
            'monthly_income' => 15000000,
        ]);

        $student3->parents()->attach($parent5->id, [
            'relation_type' => 'ayah',
            'is_primary_contact' => true,
        ]);

        StudentClass::create([
            'student_id' => $student3->id,
            'class_id' => $class7B->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
        ]);

        $this->command->info("  ✅ Budi Hartono - NIS: 2024003 - Login: budi.hartono@student.school.id");

        // ========================================
        // STUDENT 4: Siti Nurhaliza (Kelas 8A) - Siswa Kelas 2
        // ========================================
        $this->command->info('Creating Student 4: Siti Nurhaliza...');
        
        $user4 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@student.school.id',
            'password' => Hash::make('password123'),
            'phone' => '08345678901',
            'role' => 'siswa',
        ]);

        $student4 = Student::create([
            'user_id' => $user4->id,
            'nis' => '2023015',  // NIS dari tahun sebelumnya
            'nisn' => '0123456789015',
            'name' => 'Siti Nurhaliza',
            'nickname' => 'Siti',
            'gender' => 'P',
            'birth_place' => 'Medan',
            'birth_date' => '2009-11-25',
            'religion' => 'Islam',
            'address' => 'Jl. Gatot Subroto No. 321, Medan',
            'phone' => '08345678901',
            'email' => 'siti.nurhaliza@student.school.id',
            'status' => 'active',
            'admission_date' => '2023-07-15',  // Masuk tahun lalu
        ]);

        $parent6 = StudentParent::create([
            'name' => 'Rahman Abdullah',
            'nik' => '1275011234567890',
            'phone' => '08151234567',
            'occupation' => 'Dokter',
            'address' => 'Jl. Gatot Subroto No. 321, Medan',
            'education_level' => 'S2',
            'monthly_income' => 20000000,
        ]);

        $student4->parents()->attach($parent6->id, [
            'relation_type' => 'ayah',
            'is_primary_contact' => true,
        ]);

        StudentClass::create([
            'student_id' => $student4->id,
            'class_id' => $class8A->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'active',
        ]);

        $this->command->info("  ✅ Siti Nurhaliza - NIS: 2023015 - Login: siti.nurhaliza@student.school.id");

        // Summary
        $this->command->info('');
        $this->command->info('✅ Students seeded successfully!');
        $this->command->info('   Total Students: ' . Student::count());
        $this->command->info('   Total Parents: ' . StudentParent::count());
        $this->command->info('   Total Student-Class Relations: ' . StudentClass::count());
        $this->command->info('');
        $this->command->info('📝 Login Credentials (All passwords: password123):');
        $this->command->info('   - ahmad.rizki@student.school.id');
        $this->command->info('   - putri.maharani@student.school.id');
        $this->command->info('   - budi.hartono@student.school.id');
        $this->command->info('   - siti.nurhaliza@student.school.id');
    }
}
