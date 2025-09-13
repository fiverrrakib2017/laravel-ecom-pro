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

            /**----Get Online Customer**/
            $activeList = collect($client->query(new Query('/ppp/active/print'))->read());

            /**----Get Mikrotik Customer**/
            $pppSecrets = collect($client->query(new Query('/ppp/secret/print'))->read())
                ->keyBy('name');

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
                        $lastSeen = now();
                        $secret = $pppSecrets->get($customer->username);

                        if ($secret && !empty($secret['last-logged-out'])) {
                            try {
                                $lastSeen = Carbon::parse($secret['last-logged-out']);
                            } catch (\Throwable $e) {
                            }
                        }

                        if ($customer->status !== 'offline' || $customer->last_seen === null) {
                            $customer->update([
                                'status'    => 'offline',
                                'last_seen' => $lastSeen,
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
}
