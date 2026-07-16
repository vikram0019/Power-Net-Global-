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
}
