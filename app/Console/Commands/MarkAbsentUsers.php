<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use App\Support\WorkdayCalculator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkAbsentUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark users as absent who did not check in for the specified date (default: today)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateString = $this->argument('date') ?? now()->toDateString();
        
        try {
            $date = Carbon::parse($dateString);
        } catch (\Exception $e) {
            $this->error('Invalid date format. Please use Y-m-d format (e.g., 2024-12-16)');
            return self::FAILURE;
        }

        $this->info("Processing absent marking for date: {$date->toDateString()}");

        // Check if the date is a weekend or holiday
        $isWeekend = WorkdayCalculator::isWeekend($date);
        $isHoliday = WorkdayCalculator::isHoliday($date);

        if ($isWeekend || $isHoliday) {
            $this->warn("Date {$date->toDateString()} is a " . ($isWeekend ? 'weekend' : 'holiday') . '.');
            $this->warn('Absent marking will still proceed, but most users should not be required to attend.');
        }

        // Get all active users
        $allUsers = User::all();

        // Get users who already have attendance records for this date
        $usersWithAttendance = Attendance::whereDate('date', $date->toDateString())
            ->pluck('user_id')
            ->toArray();

        // Get users who are on approved leave for this date
        $usersOnLeave = Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->pluck('employee_id')
            ->toArray();

        $markedCount = 0;
        $skippedCount = 0;
        $onLeaveCount = 0;

        foreach ($allUsers as $user) {
            // Skip if user already has attendance record
            if (in_array($user->id, $usersWithAttendance)) {
                $skippedCount++;
                continue;
            }

            // Skip if user is on approved leave
            if (in_array($user->id, $usersOnLeave)) {
                $onLeaveCount++;
                $this->line("  Skipping {$user->name} (on leave)");
                continue;
            }

            // Create absent record
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->shift_id = $user->shift_kerja_id;
            $attendance->date = $date->toDateString();
            $attendance->time_in = null;
            $attendance->time_out = null;
            $attendance->latlon_in = null;
            $attendance->latlon_out = null;
            $attendance->status = 'absent';
            $attendance->is_weekend = $isWeekend;
            $attendance->is_holiday = $isHoliday;
            $attendance->holiday_work = false;
            $attendance->late_minutes = 0;
            $attendance->early_leave_minutes = 0;
            $attendance->save();

            $markedCount++;
            $this->line("  ✓ Marked {$user->name} as absent");
        }

        $this->newLine();
        $this->info("Summary for {$date->toDateString()}:");
        $this->info("  Total users: " . $allUsers->count());
        $this->info("  Already checked in: {$skippedCount}");
        $this->info("  On leave: {$onLeaveCount}");
        $this->info("  Marked as absent: {$markedCount}");
        $this->newLine();
        
        if ($markedCount > 0) {
            $this->info('✓ Absent marking completed successfully!');
        } else {
            $this->comment('No users needed to be marked as absent.');
        }

        return self::SUCCESS;
    }
}
