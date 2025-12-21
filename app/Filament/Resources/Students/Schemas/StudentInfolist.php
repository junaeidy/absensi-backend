<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\Student;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Foto Siswa')
                    ->schema([
                        ImageEntry::make('photo_url')
                            ->label('Foto')
                            ->defaultImageUrl(url('/img/default-avatar.png'))
                            ->circular()
                            ->size(150),
                    ])
                    ->collapsible(),
                
                Section::make('Informasi Pribadi')
                    ->description('Data pribadi dan identitas siswa')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('nis')
                            ->label('NIS')
                            ->copyable()
                            ->icon('heroicon-o-identification')
                            ->weight('bold'),
                        TextEntry::make('nisn')
                            ->label('NISN')
                            ->placeholder('-')
                            ->copyable()
                            ->icon('heroicon-o-identification'),
                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->size('lg')
                            ->weight('bold')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('nickname')
                            ->label('Nama Panggilan')
                            ->placeholder('-')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'L' => 'primary',
                                'P' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                                default => $state,
                            }),
                        TextEntry::make('birth_place')
                            ->label('Tempat Lahir')
                            ->placeholder('-')
                            ->icon('heroicon-o-map-pin'),
                        TextEntry::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->date('d F Y')
                            ->placeholder('-')
                            ->icon('heroicon-o-cake'),
                        TextEntry::make('religion')
                            ->label('Agama')
                            ->placeholder('-')
                            ->icon('heroicon-o-bookmark'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Section::make('Informasi Kontak')
                    ->description('Alamat dan kontak siswa')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Alamat')
                            ->placeholder('-')
                            ->icon('heroicon-o-map')
                            ->columnSpanFull(),
                        TextEntry::make('phone')
                            ->label('No. HP')
                            ->placeholder('-')
                            ->copyable()
                            ->icon('heroicon-o-phone'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-')
                            ->copyable()
                            ->icon('heroicon-o-envelope'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Section::make('Status Akademik')
                    ->description('Informasi status dan riwayat pendidikan')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->size('lg')
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'alumni' => 'info',
                                'moved' => 'warning',
                                'dropped_out' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'Aktif',
                                'alumni' => 'Alumni',
                                'moved' => 'Pindah',
                                'dropped_out' => 'Drop Out',
                                default => $state,
                            })
                            ->icon(fn (string $state): string => match ($state) {
                                'active' => 'heroicon-o-check-circle',
                                'alumni' => 'heroicon-o-academic-cap',
                                'moved' => 'heroicon-o-arrow-right-circle',
                                'dropped_out' => 'heroicon-o-x-circle',
                                default => 'heroicon-o-question-mark-circle',
                            }),
                        TextEntry::make('admission_date')
                            ->label('Tanggal Masuk')
                            ->date('d F Y')
                            ->placeholder('-')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('graduation_date')
                            ->label('Tanggal Lulus')
                            ->date('d F Y')
                            ->placeholder('-')
                            ->icon('heroicon-o-calendar')
                            ->visible(fn ($record) => $record->graduation_date !== null),
                        TextEntry::make('previous_school')
                            ->label('Sekolah Asal')
                            ->placeholder('-')
                            ->icon('heroicon-o-building-office-2')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Section::make('Informasi Akun')
                    ->description('Status akun login siswa')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextEntry::make('user.email')
                            ->label('Email Login')
                            ->placeholder('Belum ada akun login')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'gray')
                            ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->copyable()
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->collapsible()
                    ->visible(fn ($record) => $record->user_id !== null),
                
                Section::make('Data Orang Tua / Wali')
                    ->description('Informasi orang tua atau wali siswa')
                    ->icon('heroicon-o-users')
                    ->schema([
                        RepeatableEntry::make('parents')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama')
                                    ->weight('bold')
                                    ->icon('heroicon-o-user')
                                    ->size('lg'),
                                TextEntry::make('pivot.relation_type')
                                    ->label('Hubungan')
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'ayah' => 'Ayah',
                                        'ibu' => 'Ibu',
                                        'wali' => 'Wali',
                                        default => $state,
                                    }),
                                TextEntry::make('pivot.is_primary_contact')
                                    ->label('Kontak Utama')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                                    ->formatStateUsing(fn (bool $state): string => $state ? '✓ Ya' : '✗ Tidak')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                TextEntry::make('nik')
                                    ->label('NIK')
                                    ->placeholder('-')
                                    ->copyable()
                                    ->icon('heroicon-o-identification'),
                                TextEntry::make('phone')
                                    ->label('No. HP')
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->placeholder('-')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),
                                TextEntry::make('occupation')
                                    ->label('Pekerjaan')
                                    ->placeholder('-')
                                    ->icon('heroicon-o-briefcase'),
                                TextEntry::make('education_level')
                                    ->label('Pendidikan')
                                    ->placeholder('-')
                                    ->icon('heroicon-o-academic-cap'),
                                TextEntry::make('address')
                                    ->label('Alamat')
                                    ->placeholder('-')
                                    ->icon('heroicon-o-map')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->contained(true),
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => $record->parents->isNotEmpty()),
                
                Section::make('Catatan')
                    ->description('Catatan tambahan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->collapsed()
                    ->collapsible()
                    ->visible(fn ($record) => !empty($record->notes)),
                
                Section::make('Informasi Sistem')
                    ->description('Data sistem dan histori')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('-')
                            ->icon('heroicon-o-plus-circle'),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('-')
                            ->since()
                            ->icon('heroicon-o-pencil'),
                        TextEntry::make('deleted_at')
                            ->label('Dihapus Pada')
                            ->dateTime('d F Y, H:i')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->visible(fn (Student $record): bool => $record->trashed()),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
