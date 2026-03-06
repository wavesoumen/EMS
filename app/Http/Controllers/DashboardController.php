<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['role', 'timeBank', 'supervisor']);

        $today = Carbon::today();

        // Today's attendance record
        $todayRecord = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->latest()
            ->first();

        // Recent attendance records (last 7 days)
        $recentRecords = AttendanceRecord::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        // Pending approvals for supervisors
        $pendingApprovals = collect();
        if ($user->hasPermission('approve_clockin')) {
            $subordinateIds = User::where('supervisor_id', $user->id)->pluck('id');
            $pendingApprovals = AttendanceRecord::whereIn('user_id', $subordinateIds)
                ->where('status', 'pending')
                ->with('user', 'user.role')
                ->orderBy('clock_in_requested_at', 'asc')
                ->get();
        }

        // Team stats for managers and above
        $teamStats = null;
        if ($user->role->level <= 3) {
            $subordinates = User::where('supervisor_id', $user->id)->with('role', 'timeBank')->get();
            $teamStats = [
                'total' => $subordinates->count(),
                'clocked_in' => AttendanceRecord::whereIn('user_id', $subordinates->pluck('id'))
                    ->where('date', $today)
                    ->whereIn('status', ['approved'])
                    ->count(),
                'pending' => $pendingApprovals->count(),
                'subordinates' => $subordinates,
            ];
        }

        // Admin stats
        $adminStats = null;
        if ($user->isAdmin()) {
            $adminStats = [
                'total_employees' => User::where('company_id', $user->company_id)->count(),
                'active_today' => AttendanceRecord::where('date', $today)
                    ->whereIn('status', ['approved', 'clocked_out'])
                    ->count(),
                'pending_all' => AttendanceRecord::where('status', 'pending')
                    ->where('date', $today)
                    ->count(),
            ];
        }

        return view('dashboard.index', compact(
            'user', 'todayRecord', 'recentRecords',
            'pendingApprovals', 'teamStats', 'adminStats'
        ));
    }
}
