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
        $ranks = Rank::withCumulativeBucketTargets();

        $ownInvest = $user->totalInvested();
        $legs = $calculator->legBreakdown($user);
        $directLegCount = $user->directReferrals()->count();
        $unlimitedLegs = (int) config('mlm.unlimited_legs');

        // pluck() and plain (uncast) integer attributes return raw driver
        // values, which come back as strings under some PDO configurations —
        // cast explicitly so strict comparisons below can't silently fail.
        $achievedRankIds = $user->rankHistory()->pluck('rank_id')->map(fn ($id) => (int) $id)->all();
        $currentRankId = $user->current_rank_id !== null ? (int) $user->current_rank_id : null;

        $ranks = $ranks->map(function (Rank $rank) use ($ownInvest, $legs, $directLegCount, $unlimitedLegs, $achievedRankIds, $currentRankId) {
            $rank->is_achieved = in_array($rank->id, $achievedRankIds, true);
            $rank->is_current = $rank->id === $currentRankId;
            $rank->invest_progress = min(100, $rank->own_invest_required > 0 ? ($ownInvest / $rank->own_invest_required) * 100 : 100);

            $rank->direct_legs_actual = $directLegCount;
            $rank->direct_legs_unlimited = $rank->legs_open >= $unlimitedLegs;
            $rank->direct_legs_progress = $rank->direct_legs_unlimited
                ? 100
                : min(100, $rank->legs_open > 0 ? ($directLegCount / $rank->legs_open) * 100 : 100);

            // Start's rest_target is always 0 (see Rank::withCumulativeBucketTargets()),
            // so it never shows a Rest Legs row — that's what makes it a
            // 2-leg-only rank without any special-case display logic here.
            $rank->has_rest_bucket = $rank->rest_target > 0;

            $rank->power_actual = $legs['power'];
            $rank->second_actual = $legs['second'];
            $rank->rest_actual = $legs['rest'];

            $powerProgress = min(100, $rank->power_target > 0 ? ($legs['power'] / $rank->power_target) * 100 : 100);
            $secondProgress = min(100, $rank->second_target > 0 ? ($legs['second'] / $rank->second_target) * 100 : 100);
            $restProgress = min(100, $rank->rest_target > 0 ? ($legs['rest'] / $rank->rest_target) * 100 : 100);

            $rank->power_progress = $powerProgress;
            $rank->second_progress = $secondProgress;
            $rank->rest_progress = $restProgress;

            // All buckets must independently clear 100% to qualify, so
            // overall progress is whichever is furthest behind.
            $rank->team_progress = $rank->has_rest_bucket
                ? min($powerProgress, $secondProgress, $restProgress)
                : min($powerProgress, $secondProgress);

            return $rank;
        });

        return view('dashboard.rank', compact('ranks', 'ownInvest', 'legs'));
    }
}
