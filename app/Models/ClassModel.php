<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'grade_id',
        'name',             // 7A, 7B, 8A, dst
        'academic_year_id',
        'capacity',         // Kapasitas max siswa
        'room',             // Ruangan
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class, 'class_id');
    }

    // Relasi dengan wali kelas (FASE 2)
    public function homeroomTeacher()
    {
        return $this->hasOne(ClassTeacher::class);
    }

    // Full name: Grade + Name (e.g., "Kelas 7A")
    public function getFullNameAttribute()
    {
        return "Kelas {$this->name}";
    }

    // Get current students count
    public function getCurrentStudentsCount()
    {
        return $this->studentClasses()->count();
    }

    // Check if class is full
    public function isFull()
    {
        return $this->getCurrentStudentsCount() >= $this->capacity;
    }
}
