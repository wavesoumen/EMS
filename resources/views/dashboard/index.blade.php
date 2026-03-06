@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="animate-fade-in">
    {{-- Clock Section --}}
    <div class="clock-section" x-data="liveClock">
        <div class="clock-date" x-text="date"></div>
        <div class="clock-time" x-text="time"></div>

        @if(!$todayRecord)
            <div class="clock-status status-idle">
                <span class="pulse-dot" style="background:var(--gray-400)"></span>
                Not Clocked In
            </div>
            <div class="clock-actions">
                <form action="{{ route('attendance.clockIn') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">⏱ Clock In</button>
                </form>
            </div>
        @elseif($todayRecord->status === 'pending')
            <div class="clock-status status-pending">
                <span class="pulse-dot orange"></span>
                Pending Approval
            </div>
            <p class="text-sm text-muted">Requested at {{ $todayRecord->clock_in_requested_at->format('h:i A') }}. Waiting for supervisor approval.</p>
        @elseif($todayRecord->status === 'approved')
            <div class="clock-status status-active">
                <span class="pulse-dot green"></span>
                Clocked In — Working
            </div>
            <p class="text-sm text-muted mb-3">Since {{ $todayRecord->clock_in_time->format('h:i A') }}</p>
            <div class="clock-actions">
                <form action="{{ route('attendance.clockOut') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg">⏹ Clock Out</button>
                </form>
            </div>
        @elseif($todayRecord->status === 'clocked_out')
            <div class="clock-status status-done">
                <span class="pulse-dot blue"></span>
                Day Complete
            </div>
            <p class="text-sm text-muted">
                {{ $todayRecord->clock_in_time->format('h:i A') }} — {{ $todayRecord->clock_out_time->format('h:i A') }}
                • <strong>{{ $todayRecord->formatted_total_time }}</strong>
            </p>
        @elseif($todayRecord->status === 'rejected')
            <div class="clock-status" style="background:var(--danger-50);color:var(--danger-dark);">
                Clock-In Rejected
            </div>
            <p class="text-sm text-muted mb-3">Your clock-in was rejected. You can try again.</p>
            <div class="clock-actions">
                <form action="{{ route('attendance.clockIn') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg">⏱ Clock In Again</button>
                </form>
            </div>
        @endif
    </div>

    {{-- Stats Row --}}
    <div class="stat-grid">
        {{-- Time Bank --}}
        <div class="time-bank-display">
            <div class="time-bank-label">Your Time Bank</div>
            <div class="time-bank-value">{{ $user->formatted_total_time }}</div>
            <div class="time-bank-sub">Accumulated work time</div>
        </div>

        @if($todayRecord && $todayRecord->status === 'clocked_out')
            <div class="stat-card stat-success">
                <div class="stat-icon icon-success">📊</div>
                <div class="stat-content">
                    <h4>Today's Work</h4>
                    <div class="stat-value">{{ $todayRecord->formatted_total_time }}</div>
                    <div class="stat-sub">{{ $todayRecord->clock_in_time->format('h:i A') }} - {{ $todayRecord->clock_out_time->format('h:i A') }}</div>
                </div>
            </div>
        @endif

        @if($teamStats)
            <div class="stat-card stat-primary">
                <div class="stat-icon icon-primary">👥</div>
                <div class="stat-content">
                    <h4>Team Members</h4>
                    <div class="stat-value">{{ $teamStats['total'] }}</div>
                    <div class="stat-sub">{{ $teamStats['clocked_in'] }} working today</div>
                </div>
            </div>

            @if($teamStats['pending'] > 0)
                <div class="stat-card stat-warning">
                    <div class="stat-icon icon-warning">⏳</div>
                    <div class="stat-content">
                        <h4>Pending Approvals</h4>
                        <div class="stat-value">{{ $teamStats['pending'] }}</div>
                        <div class="stat-sub"><a href="{{ route('attendance.pending') }}">Review now →</a></div>
                    </div>
                </div>
            @endif
        @endif

        @if($adminStats)
            <div class="stat-card stat-info">
                <div class="stat-icon icon-info">🏢</div>
                <div class="stat-content">
                    <h4>Total Employees</h4>
                    <div class="stat-value">{{ $adminStats['total_employees'] }}</div>
                    <div class="stat-sub">{{ $adminStats['active_today'] }} active today</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Pending Approvals Section (for supervisors) --}}
    @if($pendingApprovals->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h3>⏳ Pending Approvals</h3>
                <a href="{{ route('attendance.pending') }}" class="btn btn-outline btn-sm">View All →</a>
            </div>
            <div class="card-body" style="padding:1rem;">
                @foreach($pendingApprovals->take(5) as $record)
                    <div class="approval-card animate-slide-in">
                        <div class="approval-info">
                            <div class="approval-avatar">{{ strtoupper(substr($record->user->name, 0, 2)) }}</div>
                            <div class="approval-details">
                                <h4>{{ $record->user->name }}</h4>
                                <p>{{ $record->user->role->name }} • Requested at {{ $record->clock_in_requested_at->format('h:i A') }}</p>
                            </div>
                        </div>
                        <div class="approval-actions">
                            <form action="{{ route('attendance.approve', $record->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">✓ Approve</button>
                            </form>
                            <form action="{{ route('attendance.reject', $record->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">✕ Reject</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Recent Attendance --}}
    <div class="card">
        <div class="card-header">
            <h3>📅 Recent Attendance</h3>
            <a href="{{ route('attendance.history') }}" class="btn btn-outline btn-sm">View All →</a>
        </div>
        <div class="card-body" style="padding:0;">
            @if($recentRecords->count() > 0)
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentRecords as $record)
                                <tr>
                                    <td class="font-medium">{{ $record->date->format('d M Y') }}</td>
                                    <td>{{ $record->clock_in_time ? $record->clock_in_time->format('h:i A') : '—' }}</td>
                                    <td>{{ $record->clock_out_time ? $record->clock_out_time->format('h:i A') : '—' }}</td>
                                    <td class="font-semibold">{{ $record->total_minutes > 0 ? $record->formatted_total_time : '—' }}</td>
                                    <td>
                                        <span class="badge {{ $record->status_badge }}">
                                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h4>No attendance records yet</h4>
                    <p class="text-sm text-muted">Start by clocking in above!</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Team Members (for managers) --}}
    @if($teamStats && $teamStats['subordinates']->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3>👥 Your Team</h3>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Time Bank</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teamStats['subordinates'] as $sub)
                                <tr>
                                    <td class="font-medium">{{ $sub->name }}</td>
                                    <td>{{ $sub->role->name }}</td>
                                    <td class="font-semibold">{{ $sub->formatted_total_time }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
