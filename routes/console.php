<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pays one month of ROI to every eligible active investment, automatically, on the
// 1st of every month. Requires the scheduler itself to be triggered regularly —
// in production via a single cron entry (`* * * * * php artisan schedule:run`),
// locally via a Windows Scheduled Task running the same command every minute.
Schedule::command('investments:pay-roi')
    ->monthlyOn(1, '00:00')
    ->name('monthly-roi-payout')
    ->withoutOverlapping()
    ->onOneServer();
