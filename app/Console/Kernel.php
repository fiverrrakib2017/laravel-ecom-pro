<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    // protected function schedule(Schedule $schedule): void
    // {
    //     //everyFiveMinutes  hourly everyThirtyMinutes  everyMinute
    //     $schedule->command('app:check_status')->everyMinute();
    //     $schedule->command('app:check_expire')->dailyAt('10:00');
    //    // $schedule->command('app:check_wather')->everyThirtyMinutes();
    //     $schedule->command('app:customer_usage')->everyFiveMinutes();

    // }
    protected function schedule(Schedule $schedule): void
    {
        $tenants = \DB::table('tenants')->get();

        foreach ($tenants as $tenant) {
            $schedule
                ->call(function () use ($tenant) {
                    $this->__set_tenant_connection($tenant);
                    \Artisan::call('app:check_status');
                })
                ->everyMinute();

            $schedule
                ->call(function () use ($tenant) {
                    $this->__set_tenant_connection($tenant);
                    \Artisan::call('app:check_expire');
                })
                ->dailyAt('10:00');

            $schedule
                ->call(function () use ($tenant) {
                    $this->__set_tenant_connection($tenant);
                    \Artisan::call('app:customer_usage');
                })
                ->everyFiveMinutes();
        }
    }

    private function __set_tenant_connection($tenant)
    {
        \Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $tenant->db_name,
            'username' => $tenant->db_user,
            'password' => $tenant->db_pass,
        ]);

        \DB::purge('tenant');
        \DB::reconnect('tenant');
        \DB::setDefaultConnection('tenant');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
