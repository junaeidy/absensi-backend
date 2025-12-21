<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',           // 2024/2025
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    // Scope untuk get tahun ajaran aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
