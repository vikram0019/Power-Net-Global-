<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\FundRequest;
use App\Models\PaymentSetting;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WalletController extends Controller
{
    public function addFundPage(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet;

        $fundRequests = FundRequest::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $paymentSetting = PaymentSetting::first();
        $minimumInvestment = config('mlm.minimum_investment');

        return view('dashboard.wallet-add-fund', compact('wallet', 'fundRequests', 'paymentSetting', 'minimumInvestment'));
    }

    public function withdrawPage(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet;

        return view('dashboard.wallet-withdraw', compact('wallet'));
    }

    public function paymentHistory(Request $request)
    {
        $transactions = WalletTransaction::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(50);

        return view('dashboard.wallet-payment-history', compact('transactions'));
    }

    public function withdrawalRequests(Request $request)
    {
        $withdrawals = Withdrawal::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(50);

        return view('dashboard.wallet-withdrawal-requests', compact('withdrawals'));
    }

    public function submitFundRequest(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:' . config('mlm.minimum_investment')],
            'screenshot' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $request->file('screenshot')->store('fund-screenshots', 'public');

        FundRequest::create([
            'user_id' => $request->user()->id,
            'amount' => $validated['amount'],
            'screenshot_path' => $path,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Payment submitted for review. Your investment will be activated once admin approves it.');
    }

    public function requestWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'wallet_type' => ['required', 'in:roi,working,rank_reward'],
            'amount' => ['required', 'numeric', 'min:1'],
            'bep20_address' => ['required', 'string', 'max:100'],
        ]);

        $user = $request->user();
        $wallet = $user->wallet;
        $column = match ($validated['wallet_type']) {
            'roi' => 'roi_balance',
            'rank_reward' => 'rank_reward_balance',
            default => 'working_balance',
        };

        if ((float) $wallet->{$column} < (float) $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient fund'])->withInput();
        }

        $otp = (string) random_int(100000, 999999);

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'wallet_type' => $validated['wallet_type'],
            'amount' => $validated['amount'],
            'bep20_address' => $validated['bep20_address'],
            'status' => 'pending',
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp, 'Withdrawal Verification'));
        } catch (\Throwable $e) {
            report($e);
        }

        $flash = ['status' => 'Withdrawal requested. Enter the OTP sent to your email to confirm.'];

        if (app()->environment('local')) {
            $flash['dev_otp_hint'] = $otp;
        }

        return back()->with($flash + ['pending_withdrawal_id' => $withdrawal->id]);
    }

    public function verifyWithdrawalOtp(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate(['otp' => ['required', 'digits:6']]);

        if ($withdrawal->status !== 'pending' || $withdrawal->otp_code !== $request->otp || $withdrawal->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        $withdrawal->update([
            'status' => 'otp_verified',
            'otp_verified_at' => now(),
            'otp_code' => null,
        ]);

        return back()->with('status', 'OTP verified. Your withdrawal is now pending admin approval.');
    }
}
