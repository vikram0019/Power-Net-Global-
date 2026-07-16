<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function show(Request $request)
    {
        $referral = $request->query('ref', '');

        return view('auth.signup', compact('referral'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'referral_code' => ['required', 'string', 'exists:users,referral_code'],
        ]);

        $sponsor = User::where('referral_code', $validated['referral_code'])->firstOrFail();

        $user = User::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'referral_code' => User::generateReferralCode(),
            'sponsor_id' => $sponsor->id,
            'status' => 'pending',
        ]);

        $this->sendOtp($user, 'Account Verification');

        session(['pending_user_id' => $user->id]);

        return redirect()->route('signup.otp');
    }

    public function showOtp()
    {
        $userId = session('pending_user_id');

        if (! $userId || ! ($user = User::find($userId))) {
            return redirect()->route('signup');
        }

        return view('auth.signup-otp', compact('user'));
    }

    public function verifyOtp(Request $request)
    {
        $userId = session('pending_user_id');

        if (! $userId || ! ($user = User::find($userId))) {
            return redirect()->route('signup');
        }

        $request->validate(['otp' => ['required', 'digits:6']]);

        if (! $user->otp_code || $user->otp_code !== $request->otp || ! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        $user->update([
            'status' => 'active',
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        Wallet::firstOrCreate(['user_id' => $user->id]);

        session()->forget(['pending_user_id', 'dev_otp_hint']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Welcome to PowerNetGlobal! Your account is verified.');
    }

    public function resendOtp()
    {
        $userId = session('pending_user_id');

        if (! $userId || ! ($user = User::find($userId))) {
            return redirect()->route('signup');
        }

        $this->sendOtp($user, 'Account Verification');

        return back()->with('status', 'A new OTP has been sent to your email.');
    }

    private function sendOtp(User $user, string $purpose): void
    {
        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp, $purpose));
        } catch (\Throwable $e) {
            report($e);
        }

        if (app()->environment('local')) {
            session(['dev_otp_hint' => $otp]);
        }
    }
}
