<?php

namespace App\Services;

use App\Events\InvestmentCreated;
use App\Models\Investment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InvestmentService
{
    public function __construct(private WalletService $walletService)
    {
    }

    public function invest(User $user, float $amount): Investment
    {
        $minimum = (float) config('mlm.minimum_investment');

        if ($amount < $minimum) {
            throw new InvalidArgumentException("Minimum investment is \${$minimum}.");
        }

        return DB::transaction(function () use ($user, $amount) {
            $this->walletService->debit($user, 'deposit', $amount, 'Investment purchase');

            $investment = Investment::create([
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            event(new InvestmentCreated($investment));

            return $investment;
        });
    }

    /**
     * Reduces a user's active investment principal by $amount (oldest
     * investments drawn down first), used for admin-initiated "Investment"
     * withdrawals. Unlike invest(), this never touches the deposit wallet —
     * it directly shrinks/closes Investment rows, since invested principal
     * isn't tracked as a wallet balance column.
     */
    public function withdrawPrincipal(User $user, float $amount): void
    {
        $available = (float) $user->investments()->where('status', 'active')->sum('amount');

        if ($amount > $available) {
            throw new InvalidArgumentException('Insufficient invested principal.');
        }

        DB::transaction(function () use ($user, $amount) {
            $remaining = $amount;

            $investments = $user->investments()
                ->where('status', 'active')
                ->oldest()
                ->lockForUpdate()
                ->get();

            foreach ($investments as $investment) {
                if ($remaining <= 0) {
                    break;
                }

                $draw = min($remaining, (float) $investment->amount);
                $investment->amount = bcsub((string) $investment->amount, (string) $draw, 2);

                if (bccomp((string) $investment->amount, '0', 2) <= 0) {
                    $investment->amount = 0;
                    $investment->status = 'completed';
                }

                $investment->save();
                $remaining -= $draw;
            }
        });
    }
}
