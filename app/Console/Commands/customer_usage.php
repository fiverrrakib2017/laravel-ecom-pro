<?php

namespace App\Console\Commands;
use App\Jobs\Customer_usages;
use RouterOS\Client;
use RouterOS\Query;
use App\Models\Customer;
use App\Models\Router;

use Illuminate\Console\Command;
use function App\Helpers\formate_uptime;
class customer_usage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:customer_usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch daily user usage from MikroTik';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('--- Customer Daily Usages Started ---');
        $customers = Customer::where('is_delete', '0')->where('status', '!=', 'expired')->where('status', '!=', 'disabled')->where('status', '!=', 'discontinue')->get();
            foreach($customers as $customer){
                dispatch(new Customer_usages ($customer->id));
            }
        $this->info('--- Customer Daily Usages  Finished ---');
    }
    protected function get_customer_daily_usage()
    {
        $customers = Customer::where('is_delete', '0')
            ->whereNotIn('status', ['expired', 'disabled', 'discontinue'])
            ->get();

        foreach ($customers as $customer) {
            if ($customer->connection_type != 'pppoe') {
                continue;
            }

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
                    'port' => (int)$router->port,
                    'timeout' => 3,
                    'attempts' => 1,
                ]);

                $interfaces = $client->query(new Query('/interface/print'))->read();
                $sessions = $client->query(new Query('/ppp/active/print'))->read();

                $uptime = $ip_address = $mac_address = $interface_name = 'N/A';
                $session_id = null;

                foreach ($sessions as $session) {
                    if ($session['name'] == $customer->username) {
                        $uptime = $session['uptime'] ?? 'N/A';
                        $ip_address = $session['address'] ?? 'N/A';
                        $mac_address = $session['caller-id'] ?? 'N/A';
                        $session_id = $session['session-id'] ?? null;
                        break;
                    }
                }

                // Find MAC from ARP
                if ($ip_address != 'N/A') {
                    $arp_entries = $client->query(new Query('/ip/arp/print'))->read();
                    foreach ($arp_entries as $entry) {
                        if ($entry['address'] === $ip_address) {
                            $mac_address = $entry['mac-address'] ?? $mac_address;
                            break;
                        }
                    }
                }

                $rx_speed = $tx_speed = 0;

                foreach ($interfaces as $intf) {
                    if (strpos($intf['name'], $customer->username) !== false) {
                        $interface_name = $intf['name'];

                        $monitor = $client->query(
                            (new Query('/interface/monitor-traffic'))
                                ->equal('interface', $interface_name)
                                ->equal('once', '')
                        )->read();

                        $rx_speed =round($intf['rx-byte'] / 1024 / 1024, 2);
                        $tx_speed = round($intf['tx-byte'] / 1024 / 1024, 2);
                        break;
                    }
                }

                if ($session_id) {
                    Daily_usages::updateOrCreate([
                        'session_id' => $session_id,
                        'customer_id' => $customer->id,
                    ], [
                        'router_id' => $customer->router_id,
                        // 'username' => $session['name'],
                        'ip' => $ip_address ?? null,
                        'mac' => $mac_address ?? null,
                        'upload' => $tx_speed,
                        'download' => $rx_speed ?? 0,
                        'date' => now(),
                        //'date' => date('Y-m-d'),
                    ]);
                }

            } catch (\Exception $e) {
                $this->error("Error for {$customer->username}: " . $e->getMessage());
            }
        }
    }
}
