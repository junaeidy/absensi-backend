<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informasi Dasar')
                    ->columns(2)
                    ->schema([
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
                            ->default('guru')
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('additional_data', [])),
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
                    ]),

                // Data Tambahan untuk Guru
                Section::make('Data Guru')
                    ->columns(2)
                    ->schema([
                        TextInput::make('additional_data.nip')
                            ->label('NIP (Nomor Induk Pegawai)')
                            ->maxLength(50),
                        Select::make('additional_data.golongan')
                            ->label('Golongan')
                            ->options([
                                'I/a' => 'I/a',
                                'I/b' => 'I/b',
                                'I/c' => 'I/c',
                                'I/d' => 'I/d',
                                'II/a' => 'II/a',
                                'II/b' => 'II/b',
                                'II/c' => 'II/c',
                                'II/d' => 'II/d',
                                'III/a' => 'III/a',
                                'III/b' => 'III/b',
                                'III/c' => 'III/c',
                                'III/d' => 'III/d',
                                'IV/a' => 'IV/a',
                                'IV/b' => 'IV/b',
                                'IV/c' => 'IV/c',
                                'IV/d' => 'IV/d',
                                'IV/e' => 'IV/e',
                            ])
                            ->searchable(),
                        TextInput::make('additional_data.mata_pelajaran')
                            ->label('Mata Pelajaran')
                            ->maxLength(100),
                        Select::make('additional_data.status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options([
                                'PNS' => 'PNS',
                                'PPPK' => 'PPPK',
                                'GTT' => 'GTT (Guru Tidak Tetap)',
                                'GTY' => 'GTY (Guru Tetap Yayasan)',
                                'Honorer' => 'Honorer',
                            ]),
                        TextInput::make('additional_data.nuptk')
                            ->label('NUPTK')
                            ->maxLength(50),
                        DatePicker::make('additional_data.tmt_mulai_kerja')
                            ->label('TMT Mulai Kerja'),
                        Select::make('additional_data.pendidikan_terakhir')
                            ->label('Pendidikan Terakhir')
                            ->options([
                                'SMA/SMK' => 'SMA/SMK',
                                'D3' => 'D3',
                                'S1' => 'S1',
                                'S2' => 'S2',
                                'S3' => 'S3',
                            ])
                            ->searchable(),
                        TextInput::make('additional_data.sertifikat_pendidik')
                            ->label('No. Sertifikat Pendidik')
                            ->maxLength(50),
                    ])
                    ->visible(fn ($get): bool => $get('role') === 'guru')
                    ->collapsible(),

                // Data Tambahan untuk Kepala Sekolah
                Section::make('Data Kepala Sekolah')
                    ->columns(2)
                    ->schema([
                        TextInput::make('additional_data.nip')
                            ->label('NIP (Nomor Induk Pegawai)')
                            ->maxLength(50),
                        Select::make('additional_data.golongan')
                            ->label('Golongan')
                            ->options([
                                'III/c' => 'III/c',
                                'III/d' => 'III/d',
                                'IV/a' => 'IV/a',
                                'IV/b' => 'IV/b',
                                'IV/c' => 'IV/c',
                                'IV/d' => 'IV/d',
                                'IV/e' => 'IV/e',
                            ])
                            ->searchable(),
                        DatePicker::make('additional_data.tmt_kepsek')
                            ->label('TMT Kepala Sekolah'),
                        TextInput::make('additional_data.periode_jabatan')
                            ->label('Periode Jabatan')
                            ->placeholder('Contoh: 2020-2024')
                            ->maxLength(50),
                        Select::make('additional_data.status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options([
                                'PNS' => 'PNS',
                                'PPPK' => 'PPPK',
                            ]),
                        TextInput::make('additional_data.nuptk')
                            ->label('NUPTK')
                            ->maxLength(50),
                        Select::make('additional_data.pendidikan_terakhir')
                            ->label('Pendidikan Terakhir')
                            ->options([
                                'S1' => 'S1',
                                'S2' => 'S2',
                                'S3' => 'S3',
                            ])
                            ->searchable(),
                        TextInput::make('additional_data.no_sk_pengangkatan')
                            ->label('No. SK Pengangkatan sebagai Kepala Sekolah')
                            ->maxLength(100),
                    ])
                    ->visible(fn ($get): bool => $get('role') === 'kepala_sekolah')
                    ->collapsible(),

                // Data Tambahan untuk Staff Keuangan
                Section::make('Data Staff Keuangan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('additional_data.nip')
                            ->label('NIP / NIK')
                            ->maxLength(50),
                        Select::make('additional_data.status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options([
                                'PNS' => 'PNS',
                                'PPPK' => 'PPPK',
                                'PTT' => 'PTT (Pegawai Tidak Tetap)',
                                'Honorer' => 'Honorer',
                                'Kontrak' => 'Kontrak',
                            ]),
                        DatePicker::make('additional_data.tmt_mulai_kerja')
                            ->label('TMT Mulai Kerja'),
                        Select::make('additional_data.pendidikan_terakhir')
                            ->label('Pendidikan Terakhir')
                            ->options([
                                'SMA/SMK' => 'SMA/SMK',
                                'D3' => 'D3',
                                'S1' => 'S1',
                                'S2' => 'S2',
                            ])
                            ->searchable(),
                        TextInput::make('additional_data.sertifikasi')
                            ->label('Sertifikasi')
                            ->placeholder('Contoh: Brevet A/B')
                            ->maxLength(100),
                        Select::make('additional_data.golongan')
                            ->label('Golongan (untuk PNS/PPPK)')
                            ->options([
                                'II/a' => 'II/a',
                                'II/b' => 'II/b',
                                'II/c' => 'II/c',
                                'II/d' => 'II/d',
                                'III/a' => 'III/a',
                                'III/b' => 'III/b',
                                'III/c' => 'III/c',
                                'III/d' => 'III/d',
                            ])
                            ->searchable(),
                    ])
                    ->visible(fn ($get): bool => $get('role') === 'staff_keuangan')
                    ->collapsible(),

                // Data Tambahan untuk Staff Perpustakaan
                Section::make('Data Staff Perpustakaan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('additional_data.nip')
                            ->label('NIP / NIK')
                            ->maxLength(50),
                        Select::make('additional_data.status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options([
                                'PNS' => 'PNS',
                                'PPPK' => 'PPPK',
                                'PTT' => 'PTT (Pegawai Tidak Tetap)',
                                'Honorer' => 'Honorer',
                                'Kontrak' => 'Kontrak',
                            ]),
                        DatePicker::make('additional_data.tmt_mulai_kerja')
                            ->label('TMT Mulai Kerja'),
                        Select::make('additional_data.pendidikan_terakhir')
                            ->label('Pendidikan Terakhir')
                            ->options([
                                'SMA/SMK' => 'SMA/SMK',
                                'D3' => 'D3',
                                'S1' => 'S1',
                                'S2' => 'S2',
                            ])
                            ->searchable(),
                        TextInput::make('additional_data.sertifikasi')
                            ->label('Sertifikasi Perpustakaan')
                            ->placeholder('Contoh: Pustakawan Tingkat Terampil')
                            ->maxLength(100),
                        Select::make('additional_data.golongan')
                            ->label('Golongan (untuk PNS/PPPK)')
                            ->options([
                                'II/a' => 'II/a',
                                'II/b' => 'II/b',
                                'II/c' => 'II/c',
                                'II/d' => 'II/d',
                                'III/a' => 'III/a',
                                'III/b' => 'III/b',
                                'III/c' => 'III/c',
                                'III/d' => 'III/d',
                            ])
                            ->searchable(),
                    ])
                    ->visible(fn ($get): bool => $get('role') === 'staff_perpustakaan')
                    ->collapsible(),

                // Data Tambahan untuk BK / Konselor
                Section::make('Data BK / Konselor')
                    ->columns(2)
                    ->schema([
                        TextInput::make('additional_data.nip')
                            ->label('NIP (Nomor Induk Pegawai)')
                            ->maxLength(50),
                        Select::make('additional_data.golongan')
                            ->label('Golongan')
                            ->options([
                                'II/a' => 'II/a',
                                'II/b' => 'II/b',
                                'II/c' => 'II/c',
                                'II/d' => 'II/d',
                                'III/a' => 'III/a',
                                'III/b' => 'III/b',
                                'III/c' => 'III/c',
                                'III/d' => 'III/d',
                                'IV/a' => 'IV/a',
                                'IV/b' => 'IV/b',
                            ])
                            ->searchable(),
                        Select::make('additional_data.status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options([
                                'PNS' => 'PNS',
                                'PPPK' => 'PPPK',
                                'GTT' => 'GTT (Guru Tidak Tetap)',
                                'Honorer' => 'Honorer',
                            ]),
                        TextInput::make('additional_data.nuptk')
                            ->label('NUPTK')
                            ->maxLength(50),
                        DatePicker::make('additional_data.tmt_mulai_kerja')
                            ->label('TMT Mulai Kerja'),
                        Select::make('additional_data.pendidikan_terakhir')
                            ->label('Pendidikan Terakhir')
                            ->options([
                                'S1' => 'S1',
                                'S2' => 'S2',
                                'S3' => 'S3',
                            ])
                            ->searchable(),
                        TextInput::make('additional_data.sertifikat_konselor')
                            ->label('No. Sertifikat Konselor')
                            ->maxLength(50),
                        TextInput::make('additional_data.spesialisasi')
                            ->label('Spesialisasi')
                            ->placeholder('Contoh: Konseling Anak, Karir, dll')
                            ->maxLength(100),
                    ])
                    ->visible(fn ($get): bool => $get('role') === 'bk')
                    ->collapsible(),

                // Data Tambahan untuk Admin
                Section::make('Data Admin')
                    ->columns(2)
                    ->schema([
                        TextInput::make('additional_data.nip')
                            ->label('NIP / NIK')
                            ->maxLength(50),
                        Select::make('additional_data.status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->options([
                                'PNS' => 'PNS',
                                'PPPK' => 'PPPK',
                                'PTT' => 'PTT',
                                'Kontrak' => 'Kontrak',
                                'Honorer' => 'Honorer',
                            ]),
                        DatePicker::make('additional_data.tmt_mulai_kerja')
                            ->label('TMT Mulai Kerja'),
                        Select::make('additional_data.pendidikan_terakhir')
                            ->label('Pendidikan Terakhir')
                            ->options([
                                'SMA/SMK' => 'SMA/SMK',
                                'D3' => 'D3',
                                'S1' => 'S1',
                                'S2' => 'S2',
                                'S3' => 'S3',
                            ])
                            ->searchable(),
                    ])
                    ->visible(fn ($get): bool => $get('role') === 'admin')
                    ->collapsible(),

                // Hidden fields
                Section::make('Data Sistem')
                    ->schema([
                        Textarea::make('face_embedding')
                            ->label('Face Embedding Data')
                            ->hidden()
                            ->columnSpanFull(),
                        TextInput::make('fcm_token')
                            ->label('FCM Token')
                            ->hidden()
                            ->columnSpanFull(),
                    ])
                    ->hidden(),
            ]);
    }
}
