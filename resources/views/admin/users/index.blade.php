@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div class="animate-fade-in">
    <div class="card">
        <div class="card-header">
            <h3>👥 All Users</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ Add User</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            <th>Supervisor</th>
                            <th>Time Bank</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:0.75rem;">
                                        <div class="user-avatar" style="width:32px;height:32px;font-size:0.7rem;">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <span class="font-medium">{{ $u->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $u->mobile }}</td>
                                <td><span class="badge badge-info">{{ $u->role->name }}</span></td>
                                <td>{{ $u->supervisor ? $u->supervisor->name : '—' }}</td>
                                <td class="font-semibold">{{ $u->formatted_total_time }}</td>
                                <td>
                                    @if($u->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-outline btn-sm">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
