<?php

namespace App\Listeners;

use App\Events\InvestmentCreated;
use App\Services\RankService;

class RecalculateUplineRanks
{
    public function __construct(private RankService $rankService)
    {
    }

    public function handle(InvestmentCreated $event): void
    {
        $investor = $event->investment->user;

        $this->rankService->evaluateAndPromote($investor);

        $ancestor = $investor->sponsor;

        while ($ancestor) {
            $this->rankService->evaluateAndPromote($ancestor);
            $ancestor = $ancestor->sponsor;
        }
    }
}
