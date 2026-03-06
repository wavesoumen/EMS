<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="MUKTI EMS — Employee Management System Dashboard">
    <title>@yield('title', 'Dashboard') — MUKTI EMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="sidebar">
    <div class="app-layout">
        {{-- Sidebar Overlay (mobile) --}}
        <div class="sidebar-overlay" :class="{ 'active': open }" @click="open = false"></div>

        {{-- Sidebar --}}
        <aside class="sidebar" :class="{ 'open': open }">
            <div class="sidebar-brand">
                <div class="brand-icon">M</div>
                <div>
                    <h2>MUKTI</h2>
                    <div class="brand-sub">EMS Platform</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span>
                    Dashboard
                </a>

                <a href="{{ route('attendance.history') }}" class="nav-link {{ request()->routeIs('attendance.history') ? 'active' : '' }}">
                    <span class="nav-icon">📅</span>
                    My Attendance
                </a>

                @if(auth()->user()->hasPermission('approve_clockin'))
                    <div class="nav-section-title">Team</div>
                    <a href="{{ route('attendance.pending') }}" class="nav-link {{ request()->routeIs('attendance.pending') ? 'active' : '' }}">
                        <span class="nav-icon">✅</span>
                        Pending Approvals
                        @php
                            $pendingCount = \App\Models\AttendanceRecord::whereIn('user_id',
                                \App\Models\User::where('supervisor_id', auth()->id())->pluck('id')
                            )->where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="nav-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->isAdmin())
                    <div class="nav-section-title">Administration</div>
                    <a href="{{ route('admin.company.edit') }}" class="nav-link {{ request()->routeIs('admin.company.*') ? 'active' : '' }}">
                        <span class="nav-icon">🏢</span>
                        Company Settings
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <span class="nav-icon">🔐</span>
                        Manage Roles
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        Manage Users
                    </a>
                @endif
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="main-content">
            {{-- Topbar --}}
            <header class="topbar">
                <div class="topbar-left" style="display:flex;align-items:center;gap:1rem;">
                    <button class="menu-toggle" @click="toggle()">☰</button>
                    <h1>@yield('title', 'Dashboard')</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div>
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ auth()->user()->role->name ?? 'User' }}</div>
                        </div>
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Logout</button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <div class="page-content">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success">✓ {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">✕ {{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
