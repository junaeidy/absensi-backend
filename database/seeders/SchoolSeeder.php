<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\School::updateOrCreate(
            ['email' => 'info@smpn1jakarta.sch.id'],
            [
                'name' => 'SMP Negeri 1 Jakarta',
                'email' => 'info@smpn1jakarta.sch.id',
                'address' => 'Jl. Pendidikan No. 123, Jakarta Pusat, DKI Jakarta',
                'latitude' => '-6.200000',
                'longitude' => '106.816666',
                'radius_km' => '0.5',
                'attendance_type' => 'location_based_only',
            ]
        );
    }
}
