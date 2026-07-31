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

    public function verifyReferralCode(Request $request)
    {
        $code = trim((string) $request->query('code', ''));

        if ($code === '') {
            return response()->json(['valid' => false, 'message' => 'Enter a referral code.']);
        }

        $sponsor = User::where('referral_code', $code)->first();

        if (! $sponsor) {
            return response()->json(['valid' => false, 'message' => 'Invalid referral code.']);
        }

        if (! $sponsor->isActiveSponsor()) {
            return response()->json(['valid' => false, 'message' => 'This referral code belongs to an account that is not yet active.']);
        }

        return response()->json(['valid' => true, 'message' => 'This is a valid referral code.']);
    }

    /**
     * Nothing is written to the users table here — the submitted data is
     * held in the session until OTP verification succeeds, so an
     * abandoned/never-verified signup leaves no trace in the database.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'referral_code' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    $sponsor = User::where('referral_code', $value)->first();

                    if (! $sponsor) {
                        $fail('This referral code does not exist.');
                    } elseif (! $sponsor->isActiveSponsor()) {
                        $fail('This referral code belongs to an account that is not yet active. Ask your sponsor to wait for admin approval.');
                    }
                },
            ],
        ]);

        $sponsor = User::where('referral_code', $validated['referral_code'])->firstOrFail();

        $otp = (string) random_int(100000, 999999);

        session(['pending_signup' => [
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'sponsor_id' => $sponsor->id,
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10)->toDateTimeString(),
        ]]);

        $this->sendOtpMail($validated['email'], $otp, 'Account Verification');

        return redirect()->route('signup.otp');
    }

    public function showOtp()
    {
        $pending = session('pending_signup');

        if (! $pending) {
            return redirect()->route('signup');
        }

        return view('auth.signup-otp', ['email' => $pending['email']]);
    }

    public function verifyOtp(Request $request)
    {
        $pending = session('pending_signup');

        if (! $pending) {
            return redirect()->route('signup');
        }

        $request->validate(['otp' => ['required', 'digits:6']]);

        if ($pending['otp_code'] !== $request->otp || now()->greaterThan($pending['otp_expires_at'])) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        // Defensive re-check: someone else could have registered this exact
        // email/mobile while this signup sat unverified in session.
        if (User::where('email', $pending['email'])->orWhere('mobile', $pending['mobile'])->exists()) {
            session()->forget('pending_signup');

            return redirect()->route('signup')->withErrors([
                'email' => 'This email or mobile was just registered by someone else. Please sign up again.',
            ]);
        }

        $user = User::create([
            'name' => $pending['name'],
            'mobile' => $pending['mobile'],
            'email' => $pending['email'],
            'password' => $pending['password'],
            'referral_code' => User::generateReferralCode(),
            'sponsor_id' => $pending['sponsor_id'],
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        Wallet::firstOrCreate(['user_id' => $user->id]);

        session()->forget(['pending_signup', 'dev_otp_hint']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Your email is verified! Your account is now active.');
    }

    public function resendOtp()
    {
        $pending = session('pending_signup');

        if (! $pending) {
            return redirect()->route('signup');
        }

        $otp = (string) random_int(100000, 999999);
        $pending['otp_code'] = $otp;
        $pending['otp_expires_at'] = now()->addMinutes(10)->toDateTimeString();
        session(['pending_signup' => $pending]);

        $this->sendOtpMail($pending['email'], $otp, 'Account Verification');

        return back()->with('status', 'A new OTP has been sent to your email.');
    }

    private function sendOtpMail(string $email, string $otp, string $purpose): void
    {
        try {
            Mail::to($email)->send(new OtpMail($otp, $purpose));
        } catch (\Throwable $e) {
            report($e);
        }

        if (app()->environment('local')) {
            session(['dev_otp_hint' => $otp]);
        }
    }
}
