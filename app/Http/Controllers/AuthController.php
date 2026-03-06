<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|min:10|max:15',
        ]);

        $mobile = preg_replace('/[^0-9]/', '', $request->mobile);

        // Check if user exists
        $user = User::where('mobile', $mobile)->where('is_active', true)->first();
        if (!$user) {
            return back()->withErrors(['mobile' => 'No active account found with this mobile number.'])->withInput();
        }

        $code = $this->otpService->sendOtp($mobile);

        // Store mobile in session for OTP verification page
        session(['otp_mobile' => $mobile]);

        return redirect()->route('login.verify')
            ->with('otp_code', $code) // Dev mode: show OTP
            ->with('success', 'OTP sent to ' . $mobile);
    }

    public function showVerifyOtp()
    {
        if (!session('otp_mobile')) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $mobile = session('otp_mobile');
        if (!$mobile) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired. Please try again.']);
        }

        if (!$this->otpService->verifyOtp($mobile, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        $user = User::where('mobile', $mobile)->where('is_active', true)->first();
        if (!$user) {
            return redirect()->route('login')->withErrors(['mobile' => 'Account not found.']);
        }

        Auth::login($user, true);
        session()->forget('otp_mobile');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
