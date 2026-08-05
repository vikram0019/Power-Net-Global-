<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Services\RoiPayoutService;

class AdminRoiController extends Controller
{
    public function index()
    {
        $activeInvestments = Investment::where('status', 'active')->count();
        $completedInvestments = Investment::where('status', 'completed')->count();
        $totalRoiPaid = (float) Investment::sum('roi_total_paid');

        $nextScheduledRun = now()->copy()->setTime(0, 5);
        if ($nextScheduledRun->lessThanOrEqualTo(now())) {
            $nextScheduledRun->addDay();
        }

        return view('admin.roi', compact('activeInvestments', 'completedInvestments', 'totalRoiPaid', 'nextScheduledRun'));
    }

    public function run(RoiPayoutService $roiPayoutService)
    {
        $count = $roiPayoutService->processDueInvestments();

        return back()->with('status', "Daily MPG processed for {$count} active investment(s).");
    }
}
