@extends('layouts.app')
@section('title', 'Manage Roles')

@section('content')
<div class="animate-fade-in">
    <div class="card">
        <div class="card-header">
            <h3>🔐 Roles & Permissions</h3>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">+ New Role</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Level</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td class="font-semibold">{{ $role->name }}</td>
                                <td>
                                    <span class="badge badge-info">Level {{ $role->level }}</span>
                                </td>
                                <td>{{ $role->users_count }} users</td>
                                <td>
                                    @foreach($role->permissions ?? [] as $perm)
                                        <span class="badge badge-secondary" style="margin:2px;">{{ str_replace('_', ' ', $perm) }}</span>
                                    @endforeach
                                    @if(empty($role->permissions))
                                        <span class="text-muted text-sm">No special permissions</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;gap:0.5rem;">
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-outline btn-sm">Edit</a>
                                        @if($role->level !== 1)
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                  onsubmit="return confirm('Delete this role?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
