<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SubjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')
                    ->label('Kode Mata Pelajaran'),
                TextEntry::make('name')
                    ->label('Nama Mata Pelajaran'),
                TextEntry::make('description')
                    ->label('Deskripsi')
                    ->placeholder('-')
                    ->columnSpanFull(),
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
