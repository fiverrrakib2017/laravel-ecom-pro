<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $tenants = \DB::connection('mysql')->table('tenants')->get();

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

            $schedule
                ->call(function () use ($tenant) {
                    $this->__set_tenant_connection($tenant);
                    \Artisan::call('app:clean_daily_usages');
                })
                ->monthlyOn(1, '01:00');
            $schedule
                ->call(function () use ($tenant) {
                    $this->__set_tenant_connection($tenant);
                    \Artisan::call('app:send_auto_message');
                })
                ->dailyAt('10:00');
        }
    }

    private function __set_tenant_connection($tenant)
    {
        \Config::set('database.connections.tenant', [
            'driver'   => 'mysql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
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
