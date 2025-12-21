<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\ShiftKerja;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Siswa Aktif', Student::where('status', 'active')->count())
                ->description('Siswa yang aktif saat ini')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 25, 28]),

            Stat::make('Total Guru & Staff', User::where('role', '!=', 'siswa')->orWhereNull('role')->count())
                ->description('Jumlah guru dan staff')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Kelas', ClassModel::count())
                ->description('Jumlah kelas tersedia')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('info'),

            Stat::make('Total Mata Pelajaran', Subject::where('is_active', true)->count())
                ->description('Mata pelajaran aktif')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),

            Stat::make('Total Orang Tua', StudentParent::count())
                ->description('Data orang tua/wali')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('purple'),

            Stat::make('Absensi Hari Ini', $this->getTodayAttendance())
                ->description('Guru & staff yang hadir')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Cuti Pending', $this->getPendingLeave())
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }

    private function getTodayAttendance(): int
    {
        return Attendance::whereNotNull('time_in')
            ->whereDate('date', now()->toDateString())
            ->count();
    }

    private function getPendingLeave(): int
    {
        return Leave::where('status', 'pending')
            ->count();
    }

    private function getApprovedOvertimeThisMonth(): int
    {
        return Overtime::where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
    }

    private function getApprovedLeaveThisMonth(): int
    {
        return Leave::where('status', 'approved')
            ->whereNotNull('approved_at')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();
    }

    private function getCompleteAttendanceThisMonth(): int
    {
        return Attendance::whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
    }
}
