<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateTenants extends Command
{
    protected $signature = 'tenants:migrate {--seed}';
    protected $description = 'Run migrations for all tenant databases';

    public function handle()
    {
        $companies = DB::connection('isp_billing')->table('tenants')->get();

        foreach ($companies as $company) {
            $this->info("🚀 Migrating for company: {$company->subdomain}");

            config([
                'database.connections.tenant' => [
                    'driver'   => 'mysql',
                    'host'     => 'localhost',
                    'database' => $company->db_name,
                    'username' => $company->db_user,
                    'password' => $company->db_pass,
                ],
            ]);

            $options = [
                '--database' => 'tenant',
                '--path' => 'database/migrations',
                '--force' => true,
            ];

            if ($this->option('seed')) {
                $options['--seed'] = true;
            }

            try {
                Artisan::call('migrate', $options);
                $this->line(Artisan::output());
                $this->info("✅ Migration done for {$company->subdomain}");
            } catch (\Exception $e) {
                $this->error("❌ Migration failed for {$company->subdomain}: " . $e->getMessage());
            }
        }
    }
}
