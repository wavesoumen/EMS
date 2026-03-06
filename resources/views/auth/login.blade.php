@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="auth-card animate-fade-in">
    <div class="auth-logo">
        <div class="logo-icon">M</div>
        <h1>MUKTI EMS</h1>
        <p class="company-name">Employee Management System</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
    @endif

    <form action="{{ route('login.sendOtp') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">Mobile Number</label>
            <input type="tel" name="mobile" class="form-input" placeholder="Enter your mobile number"
                   value="{{ old('mobile') }}" required autofocus maxlength="15"
                   style="font-size:1.1rem; letter-spacing:1px;">
            <p class="form-hint">Enter the mobile number registered with your account</p>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
            Send OTP →
        </button>
    </form>

    <div style="text-align:center; margin-top:1.5rem;">
        <p class="text-sm text-muted">Secured by MUKTI • v1.0</p>
    </div>
</div>
@endsection
