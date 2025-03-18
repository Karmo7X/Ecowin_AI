<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class PasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $otp = rand(100000, 999999);

        $user->otp = $otp;
        $user->otp_expired_at = now()->addMinutes(10);
        $user->save();

        SendOtpEmailJob::dispatch($otp, $user->name, $user->email);
        return response()->json([
            'status' => 200,
            'message' => 'Otp sent successfully',
            'otp'=>$otp
        ],200);
    }

    public function confirmOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->otp !== $request->otp || $user->otp_expired_at < now()) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        $user->is_otp_verified = true;
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->save();

        return response()->json(['message' => 'OTP verified successfully']);
    }
    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->is_otp_verified) {
            return response()->json([
                'status' => 400,
                'message' => 'OTP confirmation required before password reset',
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->is_otp_verified = false; // Reset flag after password change
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully',
        ]);
    }
}
