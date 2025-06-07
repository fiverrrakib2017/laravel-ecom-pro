<?php

namespace App\Console\Commands;
use RouterOS\Client;
use RouterOS\Query;
use App\Models\Customer;
use App\Models\Router;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\SessionService;

class automation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:automation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run daily ISP related automated tasks';

    /**
     * Execute the console command.
     */
    public function handle(SessionService $session_service)
    {
        $this->info('--- ISP Daily Tasks Started ---');
        $this->check_online_offline_status();
        $this->check_expire_customer();

        /* Call the Session Remove method*/
        $session_service->forget_session_sidebar_customer();
        $this->info('--- ISP Daily Tasks Finished ---');
    }
    protected function check_online_offline_status()
    {
        $customers = Customer::where('is_delete', '0')->where('status', '!=', 'expired')->get();

        foreach ($customers as $customer) {
            $router = Router::where('status', 'active')->where('id', $customer->router_id)->first();

            if (!$router) {
                $this->error("Router not found for customer {$customer->username}");
                continue;
            }

            try {
                $client = new Client([
                    'host' => $router->ip_address,
                    'user' => $router->username,
                    'pass' => $router->password,
                    'port' => (int) $router->port,
                    'timeout' => 3,
                    'attempts' => 1,
                ]);

                $query = new Query('/ppp/active/print');
                $query->where('name', $customer->username);

                $response = $client->query($query)->read();

                if (!empty($response)) {
                    $customer->update(['status' => 'online']);
                    $this->info("Customer {$customer->username} is ONLINE");
                } else {
                    $customer->update(['status' => 'offline']);
                    $this->info("Customer {$customer->username} is OFFLINE");
                }
            } catch (\Exception $e) {
                $this->error("Connection error for router {$router->ip_address}: " . $e->getMessage());
            }
        }
    }
    protected function check_expire_customer()
    {
        $today = Carbon::now()->format('Y-m-d');

        $expire_customers = Customer::where('is_delete', '0')
            ->where('expire_date', '<', $today)
            ->whereIn('status', ['active', 'online', 'offline'])
            ->get();

        foreach ($expire_customers as $customer) {
            $router = Router::where('status', 'active')
                ->where('id', $customer->router_id)
                ->first();

            if (!$router) {
                $this->error("Router not found for customer {$customer->username}");
                continue;
            }

            try {
                $client = new Client([
                    'host'     => $router->ip_address,
                    'user'     => $router->username,
                    'pass'     => $router->password,
                    'port'     => (int)$router->port,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);

                /* Find PPP secret*/
                $query = new Query('/ppp/secret/print');
                $query->where('name', $customer->username);
                $secrets = $client->query($query)->read();

                if (!empty($secrets)) {
                    $secretId = $secrets[0]['.id'];

                    /* Remove from active list*/
                    $removeActive = new Query('/ppp/active/remove');
                    $removeActive->equal('name', $customer->username);
                    $client->query($removeActive)->read();

                    /*Disable the secret*/
                    $disableSecret = new Query('/ppp/secret/set');
                    $disableSecret->equal('.id', $secretId)
                                  ->equal('disabled', 'yes');
                    $client->query($disableSecret)->read();

                    $this->info("MikroTik: Customer {$customer->username} is now DISABLED & REMOVED.");
                } else {
                    $this->warn("MikroTik: PPP secret not found for {$customer->username}");
                }

                /* Now update DB*/
                $customer->update(['status' => 'expired']);
                $this->info("Customer {$customer->username} is (Expired)");

            } catch (\Exception $e) {
                $this->error("Router connection failed for {$customer->username}: " . $e->getMessage());
            }
        }
    }
    protected function enable_customer(Customer $customer){
        $router = Router::where('status', 'active')
        ->where('id', $customer->router_id)
        ->first();

        if (!$router) {
            return response()->json(['error' => 'Router not found'], 404);
        }

        try {
            $client = new Client([
                'host'     => $router->ip_address,
                'user'     => $router->username,
                'pass'     => $router->password,
                'port'     => (int)$router->port,
                'timeout'  => 3,
                'attempts' => 1
            ]);

            /*Find the secret*/
            $query = new Query('/ppp/secret/print');
            $query->where('name', $customer->username);
            $secrets = $client->query($query)->read();

            if (!empty($secrets)) {
                $secretId = $secrets[0]['.id'];

                /*Enable the secret*/
                $enableQuery = new Query('/ppp/secret/set');
                $enableQuery->equal('.id', $secretId)
                            ->equal('disabled', 'no');
                $client->query($enableQuery)->read();

                /*Update DB status*/
                $customer->update(['status' => 'active']);

                return response()->json(['message' => "Customer {$customer->username} is now active."], 200);
            } else {
                return response()->json(['error' => 'PPP secret not found in MikroTik'], 404);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    protected function remove_sidebar_session(){
        try {
            if (!app()->runningInConsole()) {
                session()->forget('sidebar_customers');
            }
        } catch (\Exception $e) {
            Log::warning('Session forget failed in console: ' . $e->getMessage());
        }
    }
}
