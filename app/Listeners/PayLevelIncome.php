<?php

namespace App\Listeners;

use App\Events\InvestmentCreated;
use App\Models\IncomeTransaction;
use App\Models\Investment;
use App\Services\WalletService;

class PayLevelIncome
{
    public function __construct(private WalletService $walletService)
    {
    }

    public function handle(InvestmentCreated $event): void
    {
        $investment = $event->investment;
        $investor = $investment->user;
        $levelPercentages = config('mlm.level_percentages');
        $poolPercent = (float) config('mlm.level_income_pool_percent');

        $upline = $investor->sponsor;
        $level = 1;

        while ($upline && $level <= 20) {
            $upline->loadMissing('currentRank');
            $levelsUnlocked = $upline->currentRank->levels_unlocked ?? 0;

            if ($levelsUnlocked >= $level && isset($levelPercentages[$level])) {
                $amount = round(
                    (float) $investment->amount * ($poolPercent / 100) * ($levelPercentages[$level] / 100),
                    2
                );

                if ($amount > 0) {
                    $this->walletService->credit(
                        $upline,
                        'working',
                        $amount,
                        "Level {$level} income from {$investor->name}",
                        Investment::class,
                        $investment->id
                    );

                    IncomeTransaction::create([
                        'user_id' => $upline->id,
                        'source_user_id' => $investor->id,
                        'type' => 'level_income',
                        'level' => $level,
                        'amount' => $amount,
                        'investment_id' => $investment->id,
                    ]);
                }
            }

            $upline = $upline->sponsor;
            $level++;
        }
    }
}
