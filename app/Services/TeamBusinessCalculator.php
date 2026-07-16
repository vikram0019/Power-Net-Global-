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
     * Rank-qualification business: sum of the top-3 legs weighted per config('mlm.leg_weights').
     * Legs outside the top 3 contribute nothing toward rank qualification.
     */
    public function weightedTeamBusiness(User $user): float
    {
        $legTotals = $user->directReferrals()
            ->get()
            ->map(fn (User $leg) => $this->legBusiness($leg))
            ->sortDesc()
            ->values();

        $weights = config('mlm.leg_weights');
        $total = 0.0;

        foreach ($weights as $index => $weightPercent) {
            $total += $legTotals->get($index, 0) * ($weightPercent / 100);
        }

        return $total;
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
