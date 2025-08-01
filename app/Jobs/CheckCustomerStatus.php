<?php
namespace App\Jobs;
use App\Models\Customer;
use App\Models\Router;
use App\Models\Radius\Radacct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Facades\Log;

class CheckCustomerStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $router_id;

    public function __construct($router_id)
    {
        $this->router_id = $router_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $router = Router::find($this->router_id);
        if (!$router) {
            return;
        }

        try {
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port,
                'timeout' => 5,
                'attempts' => 1,
            ]);

            $activeList = collect($client->query(new Query('/ppp/active/print'))->read());

            $customers = Customer::where('is_delete', '0')->where('status', '!=', 'expired')->where('status', '!=', 'disabled')->where('status', '!=', 'discontinue')->get();

            foreach ($customers as $customer) {
                /**For PPPOE customer**/
                if ($customer->connection_type == 'pppoe') {
                    $isOnline = $activeList->contains(function ($item) use ($customer) {
                        return $item['name'] === $customer->username;
                    });

                    if ($isOnline) {
                        if ($customer->status !== 'online') {
                            $customer->update(['status' => 'online']);
                        }
                    } else {
                        if ($customer->status !== 'offline') {
                            $customer->update([
                                'status' => 'offline',
                                'last_seen' => now(),
                            ]);
                        }
                    }
                }
                else if($customer->connection_type == 'radius'){}
                else if($customer->connection_type == 'hotspot'){}
                else{}
            }
        } catch (\Exception $e) {
            \Log::error("Router ({$router->ip_address}) connection failed: " . $e->getMessage());
        }
    }

    // public function handle(): void
    // {
    //     $customer = Customer::find($this->customer_id);
    //     if (!$customer) {
    //         return;
    //     }

    //     try {
    //         if ($customer->connection_type == 'pppoe') {
    //             $router = Router::where('status', 'active')->where('id', $customer->router_id)->first();

    //             if (!$router) {
    //                 //Log::error("Router not found for customer {$customer->username}");
    //                 return;
    //             }

    //             $client = new Client([
    //                 'host' => $router->ip_address,
    //                 'user' => $router->username,
    //                 'pass' => $router->password,
    //                 'port' => (int) $router->port,
    //                 'timeout' => 3,
    //                 'attempts' => 1,
    //             ]);

    //             $query = new Query('/ppp/active/print');
    //             $query->where('name', $customer->username);

    //             $response = $client->query($query)->read();

    //             if (!empty($response)) {
    //                 $customer->update(['status' => 'online']);
    //             } else {
    //                 $customer->update([
    //                     'status' => 'offline',
    //                     'last_seen' => now(),
    //                 ]);
    //             }
    //         } elseif ($customer->connection_type == 'radius') {
    //             $activeSession = Radacct::where('username', $customer->username)->whereNull('acctstoptime')->latest('acctstarttime')->first();

    //             if ($activeSession) {
    //                 $customer->update(['status' => 'online']);
    //                 //Log::info("RADIUS Customer {$customer->username} is ONLINE (via radacct)");
    //             } else {
    //                 $customer->update(['status' => 'offline']);
    //                 //Log::info("RADIUS Customer {$customer->username} is OFFLINE (via radacct)");
    //             }
    //         } elseif ($customer->connection_type == 'hotspot') {
    //             $router = Router::where('status', 'active')->where('id', $customer->router_id)->first();

    //             if (!$router) {
    //                 //Log::error("Router not found for hotspot customer {$customer->username}");
    //                 return;
    //             }

    //             try {
    //                 $client = new Client([
    //                     'host' => $router->ip_address,
    //                     'user' => $router->username,
    //                     'pass' => $router->password,
    //                     'port' => (int) $router->port,
    //                     'timeout' => 3,
    //                     'attempts' => 1,
    //                 ]);

    //                 $query = new Query('/ip/hotspot/active/print');
    //                 /* Hotspot user filter*/
    //                 $query->where('user', $customer->username);

    //                 $response = $client->query($query)->read();

    //                 if (!empty($response)) {
    //                     $customer->update(['status' => 'online']);
    //                     Log::info("HOTSPOT Customer {$customer->username} is ONLINE");
    //                 } else {
    //                     $customer->update(['status' => 'offline']);
    //                     Log::info("HOTSPOT Customer {$customer->username} is OFFLINE");
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error("Hotspot check failed for {$customer->username}: " . $e->getMessage());
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("Status check failed for customer {$customer->username}: " . $e->getMessage());
    //     }
    // }
}
