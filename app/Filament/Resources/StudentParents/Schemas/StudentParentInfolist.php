<?php

namespace App\Filament\Resources\StudentParents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentParentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama Lengkap'),
                TextEntry::make('nik')
                    ->label('NIK')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label('No. HP'),
                TextEntry::make('email')
                    ->label('Email')
                    ->placeholder('-'),
                TextEntry::make('occupation')
                    ->label('Pekerjaan')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->label('Alamat')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('education_level')
                    ->label('Tingkat Pendidikan')
                    ->placeholder('-'),
                TextEntry::make('monthly_income')
                    ->label('Pendapatan Bulanan')
                    ->money('IDR')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ]);
    }
}
