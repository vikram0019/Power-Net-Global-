<?php

namespace App\Listeners;

use App\Events\InvestmentCreated;
use App\Models\IncomeTransaction;
use App\Services\WalletService;

class PayDirectReward
{
    public function __construct(private WalletService $walletService)
    {
    }

    public function handle(InvestmentCreated $event): void
    {
        $investment = $event->investment;
        $investor = $investment->user;
        $sponsor = $investor->sponsor;

        if ($sponsor) {
            $this->pay($sponsor, $investor, $investment->id, (float) $investment->amount, 'direct_reward', config('mlm.direct_reward_percent'));

            $sponsorsSponsor = $sponsor->sponsor;
            if ($sponsorsSponsor) {
                $this->pay($sponsorsSponsor, $investor, $investment->id, (float) $investment->amount, 'direct_reward_upline', config('mlm.direct_reward_upline_percent'));
            }
        }
    }

    private function pay($recipient, $investor, int $investmentId, float $investmentAmount, string $type, float $percent): void
    {
        if (! $recipient->hasMinimumInvestment()) {
            return;
        }

        $amount = round($investmentAmount * $percent / 100, 2);

        $this->walletService->credit(
            $recipient,
            'working',
            $amount,
            ($type === 'direct_reward' ? 'Direct reward from ' : 'Direct reward (upline) from ') . $investor->name,
            \App\Models\Investment::class,
            $investmentId
        );

        IncomeTransaction::create([
            'user_id' => $recipient->id,
            'source_user_id' => $investor->id,
            'type' => $type,
            'amount' => $amount,
            'investment_id' => $investmentId,
        ]);
    }
}
