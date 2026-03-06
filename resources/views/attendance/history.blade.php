@extends('layouts.app')
@section('title', 'My Attendance History')

@section('content')
<div class="animate-fade-in">
    <div class="card">
        <div class="card-header">
            <h3>📅 Attendance History</h3>
            <div>
                <span class="badge badge-info">Time Bank: {{ $user->formatted_total_time }}</span>
            </div>
        </div>
        <div class="card-body" style="padding:0;">
            @if($records->count() > 0)
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Duration</th>
                                <th>Approved By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td class="font-medium">{{ $record->date->format('d M Y, D') }}</td>
                                    <td>{{ $record->clock_in_time ? $record->clock_in_time->format('h:i A') : '—' }}</td>
                                    <td>{{ $record->clock_out_time ? $record->clock_out_time->format('h:i A') : '—' }}</td>
                                    <td class="font-semibold">{{ $record->total_minutes > 0 ? $record->formatted_total_time : '—' }}</td>
                                    <td>{{ $record->approver ? $record->approver->name : '—' }}</td>
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
                <div class="card-footer">
                    {{ $records->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h4>No attendance records</h4>
                    <p class="text-sm text-muted">Your attendance history will appear here once you start clocking in.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
