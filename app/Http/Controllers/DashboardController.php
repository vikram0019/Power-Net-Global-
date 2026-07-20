<?php

namespace App\Http\Controllers;

use App\Models\IncomeTransaction;
use App\Services\TeamBusinessCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request, TeamBusinessCalculator $calculator)
    {
        $user = $request->user();

        // Upline (2%) pass-up income is intentionally not surfaced on the member dashboard.
        $visibleIncome = IncomeTransaction::where('user_id', $user->id)
            ->where('type', '!=', 'direct_reward_upline');

        $totalInvested = $user->totalInvested();
        $totalIncome = (float) (clone $visibleIncome)->sum('amount');
        $incomeByType = (clone $visibleIncome)
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $teamSize = $calculator->totalTeamSize($user);
        $directCount = $user->directReferrals()->count();

        $monthly = (clone $visibleIncome)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('SUM(amount) as total'))
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $recentIncome = (clone $visibleIncome)
            ->with('sourceUser')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.overview', compact(
            'totalInvested',
            'totalIncome',
            'incomeByType',
            'teamSize',
            'directCount',
            'monthly',
            'recentIncome'
        ));
    }
}
