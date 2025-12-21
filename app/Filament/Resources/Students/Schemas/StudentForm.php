<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentParent;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pribadi')
                    ->schema([
                        TextInput::make('nis')
                            ->label('NIS')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        
                        TextInput::make('nisn')
                            ->label('NISN')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('nickname')
                            ->label('Nama Panggilan')
                            ->maxLength(255),
                        
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                        
                        TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->maxLength(255),
                        
                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        
                        Select::make('religion')
                            ->label('Agama')
                            ->options([
                                'Islam' => 'Islam',
                                'Kristen' => 'Kristen',
                                'Katolik' => 'Katolik',
                                'Hindu' => 'Hindu',
                                'Buddha' => 'Buddha',
                                'Konghucu' => 'Konghucu',
                            ]),
                    ])
                    ->columns(2),

                Section::make('Kontak')
                    ->schema([
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        TextInput::make('phone')
                            ->label('No. HP')
                            ->tel()
                            ->maxLength(20),
                        
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Akun Login Siswa')
                    ->description('Atur akses login untuk siswa di aplikasi Mobile')
                    ->schema([
                        Toggle::make('create_user_account')
                            ->label('Aktifkan Akun Login')
                            ->helperText('Siswa dapat login ke aplikasi dengan email dan password')
                            ->live()
                            ->default(true)
                            ->columnSpanFull(),
                        
                        TextInput::make('user_email')
                            ->label('Email Login')
                            ->email()
                            ->required(fn ($get) => $get('create_user_account'))
                            ->visible(fn ($get) => $get('create_user_account'))
                            ->helperText('Email untuk login siswa (contoh: nama.siswa@student.school.id)')
                            ->default(fn ($get) => $get('email'))
                            ->maxLength(255),
                        
                        TextInput::make('user_password')
                            ->label('Password Login')
                            ->password()
                            ->revealable()
                            ->required(fn ($get) => $get('create_user_account'))
                            ->visible(fn ($get) => $get('create_user_account'))
                            ->helperText('Password default: password123 (siswa dapat mengubahnya nanti)')
                            ->default('password123')
                            ->minLength(8),
                        
                        TextInput::make('user_name')
                            ->label('Nama untuk Akun')
                            ->visible(fn ($get) => $get('create_user_account'))
                            ->default(fn ($get) => $get('name'))
                            ->helperText('Akan menggunakan nama siswa secara otomatis')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Section::make('Data Orang Tua / Wali')
                    ->description('Tambahkan data orang tua atau wali siswa')
                    ->schema([
                        Repeater::make('parentRelations')
                            ->label('')
                            ->schema([
                                Select::make('parent_id')
                                    ->label('Pilih Orang Tua/Wali')
                                    ->options(StudentParent::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $parent = StudentParent::find($state);
                                            if ($parent) {
                                                $set('parent_name', $parent->name);
                                                $set('parent_phone', $parent->phone);
                                            }
                                        }
                                    })
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nama')
                                            ->required()
                                            ->maxLength(255),
                                        
                                        TextInput::make('nik')
                                            ->label('NIK KTP')
                                            ->maxLength(20)
                                            ->unique(table: 'student_parents', column: 'nik'),
                                        
                                        TextInput::make('phone')
                                            ->label('No. HP')
                                            ->tel()
                                            ->required()
                                            ->maxLength(20),
                                        
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255),
                                        
                                        TextInput::make('occupation')
                                            ->label('Pekerjaan')
                                            ->maxLength(255),
                                        
                                        Textarea::make('address')
                                            ->label('Alamat')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        
                                        TextInput::make('education_level')
                                            ->label('Pendidikan Terakhir')
                                            ->maxLength(255),
                                        
                                        TextInput::make('monthly_income')
                                            ->label('Penghasilan per Bulan')
                                            ->numeric()
                                            ->prefix('Rp'),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $parent = StudentParent::create($data);
                                        return $parent->id;
                                    })
                                    ->columnSpanFull(),
                                
                                TextInput::make('parent_name')
                                    ->label('Nama')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn (Get $get) => $get('parent_id')),
                                
                                TextInput::make('parent_phone')
                                    ->label('No. HP')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn (Get $get) => $get('parent_id')),
                                
                                Select::make('relation_type')
                                    ->label('Hubungan')
                                    ->options([
                                        'ayah' => 'Ayah',
                                        'ibu' => 'Ibu',
                                        'wali' => 'Wali',
                                    ])
                                    ->required(),
                                
                                Toggle::make('is_primary_contact')
                                    ->label('Kontak Utama')
                                    ->helperText('Orang tua yang akan dihubungi untuk notifikasi')
                                    ->default(false),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Orang Tua/Wali')
                            ->reorderable(false)
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => 
                                StudentParent::find($state['parent_id'])?->name ?? 'Pilih orang tua'
                            ),
                    ])
                    ->collapsed(),

                Section::make('Status & Lainnya')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'alumni' => 'Alumni',
                                'moved' => 'Pindah',
                                'dropped_out' => 'Drop Out',
                            ])
                            ->default('active')
                            ->required(),
                        
                        DatePicker::make('admission_date')
                            ->label('Tanggal Masuk')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        
                        DatePicker::make('graduation_date')
                            ->label('Tanggal Lulus')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        
                        TextInput::make('previous_school')
                            ->label('Sekolah Asal')
                            ->maxLength(255),
                        
                        FileUpload::make('photo_url')
                            ->label('Foto')
                            ->image()
                            ->directory('students')
                            ->imageEditor()
                            ->columnSpanFull(),
                        
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
