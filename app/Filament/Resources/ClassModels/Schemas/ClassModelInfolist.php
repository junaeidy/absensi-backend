<?php

namespace App\Filament\Resources\ClassModels\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClassModelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('grade.name')
                    ->label('Tingkat'),
                TextEntry::make('name')
                    ->label('Nama Kelas'),
                TextEntry::make('academicYear.name')
                    ->label('Tahun Ajaran'),
                TextEntry::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->suffix(' siswa'),
                TextEntry::make('room')
                    ->label('Ruangan')
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
