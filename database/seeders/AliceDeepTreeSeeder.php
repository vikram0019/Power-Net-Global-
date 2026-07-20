<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Services\InvestmentService;
use App\Services\WalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AliceDeepTreeSeeder extends Seeder
{
    /**
     * Extends Alice's existing Bob->Eve leg down to level 20 (18 new users) and sizes
     * investments so her weighted team business crosses the Eagle rank threshold
     * ($25,000) while staying under Emerald's ($50,000) — landing exactly on Eagle.
     */
    public function run(): void
    {
        $alice = User::where('email', 'alice@demo.powernetglobal.com')->firstOrFail();
        $eve = User::where('email', 'eve@demo.powernetglobal.com')->firstOrFail();

        $walletService = app(WalletService::class);
        $investmentService = app(InvestmentService::class);

        $make = function (string $name, string $email, string $mobile, User $sponsor) {
            $user = User::create([
                'name' => $name,
                'mobile' => $mobile,
                'email' => $email,
                'password' => Hash::make('Demo@123'),
                'referral_code' => User::generateReferralCode(),
                'sponsor_id' => $sponsor->id,
                'email_verified_at' => now(),
                'status' => 'active',
            ]);

            Wallet::firstOrCreate(['user_id' => $user->id]);

            return $user;
        };

        $invest = function (User $user, float $amount) use ($walletService, $investmentService) {
            $walletService->credit($user, 'deposit', $amount, 'Deep tree seed — fund added');
            $investmentService->invest($user, $amount);
        };

        // Top up Alice's own investment from $100 to $500 to clear Eagle's own-invest requirement.
        $invest($alice, 400);

        // Extend Eve's line down through level 20 (levels 3-20 = 18 new users).
        $sponsor = $eve;
        for ($level = 3; $level <= 20; $level++) {
            $user = $make(
                "Level {$level} Member",
                "level{$level}@demo.powernetglobal.com",
                '99880' . str_pad((string) $level, 5, '0', STR_PAD_LEFT),
                $sponsor
            );

            $invest($user, 3000);

            $sponsor = $user;
        }
    }
}
