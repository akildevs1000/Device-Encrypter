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
            ->withoutOverlapping();

        $schedule->call(function () {
            Log::info('Scheduler heartbeat OK', ['time' => now()->toDateTimeString()]);
        })
        ->everyMinute()
        ->name('scheduler-heartbeat')
        ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
