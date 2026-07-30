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
        $ranks = Rank::withCumulativeTeamBusiness();
        $ownInvest = $user->totalInvested();
        $teamBusiness = $this->teamBusinessCalculator->weightedTeamBusiness($user);

        $alreadyAchievedRankIds = $user->rankHistory()->pluck('rank_id')->all();
        $highestQualifyingRankId = $user->current_rank_id;

        foreach ($ranks as $rank) {
            $qualifies = $ownInvest >= (float) $rank->own_invest_required
                && $teamBusiness >= $rank->cumulative_team_business_required;

            $alreadyAchieved = in_array($rank->id, $alreadyAchievedRankIds, true);

            if ($qualifies && ! $alreadyAchieved) {
                $this->promote($user, $rank);
            }

            // Grandfather ranks already achieved: raising a rank's cumulative
            // threshold later must never demote a user's current rank below
            // one they already earned (and were paid for) under an older,
            // lower threshold.
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
