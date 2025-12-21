<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',          // Link to users table for login
        'nis',              // Nomor Induk Siswa (sekolah)
        'nisn',             // Nomor Induk Siswa Nasional
        'name',
        'nickname',
        'gender',           // L/P
        'birth_place',
        'birth_date',
        'religion',
        'address',
        'phone',
        'email',
        'photo_url',
        'status',           // active, alumni, moved, dropped_out
        'admission_date',   // Tanggal masuk
        'graduation_date',  // Tanggal lulus (nullable)
        'previous_school',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'admission_date' => 'date',
            'graduation_date' => 'date',
        ];
    }

    // Relasi ke User untuk login
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi many-to-many dengan parents
    public function parents()
    {
        return $this->belongsToMany(
            StudentParent::class,
            'student_parent_relations',
            'student_id',
            'parent_id'
        )->withPivot('relation_type', 'is_primary_contact')
         ->withTimestamps();
    }

    // Helper untuk get primary contact
    public function primaryContact()
    {
        return $this->parents()
            ->wherePivot('is_primary_contact', true)
            ->first();
    }

    // Relasi dengan classes
    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class);
    }

    // Get current active class
    public function currentClass()
    {
        return $this->studentClasses()
            ->whereHas('class.academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->first();
    }
}
