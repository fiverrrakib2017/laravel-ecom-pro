<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
class MigrateTenants extends Command
{
    protected $signature = 'tenants:migrate {--fresh} {--seed}';
    protected $description = 'Run migrations for all tenant databases';

    public function handle(){
          $tenants = DB::table('tenants')->get();
        foreach ($tenants as $tenant) {
            $this->info("Migrating tenant: {$tenant->subdomain} ({$tenant->db_name})");

            /*------Build tenant connection-----*/
            $connection = [
                'driver'    => 'mysql',
                'host'      => env('DB_HOST', '127.0.0.1'),
                'port'      => env('DB_PORT', '3306'),
                'database'  => $tenant->db_name,
                'username'  => $tenant->db_user,
                'password'  => $tenant->db_pass,
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
                'strict'    => true,
            ];

            /*------Register connection------*/
            Config::set("database.connections.tenant_{$tenant->id}", $connection);

            /*----Migrate----*/
            $params = [
                '--database' => "tenant_{$tenant->id}",
                '--force'    => true,
            ];

            if ($this->option('fresh')) {
                //Artisan::call('migrate:fresh', $params);
            } else {
                Artisan::call('migrate', $params);
            }

            if ($this->option('seed')) {
                Artisan::call('db:seed', $params);
            }

            $this->line(Artisan::output());
        }

        $this->info("✅ All tenant migrations completed.");
    }
}

