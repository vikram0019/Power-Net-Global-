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
        $teamBusiness = $this->teamBusinessCalculator->weightedTeamBusiness($user);

        $achievedRankIds = $user->rankHistory()->pluck('rank_id')->all();
        $highestQualifyingRankId = $user->current_rank_id;

        foreach ($ranks as $rank) {
            $qualifies = $ownInvest >= (float) $rank->own_invest_required
                && $teamBusiness >= (float) $rank->team_business_required;

            if (! $qualifies) {
                continue;
            }

            if (! in_array($rank->id, $achievedRankIds, true)) {
                $this->promote($user, $rank);
                $achievedRankIds[] = $rank->id;
            }

            $highestQualifyingRankId = $rank->id;
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
            'working',
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
