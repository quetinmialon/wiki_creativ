<?php

use App\Console\Commands\DeletingsLogs;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
app(Schedule::class)->command('temp:deletings-logs')->dailyAt('00:00');
app(Schedule::class)->command('temp:deleting-unused-images')->dailyAt('02:00');
app(Schedule::class)->command('temp:deleting-expired-permissions')->dailyAt('00:00');
app(Schedule::class)->command('temp:update-expired-permissions')->dailyAt('00:00');
