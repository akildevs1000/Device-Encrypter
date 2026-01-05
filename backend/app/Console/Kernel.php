<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('license:check')
            ->daily()
            ->withoutOverlapping()
            // ->onOneServer() // enable if you have multiple servers + shared cache
            ->appendOutputTo(storage_path('logs/license-check.log'));

        $schedule->call(function () {
            Log::info('Scheduler heartbeat OK', ['time' => now()->toDateTimeString()]);
        })
        ->everyMinute()
        ->name('scheduler-heartbeat')
        ->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/scheduler-heartbeat.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
