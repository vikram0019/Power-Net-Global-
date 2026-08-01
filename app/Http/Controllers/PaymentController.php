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
        $this->applySearch($roiQuery, $request);

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
        $this->applySearch($query, $request);

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
        $this->applySearch($query, $request, sourceUser: true, level: true);

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
        $this->applySearch($query, $request, sourceUser: true, typeLabels: [
            'direct_reward' => 'Direct',
            'direct_reward_upline' => 'Upline Pass-up',
        ]);

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

    /**
     * Applies the shared "q" search-box filter (GET param, see
     * partials.payment-date-filter) across whichever columns a given
     * listing actually shows — amount and date always, plus sourceUser
     * name / level / type label where that tab has those columns.
     */
    private function applySearch(Builder $query, Request $request, bool $sourceUser = false, bool $level = false, array $typeLabels = []): void
    {
        $term = trim((string) $request->input('q', ''));

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $q) use ($term, $sourceUser, $level, $typeLabels) {
            $q->where('amount', 'like', "%{$term}%")
                ->orWhereRaw("DATE_FORMAT(created_at, '%d %b %Y') LIKE ?", ["%{$term}%"]);

            if ($level) {
                // The table displays "Level {n}", not the raw number, so
                // match against that same formatted string — otherwise
                // searching "Level 2" (matching what's on screen) never
                // matches a level column that only stores "2".
                $q->orWhereRaw("CONCAT('Level ', level) LIKE ?", ["%{$term}%"]);
            }

            if ($sourceUser) {
                $q->orWhereHas('sourceUser', fn (Builder $sq) => $sq->where('name', 'like', "%{$term}%"));
            }

            foreach ($typeLabels as $value => $label) {
                if (stripos($label, $term) !== false) {
                    $q->orWhere('type', $value);
                }
            }
        });
    }
}
