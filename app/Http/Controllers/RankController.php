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
        $teamBusiness = $calculator->weightedTeamBusiness($user);

        $achievedRankIds = $user->rankHistory()->pluck('rank_id')->all();

        $ranks = $ranks->map(function (Rank $rank) use ($ownInvest, $teamBusiness, $achievedRankIds, $user) {
            $rank->is_achieved = in_array($rank->id, $achievedRankIds, true);
            $rank->is_current = $rank->id === $user->current_rank_id;
            $rank->invest_progress = min(100, $rank->own_invest_required > 0 ? ($ownInvest / $rank->own_invest_required) * 100 : 100);
            $rank->team_progress = min(100, $rank->team_business_required > 0 ? ($teamBusiness / $rank->team_business_required) * 100 : 100);

            return $rank;
        });

        return view('dashboard.rank', compact('ranks', 'ownInvest', 'teamBusiness'));
    }
}
