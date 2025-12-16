<?php

namespace App\Filament\Resources\Weekends\Tables;

use App\Support\WorkdayCalculator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class WeekendsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y (D)')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('name')
                    ->label('Nama')
                    ->default('Weekend'),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(function () {
                        $years = DB::table('holidays')
                            ->where('type', 'weekend')
                            ->selectRaw('YEAR(date) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year', 'year');

                        if ($years->isEmpty()) {
                            $currentYear = now()->year;

                            return [
                                $currentYear - 1 => $currentYear - 1,
                                $currentYear => $currentYear,
                                $currentYear + 1 => $currentYear + 1,
                            ];
                        }

                        return $years;
                    })
                    ->query(function (Builder $query, $state) {
                        if ($state['value'] ?? null) {
                            return $query->whereYear('date', $state['value']);
                        }
                    }),
            ])
            ->headerActions([
                Action::make('generate_weekends')
                    ->label('Hasilkan Akhir Pekan')
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->form([
                        TextInput::make('year')
                            ->label('Tahun')
                            ->required()
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->default(now()->year),
                    ])
                    ->modalHeading('Hasilkan Akhir Pekan')
                    ->modalDescription('Ini akan secara otomatis menghasilkan semua hari Minggu untuk tahun yang dipilih. Sistem akan skip jika tanggal sudah ada (termasuk hari libur nasional).')
                    ->action(function (array $data) {
                        $result = WorkdayCalculator::generateWeekendForYear($data['year']);

                        \Filament\Notifications\Notification::make()
                            ->title('Akhir Pekan Dihasilkan')
                            ->success()
                            ->body("Dimasukkan: {$result['inserted']}, Dilewati: {$result['skipped']}")
                            ->send();
                    }),

                Action::make('generate_national_holidays')
                    ->label('Hasilkan Hari Libur Nasional')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->form([
                        TextInput::make('year')
                            ->label('Tahun')
                            ->required()
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->default(now()->year)
                            ->helperText('Data diambil dari API hari libur Indonesia'),
                    ])
                    ->modalHeading('Hasilkan Hari Libur Nasional')
                    ->modalDescription('Ini akan secara otomatis mengambil data hari libur nasional Indonesia dari API untuk tahun yang dipilih. Jika gagal, Anda dapat menambahkan secara manual melalui menu "Hari Libur Nasional".')
                    ->action(function (array $data) {
                        $result = WorkdayCalculator::generateNationalHolidays($data['year']);

                        if ($result['error']) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Mengambil Data')
                                ->warning()
                                ->body($result['error'] . ' Anda dapat menambahkan secara manual melalui menu "Hari Libur Nasional".')
                                ->persistent()
                                ->send();
                        } elseif ($result['inserted'] === 0 && $result['skipped'] > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Data Sudah Ada')
                                ->info()
                                ->body("Semua {$result['skipped']} hari libur untuk tahun {$data['year']} sudah ada di database.")
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Hari Libur Nasional Dihasilkan')
                                ->success()
                                ->body("Dimasukkan: {$result['inserted']}, Dilewati: {$result['skipped']}")
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'admin' || auth()->user()->role === 'hr'),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
