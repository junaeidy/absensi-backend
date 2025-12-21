<?php

namespace App\Filament\Resources\ClassModels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Grade;
use App\Models\AcademicYear;

class ClassModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grade_id')
                    ->label('Tingkat')
                    ->options(Grade::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->placeholder('Contoh: A, B, C, IPA 1, IPS 2, dll')
                    ->required(),
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(AcademicYear::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('capacity')
                    ->label('Kapasitas')
                    ->placeholder('Jumlah maksimal siswa')
                    ->required()
                    ->numeric()
                    ->default(30)
                    ->minValue(1)
                    ->suffix('siswa'),
                TextInput::make('room')
                    ->label('Ruangan')
                    ->placeholder('Contoh: Ruang 101, Lab Komputer, dll')
                    ->default(null),
            ]);
    }
}
