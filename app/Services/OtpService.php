<?php

namespace App\Services;

use App\Models\Otp;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generate and store a 6-digit OTP for the given mobile number.
     * In dev mode, the OTP is returned directly (no SMS sent).
     */
    public function sendOtp(string $mobile): string
    {
        // Invalidate any existing OTPs for this mobile
        Otp::where('mobile', $mobile)->where('verified', false)->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::create([
            'mobile' => $mobile,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
            'verified' => false,
        ]);

        // TODO: Integrate SMS gateway (Twilio, MSG91, etc.)
        // For now, OTP is shown on-screen in dev mode.

        return $code;
    }

    /**
     * Verify the OTP for the given mobile number.
     */
    public function verifyOtp(string $mobile, string $code): bool
    {
        $otp = Otp::where('mobile', $mobile)
            ->where('code', $code)
            ->where('verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$otp) {
            return false;
        }

        $otp->update(['verified' => true]);
        return true;
    }
}
