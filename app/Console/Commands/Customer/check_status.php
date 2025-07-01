<?php

namespace App\Console\Commands\Customer;

use App\Jobs\CheckCustomerStatus ;
use RouterOS\Client;
use RouterOS\Query;
use App\Models\Customer;
use App\Models\Router;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\SessionService;
class check_status extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run ISP related automated tasks';

    /**
     * Execute the console command.
     */
    public function handle(SessionService $session_service)
    {
        $this->info('---Tasks Started ---');

        // Customer::where('is_delete', '0')
        //     ->whereNotIn('status', ['expired', 'disabled', 'discontinue'])
        //     ->chunk(100, function ($customers) {
        //         foreach ($customers as $customer) {
        //             dispatch(new CheckCustomerStatus ($customer->id));
        //         }
        //     });
            $customers = Customer::where('is_delete', '0')->where('status', '!=', 'expired')->where('status', '!=', 'disabled')->where('status', '!=', 'discontinue')->get();
            foreach($customers as $customer){
                  dispatch(new CheckCustomerStatus ($customer->id));
            }

        /*session reset*/
        //$session_service->forget_session_sidebar_customer();

        $this->info('---Tasks Finished ---');
    }
    protected function check_online_offline_status()
    {
        $customers = Customer::where('is_delete', '0')->where('status', '!=', 'expired')->where('status', '!=', 'disabled')->where('status', '!=', 'discontinue')->get();

        foreach ($customers as $customer) {
            /*********** PPPOE Customer  ****************/
            if ($customer->connection_type == 'pppoe') {
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
            /*********** Radius Customer  ****************/
            if ($customer->connection_type == 'radius'){
                 $activeSession = \App\Models\Radius\Radacct::where('username', $customer->username)
                                ->whereNull('acctstoptime')
                                ->latest('acctstarttime')
                                ->first();
                if ($activeSession) {
                    $customer->update(['status' => 'online']);
                    $this->info("RADIUS Customer {$customer->username} is ONLINE (via radacct)");
                } else {
                    $customer->update(['status' => 'offline']);
                    $this->info("RADIUS Customer {$customer->username} is OFFLINE (via radacct)");
                }
            }
            if($customer->connection_type=='hotspot'){

            }
        }
    }
}
