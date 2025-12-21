<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/img/default-avatar.png')),
                
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('gender')
                    ->label('JK')
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
                
                TextColumn::make('phone')
                    ->label('No. HP')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable(),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('user.email')
                    ->label('Akun Login')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? '✓ Aktif' : '✗ Nonaktif')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->tooltip(fn ($state) => $state ? "Email: {$state}" : 'Belum ada akun login')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('parents.name')
                    ->label('Orang Tua/Wali')
                    ->badge()
                    ->separator(', ')
                    ->limitList(2)
                    ->tooltip(fn ($record) => $record->parents->pluck('name')->join(', '))
                    ->toggleable(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
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
                    }),
                
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('admission_date')
                    ->label('Tanggal Masuk')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'alumni' => 'Alumni',
                        'moved' => 'Pindah',
                        'dropped_out' => 'Drop Out',
                    ])
                    ->default('active'),
                
                SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                
                TrashedFilter::make()
                    ->label('Data Terhapus'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
