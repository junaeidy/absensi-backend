<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Mata Pelajaran')
                    ->placeholder('Contoh: MAT, IPA, IPS, dll')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
                TextInput::make('name')
                    ->label('Nama Mata Pelajaran')
                    ->placeholder('Contoh: Matematika, Bahasa Indonesia, dll')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Keterangan tambahan tentang mata pelajaran')
                    ->default(null)
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->helperText('Nonaktifkan jika mata pelajaran tidak lagi diajarkan')
                    ->default(true)
                    ->required(),
            ]);
    }
}
