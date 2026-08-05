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
     * Pays one day's slice of MPG to every active, MPG-enabled investment still
     * under its lifetime cap (amount * roi_max_months * monthly_roi_percent%,
     * 200% by default). The daily rate is the 8% monthly rate divided by the
     * actual number of days in the current calendar month, so each month's
     * payouts still sum to exactly 8% regardless of month length. Dummy users
     * created by admin default to roi_enabled = false and are skipped unless
     * admin turns the benefit on for them. Runs automatically once a day via
     * the schedule defined in routes/console.php, and can also be triggered on
     * demand via the admin panel "Run MPG Now" button or the investments:pay-roi
     * artisan command directly. Idempotent per calendar day — running it twice
     * in the same day pays nothing the second time.
     */
    public function processDueInvestments(): int
    {
        $maxMonths = (int) config('mlm.roi_max_months');
        $percent = (float) config('mlm.monthly_roi_percent');
        $today = now()->toDateString();
        $dailyPercent = $percent / now()->daysInMonth;
        $paid = 0;

        Investment::with('user')
            ->where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('last_roi_paid_at')
                    ->orWhereDate('last_roi_paid_at', '<', $today);
            })
            ->whereHas('user', fn ($q) => $q->where('roi_enabled', true))
            ->chunkById(100, function ($investments) use (&$paid, $maxMonths, $dailyPercent, $today) {
                foreach ($investments as $investment) {
                    DB::transaction(function () use ($investment, $maxMonths, $dailyPercent, $today, &$paid) {
                        $investment = Investment::lockForUpdate()->find($investment->id);
                        $cap = $investment->roiCap();
                        $remaining = bcsub((string) $cap, (string) $investment->roi_total_paid, 2);

                        if (bccomp($remaining, '0', 2) <= 0) {
                            return;
                        }

                        $amount = round(((float) $investment->amount) * $dailyPercent / 100, 2);
                        $amount = min($amount, (float) $remaining);

                        if ($amount <= 0) {
                            return;
                        }

                        $this->walletService->credit(
                            $investment->user,
                            'roi',
                            $amount,
                            'Daily MPG payout',
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

                        $investment->roi_total_paid = bcadd((string) $investment->roi_total_paid, (string) $amount, 2);
                        $investment->last_roi_paid_at = now();

                        if (bccomp((string) $investment->roi_total_paid, (string) $cap, 2) >= 0) {
                            $investment->status = 'completed';
                        }

                        $investment->save();

                        $paid++;
                    });
                }
            });

        return $paid;
    }
}
