<?php

namespace App\Console\Commands;

use App\Services\RoiPayoutService;
use Illuminate\Console\Command;

class PayRoiCommand extends Command
{
    protected $signature = 'investments:pay-roi';

    protected $description = 'Pay one month of ROI to every active investment (run monthly via cron, or manually via the admin panel)';

    public function handle(RoiPayoutService $roiPayoutService): int
    {
        $count = $roiPayoutService->processDueInvestments();

        $this->info("Paid ROI for {$count} investment(s).");

        return self::SUCCESS;
    }
}
