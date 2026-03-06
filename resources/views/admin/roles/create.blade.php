@extends('layouts.app')
@section('title', 'Create Role')

@section('content')
<div class="animate-fade-in">
    <div class="card" style="max-width:600px;">
        <div class="card-header">
            <h3>+ Create New Role</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Role Name</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g., Director, Team Lead" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Hierarchy Level</label>
                    <input type="number" name="level" class="form-input" value="{{ old('level', 3) }}" min="1" max="100" required>
                    <p class="form-hint">Lower number = higher authority. Admin=1, HR=2, Manager=3, Employee=4.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Permissions</label>
                    @foreach($availablePermissions as $key => $label)
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" id="perm_{{ $key }}"
                                {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                            <label for="perm_{{ $key }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>

                <div style="display:flex;gap:1rem;">
                    <button type="submit" class="btn btn-primary">Create Role</button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
