<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeamBusinessCalculator
{
    public function legBusiness(User $legRoot): float
    {
        $result = DB::selectOne(
            'WITH RECURSIVE subtree AS (
                SELECT id FROM users WHERE id = ?
                UNION ALL
                SELECT u.id FROM users u INNER JOIN subtree s ON u.sponsor_id = s.id
            )
            SELECT COALESCE(SUM(i.amount), 0) AS total
            FROM investments i
            WHERE i.user_id IN (SELECT id FROM subtree)',
            [$legRoot->id]
        );

        return (float) $result->total;
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
        $result = DB::selectOne(
            'WITH RECURSIVE subtree AS (
                SELECT id FROM users WHERE sponsor_id = ?
                UNION ALL
                SELECT u.id FROM users u INNER JOIN subtree s ON u.sponsor_id = s.id
            )
            SELECT COUNT(*) AS total FROM subtree',
            [$user->id]
        );

        return (int) $result->total;
    }
}
