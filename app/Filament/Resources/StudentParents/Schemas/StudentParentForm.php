<?php

namespace App\Filament\Resources\StudentParents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StudentParentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->placeholder('Nama lengkap orang tua/wali')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nik')
                    ->label('NIK')
                    ->placeholder('Nomor Induk Kependudukan')
                    ->maxLength(16)
                    ->default(null),
                TextInput::make('phone')
                    ->label('No. HP')
                    ->tel()
                    ->placeholder('08xxxxxxxxxx')
                    ->required()
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->placeholder('contoh@email.com')
                    ->default(null)
                    ->maxLength(255),
                TextInput::make('occupation')
                    ->label('Pekerjaan')
                    ->placeholder('Contoh: Guru, PNS, Wiraswasta, dll')
                    ->default(null)
                    ->maxLength(255),
                Textarea::make('address')
                    ->label('Alamat')
                    ->placeholder('Alamat lengkap tempat tinggal')
                    ->default(null)
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('education_level')
                    ->label('Tingkat Pendidikan')
                    ->options([
                        'SD' => 'SD',
                        'SMP' => 'SMP',
                        'SMA/SMK' => 'SMA/SMK',
                        'D3' => 'D3',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ])
                    ->default(null),
                TextInput::make('monthly_income')
                    ->label('Pendapatan Bulanan')
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('0')
                    ->default(null),
            ]);
    }
}
