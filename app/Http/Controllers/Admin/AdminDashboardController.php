<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('is_admin', false)->count();
        $activeUsers = User::where('is_admin', false)->where('status', 'active')->count();
        $totalInvested = (float) Investment::sum('amount');
        $totalWalletBalance = (float) Wallet::sum(DB::raw('deposit_balance + roi_balance + working_balance'));
        $pendingWithdrawals = Withdrawal::whereIn('status', ['otp_verified'])->count();
        $pendingWithdrawalAmount = (float) Withdrawal::whereIn('status', ['otp_verified'])->sum('amount');

        $signupsByDay = User::where('is_admin', false)
            ->where('created_at', '>=', now()->subDays(14))
            ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('total', 'd');

        $recentUsers = User::where('is_admin', false)->where('status', '!=', 'pending')->latest()->take(8)->get();
        $recentWithdrawals = Withdrawal::with('user')->whereIn('status', ['otp_verified'])->latest()->take(8)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'totalInvested',
            'totalWalletBalance',
            'pendingWithdrawals',
            'pendingWithdrawalAmount',
            'signupsByDay',
            'recentUsers',
            'recentWithdrawals'
        ));
    }
}
