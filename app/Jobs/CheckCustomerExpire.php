<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Router;
use App\Models\Grace_recharge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use RouterOS\Client;
use RouterOS\Query;

class CheckCustomerExpire implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $router_id;

    public int $tries = 1;
    public int $timeout = 120;

    public function __construct(int $router_id)
    {
        $this->router_id = $router_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $lockKey = "expire:router:{$this->router_id}";
        $lock = Cache::lock($lockKey, 110);

        if (!$lock->get()) {
            Log::warning("Skip: another CheckCustomerExpire running for router {$this->router_id}");
            return;
        }

        try {
            $router = Router::find($this->router_id);
             Log::info("Expire Customer Jobs".$router);
            if (!$router || $router->status !== 'active') {
                Log::warning("Router not found or inactive: {$this->router_id}");
                return;
            }

            $today = Carbon::now()->format('Y-m-d');

            Customer::where('is_delete', '0')
                ->where('router_id', $this->router_id)
                ->whereDate('expire_date', '<=', $today)
                ->whereIn('status', ['active', 'online', 'offline'])
                ->orderBy('id')
                ->chunkById(200, function ($customers) use ($router) {


                    $client = null;
                    try {
                        $client = new Client([
                            'host'     => $router->ip_address,
                            'user'     => $router->username,
                            'pass'     => $router->password,
                            'port'     => (int) $router->port,
                            'timeout'  => 5,
                            'attempts' => 1,
                        ]);
                        $client->connect();
                    } catch (\Throwable $e) {
                        Log::error("Router ({$router->ip_address}) connection failed: ".$e->getMessage());
                        $client = null;
                    }

                    foreach ($customers as $customer) {
                        try {
                            // --- Grace recharge adjust  ---
                            $grace = Grace_recharge::where('customer_id', $customer->id)->first();
                            if ($grace && $customer->expire_date) {
                                $customer->expire_date = Carbon::parse($customer->expire_date)->subDays((int) $grace->days);
                                $customer->save();
                                $grace->delete();
                            }

                            // --- Connection-specific handling ---
                            switch ($customer->connection_type) {
                                case 'pppoe':
                                    if (!$client) {
                                        Log::warning("PPPoe skip (no router connection) for {$customer->username}");
                                        break;
                                    }
                                    $this->expirePppoeUser($client, $customer->username);
                                    $customer->update(['status' => 'expired']);
                                    Log::info("PPPoe expired: {$customer->username}");
                                    break;

                                case 'hotspot':
                                    if (!$client) {
                                        Log::warning("Hotspot skip (no router connection) for {$customer->username}");
                                        break;
                                    }
                                    $this->expireHotspotUser($client, $customer->username);
                                    $customer->update(['status' => 'expired']);
                                    Log::info("Hotspot expired & disabled: {$customer->username}");
                                    break;

                                case 'radius':
                                    \App\Models\Radius\Radcheck::where('username', $customer->username)->delete();
                                    \App\Models\Radius\Radreply::where('username', $customer->username)->delete();

                                    $customer->update(['status' => 'expired']);
                                    Log::info("RADIUS access removed & expired: {$customer->username}");
                                    break;

                                default:
                                    Log::warning("Unknown connection_type '{$customer->connection_type}' for {$customer->username}");
                                    break;
                            }
                        } catch (\Throwable $e) {
                            Log::error("Expire error for {$customer->username}: ".$e->getMessage());
                        }
                    }
                });

        } catch (\Throwable $e) {
            Log::error("Job failed for router {$this->router_id}: ".$e->getMessage());
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Disable PPPoE secret and remove active sessions.
     */
    protected function expirePppoeUser(Client $client, string $username): void
    {
        // Find PPP secret
        $qSecrets = (new Query('/ppp/secret/print'))->where('name', $username);
        $secrets = $client->query($qSecrets)->read();

        if (empty($secrets)) {
            Log::warning("PPP secret not found for {$username}");
            return;
        }

        $secretId = $secrets[0]['.id'] ?? null;
        if (!$secretId) {
            Log::warning("PPP secret id missing for {$username}");
            return;
        }

        // Remove all active sessions
        $qActive = (new Query('/ppp/active/print'))->where('name', $username);
        $active = $client->query($qActive)->read();

        foreach ($active as $session) {
            if (!empty($session['.id'])) {
                $client->query((new Query('/ppp/active/remove'))->equal('.id', $session['.id']))->read();
            }
        }

        // Disable secret
        $client->query(
            (new Query('/ppp/secret/set'))
                ->equal('.id', $secretId)
                ->equal('disabled', 'yes')
        )->read();
    }

    /**
     * Remove Hotspot active session and disable the user.
     */
    protected function expireHotspotUser(Client $client, string $username): void
    {
        // Remove active sessions
        $qActive = (new Query('/ip/hotspot/active/print'))->where('user', $username);
        $active = $client->query($qActive)->read();

        foreach ($active as $session) {
            if (!empty($session['.id'])) {
                $client->query((new Query('/ip/hotspot/active/remove'))->equal('.id', $session['.id']))->read();
            }
        }

        // Fetch user to get .id, then disable
        $qUser = (new Query('/ip/hotspot/user/print'))->where('name', $username);
        $users = $client->query($qUser)->read();

        if (empty($users)) {
            Log::warning("Hotspot user not found for {$username}");
            return;
        }

        $userId = $users[0]['.id'] ?? null;
        if (!$userId) {
            Log::warning("Hotspot user id missing for {$username}");
            return;
        }

        $client->query(
            (new Query('/ip/hotspot/user/set'))
                ->equal('.id', $userId)
                ->equal('disabled', 'yes')
        )->read();
    }
}
