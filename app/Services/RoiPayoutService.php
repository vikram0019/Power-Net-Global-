<?php

namespace App\Services;

use App\Models\IncomeTransaction;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;

class RoiPayoutService
{
    public function __construct(private WalletService $walletService)
    {
    }

    /**
     * Pays one month's ROI to every active, ROI-enabled investment still under the
     * configured month cap (25 by default). Dummy users created by admin default to
     * roi_enabled = false and are skipped unless admin turns the benefit on for them.
     * Runs automatically on the 1st of every month via the schedule defined in
     * routes/console.php, and can also be triggered on demand via the admin panel
     * "Run ROI Now" button or the investments:pay-roi artisan command directly.
     */
    public function processDueInvestments(): int
    {
        $maxMonths = (int) config('mlm.roi_max_months');
        $percent = (float) config('mlm.monthly_roi_percent');
        $paid = 0;

        Investment::with('user')
            ->where('status', 'active')
            ->where('roi_months_paid', '<', $maxMonths)
            ->whereHas('user', fn ($q) => $q->where('roi_enabled', true))
            ->chunkById(100, function ($investments) use (&$paid, $maxMonths, $percent) {
                foreach ($investments as $investment) {
                    DB::transaction(function () use ($investment, $maxMonths, $percent) {
                        $amount = round(((float) $investment->amount) * $percent / 100, 2);

                        $this->walletService->credit(
                            $investment->user,
                            'roi',
                            $amount,
                            'Monthly ROI payout',
                            Investment::class,
                            $investment->id
                        );

                        IncomeTransaction::create([
                            'user_id' => $investment->user_id,
                            'source_user_id' => null,
                            'type' => 'monthly_roi',
                            'amount' => $amount,
                            'investment_id' => $investment->id,
                        ]);

                        $investment->roi_months_paid += 1;
                        $investment->roi_total_paid = bcadd((string) $investment->roi_total_paid, (string) $amount, 2);
                        $investment->last_roi_paid_at = now();

                        if ($investment->roi_months_paid >= $maxMonths) {
                            $investment->status = 'completed';
                        }

                        $investment->save();
                    });

                    $paid++;
                }
            });

        return $paid;
    }
}
