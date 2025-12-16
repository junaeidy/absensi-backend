<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'kepala_sekolah' => 'Kepala Sekolah',
                        'guru' => 'Guru',
                        'staff_keuangan' => 'Staff Keuangan',
                        'staff_perpustakaan' => 'Staff Perpustakaan',
                        'bk' => 'BK / Konselor',
                    ])
                    ->required()
                    ->default('guru'),
                Select::make('jabatan_id')
                    ->label('Jabatan')
                    ->relationship('jabatan', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Pilih 1 jabatan untuk guru/staff'),
                Select::make('departemen_id')
                    ->label('Departemen')
                    ->relationship('departemen', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Pilih 1 departemen untuk guru/staff'),
                Select::make('shift_kerja_id')
                    ->label('Shift Kerja')
                    ->relationship('shiftKerja', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Pilih 1 shift kerja untuk guru/staff'),
                FileUpload::make('image_url')
                    ->label('Foto')
                    ->image()
                    ->imageEditor()
                    ->directory('avatars')
                    ->visibility('public')
                    ->disk('public')
                    ->columnSpanFull(),
                Textarea::make('face_embedding')
                    ->label('Face Embedding Data')
                    ->hidden()
                    ->columnSpanFull(),
                TextInput::make('fcm_token')
                    ->label('FCM Token')
                    ->hidden()
                    ->columnSpanFull(),
            ]);
    }
}
