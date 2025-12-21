<?php

namespace App\Filament\Resources\ClassModels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grade.name')
                    ->label('Tingkat')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable()
                    ->suffix(' siswa'),
                TextColumn::make('room')
                    ->label('Ruangan')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
