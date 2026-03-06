@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="animate-fade-in">
    <div class="grid-2">
        {{-- Edit Form --}}
        <div class="card">
            <div class="card-header">
                <h3>Edit User: {{ $editUser->name }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $editUser->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $editUser->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <input type="tel" name="mobile" class="form-input" value="{{ old('mobile', $editUser->mobile) }}" required maxlength="15">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $editUser->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }} (Level {{ $role->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Supervisor</label>
                        <select name="supervisor_id" class="form-select">
                            <option value="">No supervisor</option>
                            @foreach($supervisors as $sup)
                                <option value="{{ $sup->id }}" {{ old('supervisor_id', $editUser->supervisor_id) == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }} ({{ $sup->role->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" {{ old('is_active', $editUser->is_active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('is_active', $editUser->is_active) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div style="display:flex;gap:1rem;">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
                    </div>
                </form>

                @if($editUser->id !== auth()->id())
                    <hr style="margin:1.5rem 0;border-color:var(--gray-200);">
                    <form action="{{ route('admin.users.destroy', $editUser->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete User</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- User Info Sidebar --}}
        <div>
            {{-- Time Bank --}}
            <div class="time-bank-display mb-3">
                <div class="time-bank-label">Time Bank</div>
                <div class="time-bank-value">{{ $editUser->formatted_total_time }}</div>
                <div class="time-bank-sub">Total accumulated work time</div>
            </div>

            {{-- Recent Attendance --}}
            <div class="card">
                <div class="card-header">
                    <h3>📅 Recent Attendance</h3>
                </div>
                <div class="card-body" style="padding:0;">
                    @if($recentAttendance->count() > 0)
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendance as $record)
                                        <tr>
                                            <td>{{ $record->date->format('d M') }}</td>
                                            <td class="font-semibold">{{ $record->total_minutes > 0 ? $record->formatted_total_time : '—' }}</td>
                                            <td><span class="badge {{ $record->status_badge }}">{{ ucfirst($record->status) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state" style="padding:1.5rem;">
                            <p class="text-sm text-muted">No attendance records.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
