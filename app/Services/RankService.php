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
        $ranks = Rank::withCumulativeBucketTargets();
        $ownInvest = $user->totalInvested();
        $legs = $this->teamBusinessCalculator->legBreakdown($user);
        $directLegCount = $user->directReferrals()->count();
        $unlimitedLegs = (int) config('mlm.unlimited_legs');

        // pluck() bypasses Eloquent's attribute casting and returns raw driver
        // values (strings), while $rank->id below is a properly-cast int —
        // cast explicitly so the strict in_array() comparison actually matches.
        $alreadyAchievedRankIds = $user->rankHistory()->pluck('rank_id')->map(fn ($id) => (int) $id)->all();
        $highestQualifyingRankId = $user->current_rank_id;

        foreach ($ranks as $rank) {
            // Power/2nd/Rest each have their own running cumulative target
            // (see Rank::withCumulativeBucketTargets()) — a leg's raw dollar
            // amount must clear the target for its own bucket independently;
            // a large Power leg can't cover for a short Rest bucket, and
            // vice versa. Start's Rest target is always 0 (it never
            // contributes to that bucket), which is what makes it a
            // 2-leg-only rank without needing any special-case comparison here.
            $legsOpenSatisfied = $rank->legs_open >= $unlimitedLegs || $directLegCount >= $rank->legs_open;

            $qualifies = $ownInvest >= (float) $rank->own_invest_required
                && $legsOpenSatisfied
                && $legs['power'] >= $rank->power_target
                && $legs['second'] >= $rank->second_target
                && $legs['rest'] >= $rank->rest_target;

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
