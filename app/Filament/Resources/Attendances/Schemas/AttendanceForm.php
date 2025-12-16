<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('user_id')
                    ->label('Guru/Staff')
                    ->options(User::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->native(false),
                TimePicker::make('time_in')
                    ->label('Waktu Check In')
                    ->required()
                    ->seconds(false),
                TimePicker::make('time_out')
                    ->label('Waktu Check Out')
                    ->seconds(false),
                TextInput::make('latlon_in')
                    ->label('Lokasi Check In (Lat, Lon)')
                    ->placeholder('e.g., -6.2088, 106.8456')
                    ->required(),
                TextInput::make('latlon_out')
                    ->label('Lokasi Check Out (Lat, Lon)')
                    ->placeholder('e.g., -6.2088, 106.8456'),
            ]);
    }
}
