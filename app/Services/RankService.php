<?php

namespace App\Services;

use App\Models\IncomeTransaction;
use App\Models\Rank;
use App\Models\User;
use App\Models\UserRank;

class RankService
{
    public function __construct(
        private TeamBusinessCalculator $teamBusinessCalculator,
        private WalletService $walletService,
    ) {
    }

    public function evaluateAndPromote(User $user): void
    {
        $ranks = Rank::ordered()->get();
        $ownInvest = $user->totalInvested();
        $legs = $this->teamBusinessCalculator->legBreakdown($user);
        $startTeamBusiness = $this->teamBusinessCalculator->twoLegWeightedBusiness($user);

        [$powerWeight, $secondWeight, $restWeight] = config('mlm.leg_weights');

        // pluck() bypasses Eloquent's attribute casting and returns raw driver
        // values (strings), while $rank->id below is a properly-cast int —
        // cast explicitly so the strict in_array() comparison actually matches.
        $alreadyAchievedRankIds = $user->rankHistory()->pluck('rank_id')->map(fn ($id) => (int) $id)->all();
        $highestQualifyingRankId = $user->current_rank_id;

        foreach ($ranks as $rank) {
            if ($rank->code === 'start') {
                // Start only requires 2 legs open, so it qualifies off just
                // the top 2 legs combined at 50/50 against its own amount.
                $qualifies = $ownInvest >= (float) $rank->own_invest_required
                    && $startTeamBusiness >= (float) $rank->team_business_required;
            } else {
                // Every other rank's own stated amount is split into 3
                // independent buckets (Power/2nd/Rest, weighted per
                // config('mlm.leg_weights')) — each bucket must clear its
                // own target on its own; a large Power leg can't cover for
                // an empty Rest bucket.
                $required = (float) $rank->team_business_required;

                $qualifies = $ownInvest >= (float) $rank->own_invest_required
                    && $legs['power'] >= $required * $powerWeight / 100
                    && $legs['second'] >= $required * $secondWeight / 100
                    && $legs['rest'] >= $required * $restWeight / 100;
            }

            $alreadyAchieved = in_array($rank->id, $alreadyAchievedRankIds, true);

            if ($qualifies && ! $alreadyAchieved) {
                $this->promote($user, $rank);
            }

            // Grandfather ranks already achieved: a formula change must
            // never demote a user's current rank below one they already
            // earned (and were paid for) under the rules in effect then.
            if ($qualifies || $alreadyAchieved) {
                $highestQualifyingRankId = $rank->id;
            }
        }

        if ($highestQualifyingRankId !== $user->current_rank_id) {
            $user->current_rank_id = $highestQualifyingRankId;
            $user->save();
        }
    }

    private function promote(User $user, Rank $rank): void
    {
        UserRank::create([
            'user_id' => $user->id,
            'rank_id' => $rank->id,
            'achieved_at' => now(),
            'reward_paid' => true,
        ]);

        $this->walletService->credit(
            $user,
            'rank_reward',
            (float) $rank->reward_amount,
            "Rank reward — {$rank->name}",
            Rank::class,
            $rank->id
        );

        IncomeTransaction::create([
            'user_id' => $user->id,
            'source_user_id' => null,
            'type' => 'rank_reward',
            'amount' => $rank->reward_amount,
            'investment_id' => null,
        ]);
    }
}
