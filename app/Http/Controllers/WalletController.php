<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Services\InvestmentService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $wallet = $user->wallet;

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(15, ['*'], 'transactions');

        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $minimumInvestment = config('mlm.minimum_investment');

        return view('dashboard.wallet', compact('wallet', 'transactions', 'withdrawals', 'minimumInvestment'));
    }

    public function addFund(Request $request, WalletService $walletService)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        // Payment gateway is stubbed for now — funds are credited instantly.
        $walletService->credit(
            $request->user(),
            'deposit',
            (float) $validated['amount'],
            'Fund added to wallet (mock payment)'
        );

        return back()->with('status', 'Funds added successfully. You can now invest.');
    }

    public function invest(Request $request, InvestmentService $investmentService)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:' . config('mlm.minimum_investment')],
        ]);

        try {
            $investmentService->invest($request->user(), (float) $validated['amount']);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return back()->with('status', 'Investment placed successfully. Rewards have been distributed to your upline.');
    }

    public function requestWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'wallet_type' => ['required', 'in:roi,working'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $user = $request->user();
        $wallet = $user->wallet;
        $column = $validated['wallet_type'] === 'roi' ? 'roi_balance' : 'working_balance';

        if ((float) $wallet->{$column} < (float) $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient balance in the selected wallet.']);
        }

        $otp = (string) random_int(100000, 999999);

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'wallet_type' => $validated['wallet_type'],
            'amount' => $validated['amount'],
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
