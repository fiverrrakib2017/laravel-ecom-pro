<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //everyFiveMinutes  hourly everyThirtyMinutes  everyMinute
        $schedule->command('app:check_status')->everyMinute();
        $schedule->command('app:check_expire')->dailyAt('10:00');
       // $schedule->command('app:check_wather')->everyThirtyMinutes();
        $schedule->command('app:customer_usage')->everyFiveMinutes();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
