<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pays each eligible active investment its daily slice of MPG (8% monthly,
// divided by the number of days in the current month), automatically, once a
// day. Requires the scheduler itself to be triggered regularly — in production
// via a single cron entry (`* * * * * php artisan schedule:run`), locally via
// a Windows Scheduled Task running the same command every minute.
Schedule::command('investments:pay-roi')
    ->dailyAt('00:05')
    ->name('daily-mpg-payout')
    ->withoutOverlapping()
    ->onOneServer();
