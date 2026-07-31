<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use InvalidArgumentException;

class WalletService
{
    private const COLUMN_MAP = [
        'deposit' => 'deposit_balance',
        'roi' => 'roi_balance',
        'working' => 'working_balance',
        'rank_reward' => 'rank_reward_balance',
    ];

    public function credit(User $user, string $walletType, float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null): WalletTransaction
    {
        return $this->applyChange($user, $walletType, $amount, 'credit', $description, $referenceType, $referenceId);
    }

    public function debit(User $user, string $walletType, float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null): WalletTransaction
    {
        return $this->applyChange($user, $walletType, $amount, 'debit', $description, $referenceType, $referenceId);
    }

    private function applyChange(User $user, string $walletType, float $amount, string $direction, string $description, ?string $referenceType, ?int $referenceId): WalletTransaction
    {
        if (! isset(self::COLUMN_MAP[$walletType])) {
            throw new InvalidArgumentException("Unknown wallet type [{$walletType}]");
        }

        $column = self::COLUMN_MAP[$walletType];
        $wallet = $user->wallet()->lockForUpdate()->first() ?? $user->wallet()->create([]);

        $newBalance = $direction === 'credit'
            ? bcadd((string) $wallet->{$column}, (string) $amount, 2)
            : bcsub((string) $wallet->{$column}, (string) $amount, 2);

        if ($direction === 'debit' && bccomp($newBalance, '0', 2) < 0) {
            throw new InvalidArgumentException('Insufficient wallet balance.');
        }

        $wallet->{$column} = $newBalance;
        $wallet->save();

        return WalletTransaction::create([
            'user_id' => $user->id,
            'wallet_type' => $walletType,
            'direction' => $direction,
            'amount' => $amount,
            'balance_after' => $newBalance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }
}
