<?php

namespace App\Filament\Resources\Grades\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tingkat')
                    ->placeholder('Contoh: Kelas 10, Kelas 11, dll')
                    ->required(),
                TextInput::make('level')
                    ->label('Level')
                    ->placeholder('Contoh: 10, 11, 12')
                    ->helperText('Masukkan angka untuk level tingkat')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Keterangan tambahan tentang tingkat ini')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
