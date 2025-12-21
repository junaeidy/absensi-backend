<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',         // 7, 8, 9 (SMP) atau 10, 11, 12 (SMA)
        'level',        // Tingkat
        'description',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
        ];
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }
}
