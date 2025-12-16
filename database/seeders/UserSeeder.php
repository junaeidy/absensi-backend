<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\ShiftKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shiftIds = ShiftKerja::pluck('id', 'name');
        $departemenIds = Departemen::pluck('id', 'name');
        $jabatanIds = Jabatan::pluck('id', 'name');

        $users = [
            [
                'name' => 'Admin Sekolah',
                'email' => 'admin@sekolah.com',
                'role' => 'admin',
                'position' => 'System Administrator',
                'department' => 'Teknologi Informasi',
                'departemen_id' => $departemenIds['Teknologi Informasi'] ?? null,
                'jabatan_id' => $jabatanIds['Staff IT'] ?? null,
                'shift_name' => 'Shift Pagi',
                'phone' => '+6281234567890',
            ],
            [
                'name' => 'Dr. Suharto, M.Pd',
                'email' => 'kepsek@sekolah.com',
                'role' => 'kepala_sekolah',
                'position' => 'Kepala Sekolah',
                'department' => 'Kepala Sekolah',
                'departemen_id' => $departemenIds['Kepala Sekolah'] ?? null,
                'jabatan_id' => $jabatanIds['Kepala Sekolah'] ?? null,
                'shift_name' => 'Shift Pagi',
                'phone' => '+6281234567891',
            ],
            [
                'name' => 'Budi Santoso, S.Pd',
                'email' => 'budi@sekolah.com',
                'role' => 'guru',
                'position' => 'Guru Matematika',
                'department' => 'Kurikulum',
                'departemen_id' => $departemenIds['Kurikulum'] ?? null,
                'jabatan_id' => $jabatanIds['Guru Mata Pelajaran'] ?? null,
                'shift_name' => 'Shift Pagi',
                'phone' => '+6281234567892',
            ],
            [
                'name' => 'Siti Nurhaliza, S.E',
                'email' => 'siti@sekolah.com',
                'role' => 'staff_keuangan',
                'position' => 'Staff Keuangan',
                'department' => 'Keuangan',
                'departemen_id' => $departemenIds['Keuangan'] ?? null,
                'jabatan_id' => $jabatanIds['Staff Keuangan'] ?? null,
                'shift_name' => 'Shift Flexible',
                'phone' => '+6281234567893',
            ],
            [
                'name' => 'Ahmad Fauzi, S.Pd',
                'email' => 'ahmad@sekolah.com',
                'role' => 'guru',
                'position' => 'Guru Bahasa Indonesia',
                'department' => 'Kurikulum',
                'departemen_id' => $departemenIds['Kurikulum'] ?? null,
                'jabatan_id' => $jabatanIds['Guru Mata Pelajaran'] ?? null,
                'shift_name' => 'Shift Siang',
                'phone' => '+6281234567894',
            ],
            [
                'name' => 'Dewi Kartika, S.IP',
                'email' => 'dewi@sekolah.com',
                'role' => 'staff_perpustakaan',
                'position' => 'Staff Perpustakaan',
                'department' => 'Perpustakaan',
                'departemen_id' => $departemenIds['Perpustakaan'] ?? null,
                'jabatan_id' => $jabatanIds['Staff Perpustakaan'] ?? null,
                'shift_name' => 'Shift Pagi',
                'phone' => '+6281234567895',
            ],
            [
                'name' => 'Rina Susanti, S.Psi',
                'email' => 'rina@sekolah.com',
                'role' => 'bk',
                'position' => 'Guru BK',
                'department' => 'Bimbingan Konseling',
                'departemen_id' => $departemenIds['Bimbingan Konseling'] ?? null,
                'jabatan_id' => $jabatanIds['Guru BK'] ?? null,
                'shift_name' => 'Shift Malam',
                'phone' => '+6281234567896',
            ],
            [
                'name' => 'Maria Clara, S.Pd',
                'email' => 'maria@sekolah.com',
                'role' => 'guru',
                'position' => 'Guru Bahasa Inggris',
                'department' => 'Kurikulum',
                'departemen_id' => $departemenIds['Kurikulum'] ?? null,
                'jabatan_id' => $jabatanIds['Guru Mata Pelajaran'] ?? null,
                'shift_name' => 'Shift Flexible',
                'phone' => '+6281234567897',
            ],
        ];

        foreach ($users as $userData) {
            $shiftId = $shiftIds->get($userData['shift_name']) ?? $shiftIds->first();

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'phone' => $userData['phone'],
                    'role' => $userData['role'],
                    'position' => $userData['position'],
                    'department' => $userData['department'],
                    'departemen_id' => $userData['departemen_id'],
                    'jabatan_id' => $userData['jabatan_id'],
                    'shift_kerja_id' => $shiftId,
                ]
            );
        }
    }
}
