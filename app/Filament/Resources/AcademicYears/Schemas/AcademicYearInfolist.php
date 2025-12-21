<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AcademicYearInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama Tahun Ajaran'),
                TextEntry::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y'),
                TextEntry::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d/m/Y'),
                IconEntry::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
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
