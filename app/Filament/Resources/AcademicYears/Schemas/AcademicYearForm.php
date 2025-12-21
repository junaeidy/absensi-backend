<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tahun Ajaran')
                    ->placeholder('Contoh: 2024/2025')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->after('start_date'),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->helperText('Hanya satu tahun ajaran yang dapat aktif pada satu waktu')
                    ->default(false)
                    ->required(),
            ]);
    }
}
