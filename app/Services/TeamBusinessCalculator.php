<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\User;

class TeamBusinessCalculator
{
    /**
     * Collect the ids of every descendant of the given user id, walking the
     * tree one level at a time (one query per level, not per node). Portable
     * to any MySQL/MariaDB version — some shared-hosting DB servers (e.g.
     * MySQL 5.6) don't support recursive CTEs (WITH RECURSIVE, MySQL 8.0+).
     */
    private function subtreeIds(int $rootId, ?int $maxDepth = null): array
    {
        $ids = [];
        $frontier = [$rootId];
        $depth = 0;

        while ($frontier !== [] && ($maxDepth === null || $depth < $maxDepth)) {
            $children = User::whereIn('sponsor_id', $frontier)->pluck('id')->all();

            if ($children === []) {
                break;
            }

            $ids = array_merge($ids, $children);
            $frontier = $children;
            $depth++;
        }

        return $ids;
    }

    public function legBusiness(User $legRoot): float
    {
        $ids = array_merge([$legRoot->id], $this->subtreeIds($legRoot->id));

        return (float) Investment::whereIn('user_id', $ids)->sum('amount');
    }

    /**
     * Rank-qualification business: power leg (largest) weighted 50%, 2nd leg weighted
     * 30%, and every remaining leg (3rd, 4th, 5th...) summed together and weighted 20%
     * — so, unlike a strict top-3 cutoff, legs beyond the 2nd always contribute
     * something. Weight percentages come from config('mlm.leg_weights').
     */
    public function weightedTeamBusiness(User $user): float
    {
        $legTotals = $user->directReferrals()
            ->get()
            ->map(fn (User $leg) => $this->legBusiness($leg))
            ->sortDesc()
            ->values();

        [$powerWeight, $secondWeight, $restWeight] = config('mlm.leg_weights');

        $powerLeg = $legTotals->get(0, 0);
        $secondLeg = $legTotals->get(1, 0);
        $restLegsSum = $legTotals->slice(2)->sum();

        return ($powerLeg * $powerWeight / 100)
            + ($secondLeg * $secondWeight / 100)
            + ($restLegsSum * $restWeight / 100);
    }

    public function totalTeamSize(User $user): int
    {
        return count($this->subtreeIds($user->id));
    }

    /**
     * Start-rank-only qualification business: the Start rank requires just
     * 2 direct legs open, so only the top 2 legs count, weighted 50%/50%
     * each — a 3rd+ leg contributes nothing here (unlike the standard
     * weightedTeamBusiness() formula used by every other rank).
     */
    public function twoLegWeightedBusiness(User $user): float
    {
        $legTotals = $user->directReferrals()
            ->get()
            ->map(fn (User $leg) => $this->legBusiness($leg))
            ->sortDesc()
            ->values();

        $firstLeg = $legTotals->get(0, 0);
        $secondLeg = $legTotals->get(1, 0);

        return ($firstLeg * 0.5) + ($secondLeg * 0.5);
    }

    /**
     * Raw per-bucket leg amounts (unweighted): Power (largest leg), 2nd
     * (next largest), and Rest (every remaining leg summed). Used by the
     * 3-bucket rank qualification model — a rank's requirement is split
     * into independent Power/2nd/Rest targets (config('mlm.leg_weights')),
     * and each bucket must clear its own target rather than being blended
     * into one number the way weightedTeamBusiness() does.
     */
    public function legBreakdown(User $user): array
    {
        $legTotals = $user->directReferrals()
            ->get()
            ->map(fn (User $leg) => $this->legBusiness($leg))
            ->sortDesc()
            ->values();

        return [
            'power' => $legTotals->get(0, 0),
            'second' => $legTotals->get(1, 0),
            'rest' => $legTotals->slice(2)->sum(),
        ];
    }
}
