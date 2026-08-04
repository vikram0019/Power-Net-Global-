<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\IncomeTransaction;
use App\Services\TeamBusinessCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request, TeamBusinessCalculator $calculator)
    {
        $user = $request->user();

        $totalInvested = $user->totalInvested();
        $totalIncome = (float) IncomeTransaction::where('user_id', $user->id)->sum('amount');
        $incomeByType = IncomeTransaction::where('user_id', $user->id)
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $teamSize = $calculator->totalTeamSize($user);
        $directCount = $user->directReferrals()->count();
        $wallet = $user->wallet;

        $monthly = IncomeTransaction::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('SUM(amount) as total'))
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $announcements = Announcement::active()->latest()->get();

        $achievedToday = $user->rankHistory()
            ->where('achieved_at', '>=', now()->subDays(5)->startOfDay())
            ->with('rank')
            ->latest('achieved_at')
            ->first();

        return view('dashboard.overview', compact(
            'totalInvested',
            'totalIncome',
            'incomeByType',
            'teamSize',
            'directCount',
            'wallet',
            'monthly',
            'announcements',
            'achievedToday'
        ));
    }
}
