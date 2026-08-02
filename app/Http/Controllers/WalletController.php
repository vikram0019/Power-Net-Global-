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

        $roiWindowOpen = in_array((int) now()->day, [1, 2], true);
        $weeklyWindowOpen = now()->isSunday();

        return view('dashboard.wallet-withdraw', compact('wallet', 'roiWindowOpen', 'weeklyWindowOpen'));
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

        return redirect()->route('wallet.add-fund')->with('status', 'Payment submitted for review. Your investment will be activated once admin approves it.');
    }

    public function requestWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'wallet_type' => ['required', 'in:roi,working,rank_reward'],
            'amount' => ['required', 'numeric', 'min:' . config('mlm.minimum_withdrawal')],
            'bep20_address' => ['required', 'string', 'max:100'],
        ]);

        if ($validated['wallet_type'] === 'roi') {
            if (!in_array((int) now()->day, [1, 2], true)) {
                return redirect()->route('wallet.withdraw.page')->withErrors(['amount' => 'ROI Income can only be withdrawn on the 1st or 2nd day of the month.'])->withInput();
            }
        } elseif (!now()->isSunday()) {
            return redirect()->route('wallet.withdraw.page')->withErrors(['amount' => 'Working Income and Rank & Reward Income can only be withdrawn on Sundays.'])->withInput();
        }

        $user = $request->user();
        $wallet = $user->wallet;
        $column = match ($validated['wallet_type']) {
            'roi' => 'roi_balance',
            'rank_reward' => 'rank_reward_balance',
            default => 'working_balance',
        };

        if ((float) $wallet->{$column} < (float) $validated['amount']) {
            return redirect()->route('wallet.withdraw.page')->withErrors(['amount' => 'Insufficient fund'])->withInput();
        }

        $otp = (string) random_int(100000, 999999);

        $feePercent = in_array($validated['wallet_type'], config('mlm.withdrawal_fee_wallet_types'), true)
            ? config('mlm.withdrawal_fee_percent')
            : 0;
        $feeAmount = round($validated['amount'] * $feePercent / 100, 2);
        $netAmount = $validated['amount'] - $feeAmount;

        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'wallet_type' => $validated['wallet_type'],
            'amount' => $validated['amount'],
            'fee_amount' => $feeAmount,
            'net_amount' => $netAmount,
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

        return redirect()->route('wallet.withdraw.page')->with($flash + ['pending_withdrawal_id' => $withdrawal->id]);
    }

    public function verifyWithdrawalOtp(Request $request, Withdrawal $withdrawal)
    {
        // user_id has no explicit cast, so it comes back as whatever the raw
        // driver returns — a string under this server's PDO config, while
        // $request->user()->id (a primary key) is a properly-cast int. Cast
        // explicitly so the strict comparison doesn't wrongly reject the
        // legitimate owner.
        if ((int) $withdrawal->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $request->validate(['otp' => ['required', 'digits:6']]);

        if ($withdrawal->status !== 'pending' || $withdrawal->otp_code !== $request->otp || $withdrawal->otp_expires_at->isPast()) {
            return redirect()->route('wallet.withdraw.page')->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        $withdrawal->update([
            'status' => 'otp_verified',
            'otp_verified_at' => now(),
            'otp_code' => null,
        ]);

        // Redirect explicitly rather than using back() — back() depends on the
        // HTTP Referer header, which some browsers/security software strip,
        // silently falling through to the home page instead of this one.
        return redirect()->route('wallet.withdraw.page')->with('status', 'OTP verified. Your withdrawal is now pending admin approval.');
    }
}
