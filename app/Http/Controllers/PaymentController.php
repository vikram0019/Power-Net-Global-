<?php

namespace App\Http\Controllers;

use App\Models\IncomeTransaction;
use App\Models\Investment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return redirect()->route('payment.roi');
    }

    public function roi(Request $request)
    {
        $investments = Investment::where('user_id', $request->user()->id)->latest()->get();

        $roiTransactions = IncomeTransaction::where('user_id', $request->user()->id)
            ->where('type', 'monthly_roi')
            ->with('investment')
            ->orderBy('investment_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('investment_id');

        $totalRoi = (float) IncomeTransaction::where('user_id', $request->user()->id)
            ->where('type', 'monthly_roi')
            ->sum('amount');

        return view('dashboard.payment.roi', compact('investments', 'roiTransactions', 'totalRoi'));
    }

    public function rankReward(Request $request)
    {
        $transactions = IncomeTransaction::where('user_id', $request->user()->id)
            ->where('type', 'rank_reward')
            ->latest()
            ->paginate(20);

        $total = (float) IncomeTransaction::where('user_id', $request->user()->id)
            ->where('type', 'rank_reward')
            ->sum('amount');

        $rankHistory = $request->user()->rankHistory()->with('rank')->latest('achieved_at')->get();

        return view('dashboard.payment.rank-reward', compact('transactions', 'total', 'rankHistory'));
    }

    public function level(Request $request)
    {
        $transactions = IncomeTransaction::with('sourceUser')
            ->where('user_id', $request->user()->id)
            ->where('type', 'level_income')
            ->latest()
            ->paginate(20);

        $levelBreakdown = IncomeTransaction::where('user_id', $request->user()->id)
            ->where('type', 'level_income')
            ->selectRaw('level, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        $total = (float) IncomeTransaction::where('user_id', $request->user()->id)
            ->where('type', 'level_income')
            ->sum('amount');

        return view('dashboard.payment.level', compact('transactions', 'levelBreakdown', 'total'));
    }

    public function direct(Request $request)
    {
        $transactions = IncomeTransaction::with('sourceUser')
            ->where('user_id', $request->user()->id)
            ->whereIn('type', ['direct_reward', 'direct_reward_upline'])
            ->latest()
            ->paginate(20);

        $total = (float) IncomeTransaction::where('user_id', $request->user()->id)
            ->whereIn('type', ['direct_reward', 'direct_reward_upline'])
            ->sum('amount');

        $directTotal = (float) IncomeTransaction::where('user_id', $request->user()->id)
            ->where('type', 'direct_reward')
            ->sum('amount');

        $uplineTotal = (float) IncomeTransaction::where('user_id', $request->user()->id)
            ->where('type', 'direct_reward_upline')
            ->sum('amount');

        return view('dashboard.payment.direct', compact('transactions', 'total', 'directTotal', 'uplineTotal'));
    }
}
