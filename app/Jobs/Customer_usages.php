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
use App\Models\Daily_usages;
class Customer_usages  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $router_id;

    /**
     * Create a new job instance.
     */
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
            Log::error("Router not found for ID: {$this->router_id}");
            return;
        }

        $customers = Customer::where('router_id', $router->id)
            ->where('is_delete', 0)
            ->whereIn('connection_type', ['pppoe'])
            ->whereNotIn('status', ['expired', 'disabled', 'discontinue'])
            ->get();

        if ($customers->isEmpty()) return;

        try {
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int)$router->port,
                'timeout' => 5,
                'attempts' => 1,
            ]);

            // Query once
            $sessions = $client->query(new Query('/ppp/active/print'))->read();
            $interfaces = $client->query(new Query('/interface/print'))->read();
            $arps = $client->query(new Query('/ip/arp/print'))->read();

            foreach ($customers as $customer) {
                $session = collect($sessions)->firstWhere('name', $customer->username);

                if (!$session) continue;

                $ip_address = $session['address'] ?? null;
                $mac_address = $session['caller-id'] ?? null;
                $session_id = $session['session-id'] ?? null;

                // Try to get better MAC from ARP
                if ($ip_address) {
                    $arp = collect($arps)->firstWhere('address', $ip_address);
                    $mac_address = $arp['mac-address'] ?? $mac_address;
                }

                // Detect interface
                $interface = collect($interfaces)->first(function ($intf) use ($customer) {
                    return str_contains($intf['name'], $customer->username);
                });

                $rx_speed = $tx_speed = 0;
                if ($interface) {
                    $rx_speed = round(($interface['rx-byte'] ?? 0) / 1024 / 1024, 2);
                    $tx_speed = round(($interface['tx-byte'] ?? 0) / 1024 / 1024, 2);
                }

                if ($session_id) {
                    Daily_usages::updateOrCreate(
                        ['session_id' => $session_id, 'customer_id' => $customer->id],
                        [
                            'router_id' => $router->id,
                            'ip' => $ip_address,
                            'mac' => $mac_address,
                            'upload' => $tx_speed,
                            'download' => $rx_speed,
                            'date' => now(),
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error("Router usage fetch error for Router #{$router->id}: " . $e->getMessage());
        }
    }




    // public function handle(): void
    // {
    //     $customer = Customer::find($this->customer_id);
    //     if (!$customer) {
    //         Log::warning("Customer not found with ID: {$this->customer_id} In Queue Job");
    //         return;
    //     }

    //     try {
    //         if ($customer->connection_type == 'pppoe') {
    //             $router = Router::where('status', 'active')->where('id', $customer->router_id)->first();

    //             if (!$router) {
    //                 Log::error("Router not found for customer {$customer->username}");
    //                 return;
    //             }

    //             try {
    //                 $client = new Client([
    //                     'host' => $router->ip_address,
    //                     'user' => $router->username,
    //                     'pass' => $router->password,
    //                     'port' => (int)$router->port,
    //                     'timeout' => 3,
    //                     'attempts' => 1,
    //                 ]);

    //                 $interfaces = $client->query(new Query('/interface/print'))->read();
    //                 $sessions = $client->query(new Query('/ppp/active/print'))->read();

    //                 $uptime = $ip_address = $mac_address = $interface_name = 'N/A';
    //                 $session_id = null;

    //                 foreach ($sessions as $session) {
    //                     if ($session['name'] == $customer->username) {
    //                         $uptime = $session['uptime'] ?? 'N/A';
    //                         $ip_address = $session['address'] ?? 'N/A';
    //                         $mac_address = $session['caller-id'] ?? 'N/A';
    //                         $session_id = $session['session-id'] ?? null;
    //                         break;
    //                     }
    //                 }

    //                 // Find MAC from ARP
    //                 if ($ip_address != 'N/A') {
    //                     $arp_entries = $client->query(new Query('/ip/arp/print'))->read();
    //                     foreach ($arp_entries as $entry) {
    //                         if ($entry['address'] === $ip_address) {
    //                             $mac_address = $entry['mac-address'] ?? $mac_address;
    //                             break;
    //                         }
    //                     }
    //                 }

    //                 $rx_speed = $tx_speed = 0;

    //                 foreach ($interfaces as $intf) {
    //                     if (strpos($intf['name'], $customer->username) !== false) {
    //                         $interface_name = $intf['name'];

    //                         $monitor = $client->query(
    //                             (new Query('/interface/monitor-traffic'))
    //                                 ->equal('interface', $interface_name)
    //                                 ->equal('once', '')
    //                         )->read();

    //                         $rx_speed =round($intf['rx-byte'] / 1024 / 1024, 2);
    //                         $tx_speed = round($intf['tx-byte'] / 1024 / 1024, 2);
    //                         break;
    //                     }
    //                 }
    //                 if ($session_id) {
    //                     Daily_usages::updateOrCreate([
    //                         'session_id' => $session_id,
    //                         'customer_id' => $customer->id,
    //                     ], [
    //                         'router_id' => $customer->router_id,
    //                         // 'username' => $session['name'],
    //                         'ip' => $ip_address ?? null,
    //                         'mac' => $mac_address ?? null,
    //                         'upload' => $tx_speed,
    //                         'download' => $rx_speed ?? 0,
    //                         'date' => now(),
    //                         //'date' => date('Y-m-d'),
    //                     ]);
    //                 }

    //             } catch (\Exception $e) {
    //                  Log::error("Error for {$customer->username}: " . $e->getMessage());
    //             }

    //         } elseif ($customer->connection_type == 'radius') {

    //         } elseif ($customer->connection_type == 'hotspot') {

    //         }
    //     } catch (\Exception $e) {
    //         Log::error("Daily Usages check failed for customer {$customer->username}: " . $e->getMessage());
    //     }
    // }
}
