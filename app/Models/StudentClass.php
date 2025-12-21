<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;

    protected $table = 'student_classes';

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'status',           // active, graduated, moved
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
