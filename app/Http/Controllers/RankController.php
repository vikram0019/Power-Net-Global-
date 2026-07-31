<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use App\Services\TeamBusinessCalculator;
use Illuminate\Http\Request;

class RankController extends Controller
{
    public function index(Request $request, TeamBusinessCalculator $calculator)
    {
        $user = $request->user();
        $ranks = Rank::ordered()->get();

        $ownInvest = $user->totalInvested();
        $standardTeamBusiness = $calculator->weightedTeamBusiness($user);
        $startTeamBusiness = $calculator->twoLegWeightedBusiness($user);

        $achievedRankIds = $user->rankHistory()->pluck('rank_id')->all();

        $ranks = $ranks->map(function (Rank $rank) use ($ownInvest, $standardTeamBusiness, $startTeamBusiness, $achievedRankIds, $user) {
            // Start only requires 2 legs open, so its progress/display uses
            // just the top 2 legs at 50/50 — matches RankService's actual
            // qualification rule. Every other rank uses the standard
            // Power/2nd/rest 50/30/20 figure.
            $teamBusiness = $rank->code === 'start' ? $startTeamBusiness : $standardTeamBusiness;

            $rank->is_achieved = in_array($rank->id, $achievedRankIds, true);
            $rank->is_current = $rank->id === $user->current_rank_id;
            $rank->invest_progress = min(100, $rank->own_invest_required > 0 ? ($ownInvest / $rank->own_invest_required) * 100 : 100);
            // Display uses each rank's own stated amount, not the cumulative total
            // that RankService actually qualifies against — keeps the card matching
            // the number members have always seen; only the promotion logic is cumulative.
            $rank->team_progress = min(100, $rank->team_business_required > 0 ? ($teamBusiness / $rank->team_business_required) * 100 : 100);
            $rank->team_business_display = $teamBusiness;

            return $rank;
        });

        return view('dashboard.rank', compact('ranks', 'ownInvest', 'standardTeamBusiness'));
    }
}
