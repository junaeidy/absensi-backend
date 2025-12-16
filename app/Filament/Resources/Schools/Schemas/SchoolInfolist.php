<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Models\ShiftKerja;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Sekolah')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Sekolah')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('email')
                            ->label('Alamat Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        TextEntry::make('address')
                            ->label('Alamat Sekolah')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Pengaturan Lokasi')
                    ->schema([
                        TextEntry::make('latitude')
                            ->label('Latitude')
                            ->icon('heroicon-o-map-pin')
                            ->copyable(),

                        TextEntry::make('longitude')
                            ->label('Longitude')
                            ->icon('heroicon-o-map-pin')
                            ->copyable(),

                        TextEntry::make('radius_km')
                            ->label('Radius Check-in')
                            ->suffix(' km')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('attendance_type')
                            ->label('Metode Absensi')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'location_based_only' => 'Location Based (GPS)',
                                'face_recognition_only' => 'Face Recognition',
                                'hybrid' => 'Hybrid (GPS + Face)',
                                default => $state,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'location_based_only' => 'primary',
                                'face_recognition_only' => 'success',
                                'hybrid' => 'warning',
                                default => 'gray',
                            }),
                    ])
                    ->columns(4),

                Section::make('Shift Kerja Tersedia')
                    ->description('Konfigurasi shift kerja untuk sekolah ini')
                    ->schema([
                        TextEntry::make('shifts')
                            ->label('')
                            ->state(function () {
                                return ShiftKerja::where('is_active', true)
                                    ->orderBy('start_time')
                                    ->get()
                                    ->map(function ($shift) {
                                        $crossDay = $shift->is_cross_day ? ' 🌙' : '';
                                        $grace = $shift->grace_period_minutes.' menit toleransi';
                                        $employees = $shift->users()->count();

                                        return sprintf(
                                            '%s: %s - %s%s (%s, %d staff)',
                                            $shift->name,
                                            $shift->start_time->format('H:i'),
                                            $shift->end_time->format('H:i'),
                                            $crossDay,
                                            $grace,
                                            $employees
                                        );
                                    })
                                    ->toArray();
                            })
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->placeholder('Belum ada shift yang dikonfigurasi'),
                    ])
                    ->collapsible(),
            ]);
    }
}
