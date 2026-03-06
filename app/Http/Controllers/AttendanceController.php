<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\TimeBank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function clockIn()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Check if already has a pending or approved record today
        $existing = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have an active clock-in for today.');
        }

        $now = Carbon::now();

        // Admin auto-approves their own clock-in
        if ($user->isAdmin()) {
            AttendanceRecord::create([
                'user_id' => $user->id,
                'clock_in_requested_at' => $now,
                'clock_in_approved_at' => $now,
                'clock_in_time' => $now,
                'status' => 'approved',
                'date' => $today,
            ]);
            return back()->with('success', 'Clocked in successfully!');
        }

        // For non-admin, create pending request
        AttendanceRecord::create([
            'user_id' => $user->id,
            'clock_in_requested_at' => $now,
            'status' => 'pending',
            'date' => $today,
        ]);

        return back()->with('success', 'Clock-in request sent. Waiting for supervisor approval.');
    }

    public function clockOut()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $record = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'approved')
            ->first();

        if (!$record) {
            return back()->with('error', 'No approved clock-in found for today.');
        }

        $now = Carbon::now();
        $totalMinutes = $record->clock_in_time->diffInMinutes($now);

        $record->update([
            'clock_out_time' => $now,
            'total_minutes' => $totalMinutes,
            'status' => 'clocked_out',
        ]);

        // Update time bank
        $timeBank = TimeBank::firstOrCreate(
            ['user_id' => $user->id],
            ['total_minutes' => 0]
        );
        $timeBank->addMinutes($totalMinutes);

        return back()->with('success', "Clocked out! Today's work: " . $record->formatted_total_time);
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $record = AttendanceRecord::findOrFail($id);

        // Verify this user is the supervisor of the record owner
        $employee = User::findOrFail($record->user_id);
        if (!$user->isSupervisorOf($employee) && !$user->isAdmin()) {
            return back()->with('error', 'You are not authorized to approve this request.');
        }

        $now = Carbon::now();
        $record->update([
            'status' => 'approved',
            'clock_in_approved_at' => $now,
            'clock_in_time' => $record->clock_in_requested_at, // Use the original request time
            'approved_by' => $user->id,
        ]);

        return back()->with('success', $employee->name . "'s clock-in has been approved.");
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $record = AttendanceRecord::findOrFail($id);

        $employee = User::findOrFail($record->user_id);
        if (!$user->isSupervisorOf($employee) && !$user->isAdmin()) {
            return back()->with('error', 'You are not authorized to reject this request.');
        }

        $record->update(['status' => 'rejected']);

        return back()->with('success', $employee->name . "'s clock-in has been rejected.");
    }

    public function history()
    {
        $user = Auth::user();

        $records = AttendanceRecord::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('attendance.history', compact('user', 'records'));
    }

    public function pendingApprovals()
    {
        $user = Auth::user();

        $subordinateIds = User::where('supervisor_id', $user->id)->pluck('id');

        $records = AttendanceRecord::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->with('user', 'user.role')
            ->orderBy('clock_in_requested_at', 'asc')
            ->paginate(20);

        return view('attendance.pending', compact('user', 'records'));
    }
}
