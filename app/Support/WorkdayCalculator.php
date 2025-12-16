<?php

namespace App\Support;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkdayCalculator
{
    /**
     * Check if the given date is a weekend (Saturday or Sunday).
     */
    public static function isWeekend(Carbon $date): bool
    {
        return $date->isWeekend();
    }

    /**
     * Check if the given date is a holiday.
     */
    public static function isHoliday(Carbon $date): bool
    {
        return Holiday::where('date', $date->toDateString())->exists();
    }

    /**
     * Check if the given date is a non-working day (weekend or holiday).
     */
    public static function isNonWorkingDay(Carbon $date): bool
    {
        return self::isWeekend($date) || self::isHoliday($date);
    }

    /**
     * Count working days (excluding weekends and holidays) between two dates.
     */
    public static function countWorkdaysExcludingHolidays(Carbon $start, Carbon $end): int
    {
        $workdays = 0;
        $current = $start->copy()->startOfDay();
        $endDate = $end->copy()->startOfDay();

        // Get all holidays in the range for performance
        $holidays = Holiday::whereBetween('date', [$current->toDateString(), $endDate->toDateString()])
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->toArray();

        while ($current <= $endDate) {
            if (! $current->isWeekend() && ! in_array($current->toDateString(), $holidays)) {
                $workdays++;
            }
            $current->addDay();
        }

        return $workdays;
    }

    /**
     * Generate weekend holidays for a given year.
     * Only generates Sunday (not Saturday).
     *
     * @return array{inserted: int, skipped: int}
     */
    public static function generateWeekendForYear(int $year): array
    {
        $inserted = 0;
        $skipped = 0;

        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end = Carbon::create($year, 12, 31)->endOfDay();

        // Get existing dates to avoid duplicates (including national holidays)
        $existingDates = Holiday::whereYear('date', $year)
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->toArray();

        $weekendDates = [];
        $current = $start->copy();

        while ($current <= $end) {
            // Only check for Sunday (0 = Sunday in Carbon)
            if ($current->isSunday()) {
                $dateString = $current->toDateString();

                if (in_array($dateString, $existingDates)) {
                    // Skip if already exists (including as national holiday)
                    $skipped++;
                } else {
                    $weekendDates[] = [
                        'date' => $dateString,
                        'name' => 'Hari Minggu',
                        'type' => Holiday::TYPE_WEEKEND,
                        'is_official' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $inserted++;
                }
            }
            $current->addDay();
        }

        if (! empty($weekendDates)) {
            DB::table('holidays')->insert($weekendDates);
        }

        return [
            'inserted' => $inserted,
            'skipped' => $skipped,
        ];
    }

    /**
     * Get national holidays for Indonesia from external API.
     * Uses public holiday API to fetch dynamic data.
     *
     * @return array{holidays: array, error: ?string}
     */
    public static function getNationalHolidaysFromAPI(int $year): array
    {
        try {
            $url = "https://api-harilibur.vercel.app/api?year={$year}";
            
            // Using Laravel Http Client (Guzzle wrapper)
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get($url);

            if (!$response->successful()) {
                return [
                    'holidays' => [],
                    'error' => "API Error: HTTP {$response->status()}. Response: " . substr($response->body(), 0, 200)
                ];
            }

            $data = $response->json();
            
            // API returns direct array, not nested in 'data' key
            if (!is_array($data) || empty($data)) {
                return [
                    'holidays' => [],
                    'error' => "Data tidak ditemukan untuk tahun {$year}"
                ];
            }

            $holidays = [];
            foreach ($data as $holiday) {
                // Only include national holidays (is_national_holiday = true)
                if (isset($holiday['holiday_date']) 
                    && isset($holiday['holiday_name']) 
                    && isset($holiday['is_national_holiday']) 
                    && $holiday['is_national_holiday'] === true) {
                    $holidays[] = [
                        'date' => $holiday['holiday_date'],
                        'name' => $holiday['holiday_name'],
                    ];
                }
            }

            if (empty($holidays)) {
                return [
                    'holidays' => [],
                    'error' => "Tidak ada hari libur nasional untuk tahun {$year}"
                ];
            }

            return [
                'holidays' => $holidays,
                'error' => null
            ];

        } catch (\Exception $e) {
            return [
                'holidays' => [],
                'error' => "Exception: {$e->getMessage()}. Pastikan extension php_curl dan php_openssl aktif di php.ini"
            ];
        }
    }

    /**
     * Generate national holidays for a given year from API.
     *
     * @return array{inserted: int, skipped: int, error: ?string}
     */
    public static function generateNationalHolidays(int $year): array
    {
        $inserted = 0;
        $skipped = 0;
        $error = null;

        // Get national holidays data from API
        $result = self::getNationalHolidaysFromAPI($year);
        
        if (!empty($result['error'])) {
            return [
                'inserted' => 0,
                'skipped' => 0,
                'error' => $result['error'],
            ];
        }

        $nationalHolidays = $result['holidays'];

        if (empty($nationalHolidays)) {
            return [
                'inserted' => 0,
                'skipped' => 0,
                'error' => 'Tidak dapat mengambil data dari API. Silakan tambahkan secara manual melalui menu Hari Libur Nasional.',
            ];
        }

        // Get existing dates to avoid duplicates
        $existingDates = Holiday::whereYear('date', $year)
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->toArray();

        $holidayDates = [];

        foreach ($nationalHolidays as $holiday) {
            $dateString = $holiday['date'];

            if (in_array($dateString, $existingDates)) {
                $skipped++;
            } else {
                $holidayDates[] = [
                    'date' => $dateString,
                    'name' => $holiday['name'],
                    'type' => Holiday::TYPE_NATIONAL,
                    'is_official' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $inserted++;
            }
        }

        if (! empty($holidayDates)) {
            DB::table('holidays')->insert($holidayDates);
        }

        return [
            'inserted' => $inserted,
            'skipped' => $skipped,
            'error' => null,
        ];
    }
}
