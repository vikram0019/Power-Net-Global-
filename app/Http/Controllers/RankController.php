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
        $legs = $calculator->legBreakdown($user);
        $startTeamBusiness = $calculator->twoLegWeightedBusiness($user);

        [$powerWeight, $secondWeight, $restWeight] = config('mlm.leg_weights');

        // pluck() and plain (uncast) integer attributes return raw driver
        // values, which come back as strings under some PDO configurations —
        // cast explicitly so strict comparisons below can't silently fail.
        $achievedRankIds = $user->rankHistory()->pluck('rank_id')->map(fn ($id) => (int) $id)->all();
        $currentRankId = $user->current_rank_id !== null ? (int) $user->current_rank_id : null;

        $ranks = $ranks->map(function (Rank $rank) use ($ownInvest, $legs, $startTeamBusiness, $achievedRankIds, $currentRankId, $powerWeight, $secondWeight, $restWeight) {
            $rank->is_achieved = in_array($rank->id, $achievedRankIds, true);
            $rank->is_current = $rank->id === $currentRankId;
            $rank->invest_progress = min(100, $rank->own_invest_required > 0 ? ($ownInvest / $rank->own_invest_required) * 100 : 100);

            if ($rank->code === 'start') {
                // Start only requires 2 legs open, so its progress uses just
                // the top 2 legs combined at 50/50 — matches RankService's
                // actual qualification rule for this rank.
                $rank->uses_buckets = false;
                $rank->team_business_display = $startTeamBusiness;
                $rank->team_progress = min(100, $rank->team_business_required > 0 ? ($startTeamBusiness / $rank->team_business_required) * 100 : 100);
            } else {
                // Every other rank's own stated amount is split into 3
                // independent Power/2nd/Rest targets — matches
                // RankService's actual qualification rule.
                $rank->uses_buckets = true;
                $required = (float) $rank->team_business_required;

                $rank->power_target = $required * $powerWeight / 100;
                $rank->second_target = $required * $secondWeight / 100;
                $rank->rest_target = $required * $restWeight / 100;
                $rank->power_actual = $legs['power'];
                $rank->second_actual = $legs['second'];
                $rank->rest_actual = $legs['rest'];

                $powerProgress = min(100, $rank->power_target > 0 ? ($legs['power'] / $rank->power_target) * 100 : 100);
                $secondProgress = min(100, $rank->second_target > 0 ? ($legs['second'] / $rank->second_target) * 100 : 100);
                $restProgress = min(100, $rank->rest_target > 0 ? ($legs['rest'] / $rank->rest_target) * 100 : 100);

                $rank->power_progress = $powerProgress;
                $rank->second_progress = $secondProgress;
                $rank->rest_progress = $restProgress;

                // All three buckets must independently clear 100% to
                // qualify, so overall progress is whichever is furthest behind.
                $rank->team_progress = min($powerProgress, $secondProgress, $restProgress);
            }

            return $rank;
        });

        return view('dashboard.rank', compact('ranks', 'ownInvest', 'legs'));
    }
}
