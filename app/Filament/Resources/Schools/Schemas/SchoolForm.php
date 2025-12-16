<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Filament\Forms\Components\LeafletMap;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Sekolah')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Textarea::make('address')
                            ->label('Alamat Sekolah')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Pengaturan Lokasi')
                    ->description('Konfigurasi validasi lokasi GPS untuk absensi')
                    ->schema([
                        LeafletMap::make('map')
                            ->label('Pilih Lokasi Sekolah di Peta')
                            ->defaultLocation(-6.200000, 106.816666)
                            ->defaultZoom(15)
                            ->mapHeight(400)
                            ->helperText('Drag marker atau klik pada peta untuk mengatur koordinat'),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('-6.200000')
                                    ->helperText('Koordinat latitude GPS sekolah')
                                    ->reactive(),
                                    
                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('106.816666')
                                    ->helperText('Koordinat longitude GPS sekolah')
                                    ->reactive(),

                                TextInput::make('radius_km')
                                    ->label('Radius (km)')
                                    ->required()
                                    ->numeric()
                                    ->default(0.5)
                                    ->step(0.1)
                                    ->minValue(0.1)
                                    ->maxValue(10)
                                    ->helperText('Radius check-in yang diizinkan'),
                            ]),

                        Select::make('attendance_type')
                            ->label('Metode Absensi')
                            ->required()
                            ->options([
                                'location_based_only' => 'Location Based Only (GPS)',
                                'face_recognition_only' => 'Face Recognition Only',
                                'hybrid' => 'Hybrid (GPS + Face Recognition)',
                            ])
                            ->default('location_based_only')
                            ->helperText('Pilih metode absensi staf')
                            ->native(false),
                    ]),
            ]);
    }
}
