<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@powernetglobal.com'],
            [
                'name' => 'System Admin',
                'mobile' => '9990000000',
                'password' => Hash::make('Admin@123'),
                'referral_code' => User::generateReferralCode(),
                'sponsor_id' => null,
                'email_verified_at' => now(),
                'status' => 'active',
                'is_admin' => true,
            ]
        );

        Wallet::firstOrCreate(['user_id' => $admin->id]);
    }
}
