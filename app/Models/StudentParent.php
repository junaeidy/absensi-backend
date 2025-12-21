<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nik',              // NIK KTP
        'phone',
        'email',
        'occupation',       // Pekerjaan
        'address',
        'education_level',  // Pendidikan terakhir
        'monthly_income',   // Penghasilan per bulan (nullable)
    ];

    protected function casts(): array
    {
        return [
            'monthly_income' => 'decimal:2',
        ];
    }

    // Relasi many-to-many dengan students
    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'student_parent_relations',
            'parent_id',
            'student_id'
        )->withPivot('relation_type', 'is_primary_contact')
         ->withTimestamps();
    }
}
