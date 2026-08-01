<?php

namespace App\Http\Controllers;

use App\Models\IncomeTransaction;
use App\Models\Investment;
use Illuminate\Database\Eloquent\Builder;
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

        $roiQuery = IncomeTransaction::where('user_id', $request->user()->id)->where('type', 'monthly_roi');
        $this->applyDateRange($roiQuery, $request);

        $roiTransactions = (clone $roiQuery)
            ->with('investment')
            ->orderBy('investment_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('investment_id');

        $totalRoi = (float) (clone $roiQuery)->sum('amount');

        return view('dashboard.payment.roi', compact('investments', 'roiTransactions', 'totalRoi'));
    }

    public function rankReward(Request $request)
    {
        $query = IncomeTransaction::where('user_id', $request->user()->id)->where('type', 'rank_reward');
        $this->applyDateRange($query, $request);

        $transactions = (clone $query)->latest()->paginate(20)->withQueryString();
        $total = (float) (clone $query)->sum('amount');

        $rankHistory = $request->user()->rankHistory()->with('rank')->latest('achieved_at')->get();

        return view('dashboard.payment.rank-reward', compact('transactions', 'total', 'rankHistory'));
    }

    public function level(Request $request)
    {
        $query = IncomeTransaction::with('sourceUser')
            ->where('user_id', $request->user()->id)
            ->where('type', 'level_income');
        $this->applyDateRange($query, $request);

        $transactions = (clone $query)->latest()->paginate(20)->withQueryString();

        $levelBreakdown = (clone $query)
            ->selectRaw('level, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        $total = (float) (clone $query)->sum('amount');

        return view('dashboard.payment.level', compact('transactions', 'levelBreakdown', 'total'));
    }

    public function direct(Request $request)
    {
        $query = IncomeTransaction::with('sourceUser')
            ->where('user_id', $request->user()->id)
            ->whereIn('type', ['direct_reward', 'direct_reward_upline']);
        $this->applyDateRange($query, $request);

        $transactions = (clone $query)->latest()->paginate(20)->withQueryString();
        $total = (float) (clone $query)->sum('amount');
        $directTotal = (float) (clone $query)->where('type', 'direct_reward')->sum('amount');
        $uplineTotal = (float) (clone $query)->where('type', 'direct_reward_upline')->sum('amount');

        return view('dashboard.payment.direct', compact('transactions', 'total', 'directTotal', 'uplineTotal'));
    }

    /**
     * Applies the shared "from"/"to" date-range filter (GET params, see
     * partials.payment-date-filter) used across all 4 Payment listing tabs.
     */
    private function applyDateRange(Builder $query, Request $request, string $column = 'created_at'): void
    {
        if ($request->filled('from')) {
            $query->whereDate($column, '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate($column, '<=', $request->input('to'));
        }
    }
}
