<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Services\InvestmentService;
use App\Services\WalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoTreeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->firstOrFail();
        $walletService = app(WalletService::class);
        $investmentService = app(InvestmentService::class);

        $make = function (string $name, string $email, string $mobile, ?User $sponsor) {
            $user = User::create([
                'name' => $name,
                'mobile' => $mobile,
                'email' => $email,
                'password' => Hash::make('Demo@123'),
                'referral_code' => User::generateReferralCode(),
                'sponsor_id' => $sponsor?->id,
                'email_verified_at' => now(),
                'status' => 'active',
            ]);

            Wallet::firstOrCreate(['user_id' => $user->id]);

            return $user;
        };

        $invest = function (User $user, float $amount) use ($walletService, $investmentService) {
            $walletService->credit($user, 'deposit', $amount, 'Demo seed — fund added');
            $investmentService->invest($user, $amount);
        };

        $alice = $make('Alice Founder', 'alice@demo.powernetglobal.com', '9990000001', $admin);
        $bob = $make('Bob Builder', 'bob@demo.powernetglobal.com', '9990000002', $alice);
        $carol = $make('Carol Chan', 'carol@demo.powernetglobal.com', '9990000003', $alice);
        $dave = $make('Dave Diaz', 'dave@demo.powernetglobal.com', '9990000004', $alice);
        $eve = $make('Eve Evans', 'eve@demo.powernetglobal.com', '9990000005', $bob);
        $frank = $make('Frank Fox', 'frank@demo.powernetglobal.com', '9990000006', $bob);
        $grace = $make('Grace Grey', 'grace@demo.powernetglobal.com', '9990000007', $carol);

        $invest($alice, 100);
        $invest($bob, 3000);
        $invest($eve, 1000);
        $invest($frank, 1000);
        $invest($carol, 2000);
        $invest($grace, 1000);
        $invest($dave, 1500);
    }
}
