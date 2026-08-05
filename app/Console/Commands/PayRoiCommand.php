<?php

namespace App\Console\Commands;

use App\Services\RoiPayoutService;
use Illuminate\Console\Command;

class PayRoiCommand extends Command
{
    protected $signature = 'investments:pay-roi';

    protected $description = "Pay each active investment's daily slice of MPG (8% monthly, spread across the days in the month) — run daily via cron, or manually via the admin panel";

    public function handle(RoiPayoutService $roiPayoutService): int
    {
        $count = $roiPayoutService->processDueInvestments();

        $this->info("Paid daily MPG for {$count} investment(s).");

        return self::SUCCESS;
    }
}
