<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\ShiftAssignment;
use App\Models\ShiftKerja;
use App\Support\WorkdayCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // checkin
    public function checkin(Request $request)
    {
        // validate lat and long
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $currentUser = $request->user();
        $currentDateTime = now();
        
        // Check if user is on approved leave today
        $onLeave = $this->isUserOnLeave($currentUser->id, $currentDateTime->toDateString());
        if ($onLeave) {
            return response([
                'message' => 'Kamu tidak dapat melakukan check-in karena sedang dalam cuti.',
                'leave_info' => $onLeave,
            ], 422);
        }

        $scheduledShiftId = ShiftAssignment::query()
            ->forUser($currentUser->id)
            ->forDate($currentDateTime)
            ->scheduled()
            ->value('shift_id');

        $resolvedShiftId = $scheduledShiftId ?? $currentUser->shift_kerja_id;
        $activeShift = $resolvedShiftId ? ShiftKerja::query()->find($resolvedShiftId) : null;

        $isWeekend = WorkdayCalculator::isWeekend($currentDateTime->copy());
        $isHoliday = WorkdayCalculator::isHoliday($currentDateTime->copy());

        $status = 'on_time';
        $lateMinutes = 0;

        if ($activeShift) {
            $startTimeString = $activeShift->getRawOriginal('start_time') ?? $activeShift->start_time?->format('H:i:s');

            if ($startTimeString) {
                $normalizedStartTime = strlen($startTimeString) === 5 ? $startTimeString.':00' : $startTimeString;
                $shiftStart = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $currentDateTime->toDateString().' '.$normalizedStartTime,
                    config('app.timezone')
                );

                if ($activeShift->is_cross_day && $currentDateTime->lessThan($shiftStart)) {
                    $shiftStart->subDay();
                }

                $graceMinutes = (int) ($activeShift->grace_period_minutes ?? 0);
                $lateThreshold = $shiftStart->copy()->addMinutes($graceMinutes);

                if ($currentDateTime->greaterThan($lateThreshold)) {
                    $status = 'late';
                    $lateMinutes = (int) $lateThreshold->diffInMinutes($currentDateTime);
                }
            }
        }

        $attendance = new Attendance;
        $attendance->user_id = $currentUser->id;
        $attendance->shift_id = $activeShift?->id;
        $attendance->date = $currentDateTime->toDateString();
        $attendance->time_in = $currentDateTime->toTimeString();
        $attendance->latlon_in = $request->latitude.','.$request->longitude;
        $attendance->status = $status;
        $attendance->is_weekend = $isWeekend;
        $attendance->is_holiday = $isHoliday;
        $attendance->holiday_work = $activeShift ? ($isWeekend || $isHoliday) : false;
        $attendance->late_minutes = $lateMinutes;
        $attendance->save();

        return response([
            'message' => 'Berhasil check-in.',
            'attendance' => $attendance,
        ], 200);
    }

    // checkout
    public function checkout(Request $request)
    {
        // validate lat and long
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $currentUser = $request->user();
        $today = now();

        // Check if user is on approved leave today
        $onLeave = $this->isUserOnLeave($currentUser->id, $today->toDateString());
        if ($onLeave) {
            return response([
                'message' => 'Kamu tidak dapat melakukan checkout karena sedang dalam cuti.',
                'leave_info' => $onLeave,
            ], 422);
        }

        $attendance = Attendance::where('user_id', $currentUser->id)
            ->whereDate('date', $today)
            ->first();

        // check if attendance not found
        if (! $attendance) {
            return response(['message' => 'Harap lakukan check-in terlebih dahulu'], 400);
        }

        // save checkout
        $attendance->time_out = now()->toTimeString();
        $attendance->latlon_out = $request->latitude.','.$request->longitude;
        $attendance->save();

        return response([
            'message' => 'Berhasil check-out.',
            'attendance' => $attendance,
        ], 200);
    }

    // check is checkedin
    public function isCheckedin(Request $request)
    {
        // get today attendance
        $attendance = Attendance::where('user_id', $request->user()->id)
            ->whereDate('date', now())
            ->first();

        $isCheckout = $attendance ? $attendance->time_out : false;

        return response([
            'checkedin' => $attendance ? true : false,
            'checkedout' => $isCheckout ? true : false,
        ], 200);
    }

    // index
    public function index(Request $request)
    {
        $date = $request->input('date');

        $currentUser = $request->user();

        $query = Attendance::where('user_id', $currentUser->id);

        if ($date) {
            $query->where('date', $date);
        }

        $attendance = $query->get();

        return response([
            'message' => 'Success',
            'data' => $attendance,
        ], 200);
    }

    // check leave status for current user
    public function checkLeaveStatus(Request $request)
    {
        $currentUser = $request->user();
        $today = now()->toDateString();
        
        $onLeave = $this->isUserOnLeave($currentUser->id, $today);
        
        return response([
            'on_leave' => $onLeave ? true : false,
            'leave_info' => $onLeave ?: null,
        ], 200);
    }

    /**
     * Check if user is on approved leave for the given date
     *
     * @param int $userId
     * @param string $date (Y-m-d format)
     * @return array|false
     */
    private function isUserOnLeave($userId, $date)
    {
        $leave = Leave::where('employee_id', $userId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->with('leaveType')
            ->first();

        if ($leave) {
            return [
                'id' => $leave->id,
                'leave_type' => $leave->leaveType->name ?? 'Unknown',
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'reason' => $leave->reason,
            ];
        }

        return false;
    }
}
