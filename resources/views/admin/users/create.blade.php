@extends('layouts.app')
@section('title', 'Add User')

@section('content')
<div class="animate-fade-in">
    <div class="card" style="max-width:600px;">
        <div class="card-header">
            <h3>+ Add New User</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required placeholder="Employee full name">
                </div>

                <div class="form-group">
                    <label class="form-label">Mobile Number</label>
                    <input type="tel" name="mobile" class="form-input" value="{{ old('mobile') }}" required placeholder="10-digit mobile number" maxlength="15">
                    <p class="form-hint">This will be used for OTP login</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Select role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} (Level {{ $role->level }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Supervisor</label>
                    <select name="supervisor_id" class="form-select">
                        <option value="">No supervisor (top-level)</option>
                        @foreach($supervisors as $sup)
                            <option value="{{ $sup->id }}" {{ old('supervisor_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }} ({{ $sup->role->name }})
                            </option>
                        @endforeach
                    </select>
                    <p class="form-hint">Select who will approve this user's clock-in requests</p>
                </div>

                <div style="display:flex;gap:1rem;">
                    <button type="submit" class="btn btn-primary">Create User</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
