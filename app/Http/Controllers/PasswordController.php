<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\SendOtpEmailJob;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class PasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $otp = Str::random(6);

        $user->otp = $otp;
        $user->otp_expired_at = now()->addMinutes(10);
        $user->save();

        SendOtpEmailJob::dispatch($otp, $user->name, $user->email);
        return response()->json(['message' => 'Otp sent successfully']);
    }
    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->otp !== $request->otp || $user->otp_expired_at < now()) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
