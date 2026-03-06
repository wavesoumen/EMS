@extends('layouts.app')
@section('title', 'Pending Approvals')

@section('content')
<div class="animate-fade-in">
    <div class="card">
        <div class="card-header">
            <h3>⏳ Pending Clock-In Approvals</h3>
            <span class="badge badge-warning">{{ $records->total() }} pending</span>
        </div>
        <div class="card-body">
            @if($records->count() > 0)
                @foreach($records as $record)
                    <div class="approval-card animate-slide-in">
                        <div class="approval-info">
                            <div class="approval-avatar">{{ strtoupper(substr($record->user->name, 0, 2)) }}</div>
                            <div class="approval-details">
                                <h4>{{ $record->user->name }}</h4>
                                <p>
                                    <span class="badge badge-secondary">{{ $record->user->role->name }}</span>
                                    &nbsp;• Requested: {{ $record->clock_in_requested_at->format('h:i A, d M Y') }}
                                </p>
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

                <div style="margin-top:1.5rem;">
                    {{ $records->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">✅</div>
                    <h4>All caught up!</h4>
                    <p class="text-sm text-muted">No pending clock-in requests from your team.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
