@extends('layouts.auth')
@section('title', 'Verify OTP')

@section('content')
<div class="auth-card animate-fade-in" x-data="otpInput">
    <div class="auth-logo">
        <div class="logo-icon">M</div>
        <h1>Verify OTP</h1>
        <p class="company-name">Enter the 6-digit code</p>
    </div>

    @if(session('otp_code'))
        <div class="dev-otp-banner">
            <p class="text-sm text-muted" style="margin-bottom:0.25rem;">🔧 Dev Mode — Your OTP is:</p>
            <strong>{{ session('otp_code') }}</strong>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
    @endif

    <p class="text-sm text-muted text-center mb-3">
        OTP sent to <strong>{{ session('otp_mobile') }}</strong>
    </p>

    <form action="{{ route('login.verifyOtp') }}" method="POST">
        @csrf
        <input type="hidden" name="otp" :value="fullOtp">

        <div class="otp-inputs" @paste="handlePaste($event)">
            <template x-for="(digit, index) in digits" :key="index">
                <input type="text" class="otp-input" maxlength="1" inputmode="numeric"
                       :value="digit"
                       @input="handleInput(index, $event)"
                       @keydown="handleKeydown(index, $event)">
            </template>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;" :disabled="fullOtp.length < 6">
            Verify & Login →
        </button>
    </form>

    <div style="text-align:center; margin-top:1.5rem;">
        <a href="{{ route('login') }}" class="text-sm">← Back to login</a>
    </div>
</div>
@endsection
