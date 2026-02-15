<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscription:notify-expiring')->daily();
Schedule::command('subscription:notify-trial-ending')->daily();
Schedule::command('subscription:process-expired-trials')->daily();
Schedule::command('subscription:process-expired')->daily();
Schedule::command('subscription:reset-monthly')->monthlyOn(1, '00:00');
Schedule::command('business:notify-inactive')->daily();
Schedule::command('ticket:notify-critical')->daily();
Schedule::command('notifications:clean')->dailyAt('03:00');
Schedule::command('appointment:notify-reminder')->hourly();
Schedule::command('appointment:notify-reengagement')->weeklyOn(1, '09:00');
Schedule::command('appointment:cancel-expired-pending')->hourly();
