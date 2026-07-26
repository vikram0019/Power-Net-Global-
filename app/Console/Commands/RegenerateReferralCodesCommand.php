<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RegenerateReferralCodesCommand extends Command
{
    protected $signature = 'users:regenerate-referral-codes';

    protected $description = 'One-off migration: regenerate every user\'s referral code as a unique 6-digit number, replacing the old PNG-prefixed format';

    public function handle(): int
    {
        $count = 0;

        User::each(function (User $user) use (&$count) {
            $user->update(['referral_code' => User::generateReferralCode()]);
            $count++;
        });

        $this->info("Regenerated referral codes for {$count} user(s).");

        return self::SUCCESS;
    }
}
