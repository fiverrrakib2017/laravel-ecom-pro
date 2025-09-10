<?php
namespace App\Helpers;

use App\Models\Branch_transaction;
use App\Models\Customer;
use App\Models\Customer_log;
use App\Models\Sms_configuration;
use App\Models\Customer_recharge;
use App\Models\Router as Mikrotik_router;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

if (!function_exists('check_pop_balance')) {
    function check_pop_balance($pop_id)
    {
        $total_balance = Branch_transaction::where('pop_id', $pop_id)->where('transaction_type', '!=', 'due_paid')->sum('amount');

        $total_customer_recharge = Customer_recharge::where('pop_id', $pop_id)->where('transaction_type', '!=', 'due_paid')->sum('amount');

        return $total_balance - $total_customer_recharge;
    }
}
if (!function_exists('fetch_customer_data')) {
    function fetch_customer_data(Request $request, $isDeleteCondition)
    {
        $search = $request->search['value'] ?? '';
        $columnsForOrderBy = ['id', 'id', 'fullname', 'package', 'amount', 'created_at', 'expire_date', 'username', 'phone', 'pop_id', 'area_id', 'created_at', 'created_at'];

        $orderByColumn = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';

        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        $query = Customer::with(['pop', 'area', 'package'])
            ->where('is_delete', '!=', $isDeleteCondition)
            ->when($search, function ($query) use ($search) {
                $query
                    ->where('phone', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->orWhereHas('pop', function ($query) use ($search) {
                        $query->where('fullname', 'like', "%$search%");
                    })
                    ->orWhereHas('area', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('package', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            });

        /*Pagination*/
        $paginatedData = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirection)->paginate($length, ['*'], 'page', $start / $length + 1);

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => Customer::where('is_delete', '!=', $isDeleteCondition)->count(),
            'recordsFiltered' => $paginatedData->total(),
            'data' => $paginatedData->items(),
        ]);
    }
}
if (!function_exists('send_message')) {
    function send_message($phone_number, $message_text)
    {
        /** SMS API Details **/
        $sms_config = Sms_configuration::latest()->first();
        $api_url = $sms_config->api_url;
        $api_key = $sms_config->api_key;
        $senderid = $sms_config->sender_id;

        /* Prepare data */
        $data = [
            'api_key' => $api_key,
            'senderid' => $senderid,
            'number' => $phone_number,
            'message' => $message_text,
        ];

        /* Initialize cURL */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        /* Execute request */
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        return $responseData;
    }
}

function customer_log($customerId, $actionType, $userId, $description = null)
{
    $object = new Customer_log();
    $object->customer_id = $customerId;
    $object->action_type = $actionType;
    $object->user_id = $userId;
    $object->description = $description;
    $object->ip_address = request()->ip();
    $object->save();
    return $object;
}
if (!function_exists('get_mikrotik_user_info')) {
    function get_mikrotik_user_info($router, $username)
    {
        try {
            $API = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port,
            ]);

            // Active user info (uptime, IP, MAC, interface)
            $activeQuery = new Query('/ppp/active/print');
            $activeQuery->where('name', $username);
            $active = $API->query($activeQuery)->read()[0] ?? [];

            $interfaceName = $active['interface'] ?? null;

            // User profile info (from /ppp/secret)
            $secretQuery = new Query('/ppp/secret/print');
            $secretQuery->where('name', $username);
            $secret = $API->query($secretQuery)->read()[0] ?? [];

            // Profile speed (upload/download limit)
            $profileName = $secret['profile'] ?? null;
            $profileInfo = [];
            if ($profileName) {
                $profileQuery = new Query('/ppp/profile/print');
                $profileQuery->where('name', $profileName);
                $profileInfo = $API->query($profileQuery)->read()[0] ?? [];
            }

            // Monthly usage estimate
            $monthlyUsage = null;
            if ($interfaceName) {
                $interfaceList = $API->query(new Query('/interface/print'))->read();
                $interfaceId = null;

                foreach ($interfaceList as $intf) {
                    if ($intf['name'] === $interfaceName) {
                        $interfaceId = $intf['.id'];
                        break;
                    }
                }

                if ($interfaceId) {
                    $monitorQuery = new Query('/interface/monitor-traffic');
                    $monitorQuery->equal('.id', $interfaceId);
                    $monitorQuery->equal('once', '');
                    $usage = $API->query($monitorQuery)->read()[0] ?? [];

                    $rx = $usage['rx-byte'] ?? 0;
                    $tx = $usage['tx-byte'] ?? 0;
                    $monthlyUsage = $rx + $tx;
                }
            }

            // Upload & Download speed parsing
            $uploadSpeed = null;
            $downloadSpeed = null;
            if (isset($profileInfo['rate-limit'])) {
                $limits = explode('/', $profileInfo['rate-limit']);
                $uploadSpeed = $limits[0] ?? null;
                $downloadSpeed = $limits[1] ?? null;
            }

            return [
                'interface' => $interfaceName,
                'uptime' => $active['uptime'] ?? null,
                'mac' => $active['caller-id'] ?? null,
                'ip' => $active['address'] ?? null,
                'upload_speed' => $uploadSpeed,
                'download_speed' => $downloadSpeed,
                'monthly_usage' => $monthlyUsage,
                'profile' => $profileName,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }


}


if (!function_exists('formate_uptime')) {
    function formate_uptime($uptime)
    {
        $days = 0;
        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        if (preg_match('/(?:(\d+)d)?(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/', $uptime, $matches)) {
            $days = isset($matches[1]) ? (int) $matches[1] : 0;
            $hours = isset($matches[2]) ? (int) $matches[2] : 0;
            $minutes = isset($matches[3]) ? (int) $matches[3] : 0;
            $seconds = isset($matches[4]) ? (int) $matches[4] : 0;
        }

        return "{$days} d {$hours} h {$minutes} m {$seconds} sec";
    }
}

if (!function_exists('router_activation')) {
    function router_activation($customer_id)
    {
         $customer=Customer::find($customer_id);
        $router = Mikrotik_router::where('status', 'active')->find($customer->router_id);
        if ($router) {
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port ?? 8728,
            ]);

            try {
                $client->connect();

                // Find secret
                $secretQuery = (new Query('/ppp/secret/print'))->where('name', $customer->username);
                $secrets = $client->query($secretQuery)->read();

                if (!empty($secrets)) {
                    $secretId = $secrets[0]['.id'];
                    $isDisabled = $secrets[0]['disabled'] ?? 'false';

                    if ($isDisabled === 'true') {
                        /* Enable secret*/
                        $enableQuery = (new Query('/ppp/secret/set'))
                            ->equal('.id', $secretId)
                            ->equal('disabled', 'no');
                        $client->query($enableQuery)->read();
                    }

                    $activeQuery = (new Query('/ppp/active/print'))->where('name', $customer->username);
                    $activeUser = $client->query($activeQuery)->read();

                    if (!empty($activeUser)) {
                        $activeId = $activeUser[0]['.id'];
                        $removeQuery = (new Query('/ppp/active/remove'))->equal('.id', $activeId);
                        $client->query($removeQuery)->read();
                    }

                    /* Status update*/
                    $customer->status = 'online';
                    $customer->save();
                }
            } catch (\Exception $e) {
                \Log::error("Router connection or enabling failed: " . $e->getMessage());
            }
        }
    }
}

