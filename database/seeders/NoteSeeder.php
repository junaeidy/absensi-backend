<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = \App\Models\User::where('email', 'admin@admin.com')->first();

        if (! $adminUser) {
            return;
        }

        $notes = [
            [
                'user_id' => $adminUser->id,
                'title' => 'Selamat datang di Sistem Kami',
                'note' => 'Ini adalah catatan sambutan untuk semua pengguna baru. Harap baca buku panduan dan ikuti kebijakan perusahaan.',
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Jadwal Rapat',
                'note' => 'Rapat guru bulanan dijadwalkan setiap Senin pertama setiap bulan pukul 10:00 WIB.',
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Pemberitahuan Libur',
                'note' => 'Harap dicatat bahwa sekolah akan tutup pada hari libur nasional. Periksa kalender libur untuk detailnya.',
            ],
        ];

        foreach ($notes as $note) {
            \App\Models\Note::updateOrCreate(
                [
                    'user_id' => $note['user_id'],
                    'title' => $note['title'],
                ],
                $note
            );
        }
    }
}
