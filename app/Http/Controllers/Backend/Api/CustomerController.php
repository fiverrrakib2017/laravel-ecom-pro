<?php
namespace App\Http\Controllers\Backend\Api;
use App\Events\customer_bandwith_update;
use App\Http\Controllers\Controller;
use App\Models\Branch_package;
use App\Models\Branch_transaction;
use App\Models\Customer;
use App\Models\Customer_device;
use App\Models\Customer_log;
use App\Models\Customer_recharge;
use App\Models\Grace_recharge;
use App\Models\Router as Mikrotik_router;
use App\Models\Send_message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use function App\Helpers\check_pop_balance;
use function App\Helpers\customer_log;
use function App\Helpers\formate_uptime;
use function App\Helpers\get_mikrotik_user_info;
use function App\Helpers\send_message;
use function App\Helpers\router_activation;
use function App\Helpers\delete_mikrotik_user;
use function App\Helpers\add_mikrotik_user;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;
class CustomerController extends Controller
{
    /**
     * @OA\Get(
     *     path="https://isperp.xyz/api/v1/admin/customer/",
     *     summary="Get all customers with filters",
     *     tags={"Customer"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by phone, username, POP name, area name, package name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="pop_id",
     *         in="query",
     *         description="Filter by POP ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="area_id",
     *         in="query",
     *         description="Filter by Area ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status (active, expired, offline, online, grace)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="connection_type",
     *         in="query",
     *         description="Filter by connection type (pppoe, hotspot, etc.)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of customers",
     *         @OA\JsonContent(
     *             @OA\Property(property="draw", type="integer", example=0),
     *             @OA\Property(property="recordsTotal", type="integer", example=495),
     *             @OA\Property(property="recordsFiltered", type="integer", example=846),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=893),
     *                     @OA\Property(property="fullname", type="string", example="kashem"),
     *                     @OA\Property(property="phone", type="string", example="01880045308"),
     *                     @OA\Property(property="username", type="string", example="RAJAKASHEM"),
     *                     @OA\Property(property="status", type="string", example="offline"),
     *                     @OA\Property(property="connection_type", type="string", example="pppoe"),
     *                     @OA\Property(property="expire_date", type="string", format="date", example="2025-10-19"),
     *                     @OA\Property(
     *                         property="pop",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="musa")
     *                     ),
     *                     @OA\Property(
     *                         property="area",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="name", type="string", example="Baiora")
     *                     ),
     *                     @OA\Property(
     *                         property="package",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="MUSA7")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function all_customers(Request $request)
    {
        $query = Customer::with(['pop', 'area', 'package'])->where('is_delete', '!=', 1);

        /*----------- Optional filters----------*/
        if ($request->has('pop_id')) {
            $query->where('pop_id', $request->pop_id);
        }
        if ($request->has('area_id')) {
            $query->where('area_id', $request->area_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Fetch all
        $customers = $query->get();

        return response()->json([
            'total' => $customers->count(),
            'data' => $customers,
        ]);
    }
    public function check_customer_user(Request $request)
    {
        $exists = DB::table('customers')->where('username', $request->username)->exists();

        return response()->json([
            'available' => !$exists,
        ]);
    }
    /**
 * @OA\Get(
 *     path="https://isperp.xyz/api/v1/admin/customer/search",
 *     operationId="customerSearch",
 *     tags={"Customer"},
 *     summary="Search customers by username, id, fullname or phone",
 *     description="Returns a list of customers matching the search query",
 *     @OA\Parameter(
 *         name="query",
 *         in="query",
 *         description="Search term for customer username, id, fullname or phone",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful search",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="username", type="string", example="john_doe"),
 *                     @OA\Property(property="fullname", type="string", example="John Doe"),
 *                     @OA\Property(property="phone", type="string", example="01712345678"),
 *                     @OA\Property(property="nid", type="string", example="1234567890"),
 *                     @OA\Property(property="address", type="string", example="Dhaka"),
 *                     @OA\Property(property="package_id", type="integer", example=3),
 *                     @OA\Property(property="pop_id", type="integer", example=2),
 *                     @OA\Property(property="area_id", type="integer", example=5),
 *                     @OA\Property(property="status", type="string", example="active"),
 *                     @OA\Property(property="connection_type", type="string", example="pppoe"),
 *                     @OA\Property(property="expire_date", type="string", format="date", example="2025-12-31")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Query parameter is required")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */

    public function customer_search(Request $request)
    {
        $query = $request->input('query');
        $customers = Customer::where('username', 'like', "%$query%")
            ->orWhere('id', 'like', "%$query%")
            ->orWhere('fullname', 'like', "%$query%")
            ->orWhere('phone', 'like', "%$query%")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }
    /**
 * @OA\Post(
 *     path="http://isperp.xyz/api/v1/admin/customer/store",
 *     operationId="storeCustomer",
 *     tags={"Customer"},
 *     summary="Create a new customer",
 *     description="Creates a new customer with all details including devices, auto recharge, and messaging options",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="fullname", type="string", example="John Doe"),
 *             @OA\Property(property="phone", type="string", example="01712345678"),
 *             @OA\Property(property="nid", type="string", example="1234567890"),
 *             @OA\Property(property="address", type="string", example="Dhaka"),
 *             @OA\Property(property="con_charge", type="number", example=0),
 *             @OA\Property(property="amount", type="number", example=500),
 *             @OA\Property(property="username", type="string", example="john_doe"),
 *             @OA\Property(property="password", type="string", example="123456"),
 *             @OA\Property(property="package_id", type="integer", example=3),
 *             @OA\Property(property="pop_id", type="integer", example=1),
 *             @OA\Property(property="area_id", type="integer", example=2),
 *             @OA\Property(property="router_id", type="integer", example=1),
 *             @OA\Property(property="status", type="string", example="active", enum={"active","online","offline","blocked","expired","disabled"}),
 *             @OA\Property(property="expire_date", type="string", format="date", example="2025-12-31"),
 *             @OA\Property(property="remarks", type="string", example="New customer"),
 *             @OA\Property(property="connection_type", type="string", example="pppoe", enum={"pppoe","radius","hotspot"}),
 *             @OA\Property(property="liabilities", type="string", example="NO", enum={"YES","NO"}),
 *             @OA\Property(property="auto_recharge", type="boolean", example=true),
 *             @OA\Property(property="send_message", type="boolean", example=true),
 *             @OA\Property(
 *                 property="device_type",
 *                 type="array",
 *                 @OA\Items(type="string", example="Router")
 *             ),
 *             @OA\Property(
 *                 property="device_name",
 *                 type="array",
 *                 @OA\Items(type="string", example="TP-Link")
 *             ),
 *             @OA\Property(
 *                 property="serial_no",
 *                 type="array",
 *                 @OA\Items(type="string", example="12345XYZ")
 *             ),
 *             @OA\Property(
 *                 property="assign_date",
 *                 type="array",
 *                 @OA\Items(type="string", format="date", example="2025-10-19")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Customer created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Customer Created Successfully!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
                    type="object",
                    example={
                        "phone": {"The phone has already been taken."},
                        "username": {"The username has already been taken."}
                    }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Something went wrong while creating the customer. Please try again!"),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */

    public function store(Request $request)
    {
        /* Validate the form data */
        $this->validateForm($request);
        DB::beginTransaction();
        try {
            /* Create a new Customer */
            $customer = new Customer();
            $customer->fullname = $request->fullname;
            $customer->phone = $request->phone;
            $customer->nid = $request->nid;
            $customer->address = $request->address;
            $customer->con_charge = $request->con_charge ?? 0;
            $customer->amount = $request->amount ?? 0;
            $customer->username = $request->username;
            $customer->password = $request->password;
            $customer->package_id = $request->package_id;
            $customer->pop_id = $request->pop_id;
            $customer->area_id = $request->area_id;
            $customer->router_id = $request->router_id;
            $customer->status = $request->status;
            $customer->expire_date = date('Y-m-d');
            $customer->remarks = $request->remarks;
            $customer->connection_type = $request->connection_type;
            $customer->liabilities = $request->liabilities;
            /* First save customer to get the ID */
            $customer->save();

            /*-----------Customer Auto Recharge----------*/
            if ($request->auto_recharge == '1') {
                /* Check Pop Balance */
                $pop_balance = check_pop_balance($request->pop_id);
                if ($pop_balance < $request->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pop balance is not enough',
                    ]);
                }
                /* Store recharge data */
                $object = new Customer_recharge();
                $object->user_id = auth()->guard('sanctum')->user()->id;
                $object->customer_id = $customer->id;
                $object->pop_id = $request->pop_id;
                $object->area_id = $request->area_id;
                $object->recharge_month = implode(',', [date('Y-m')]);
                $object->transaction_type = 'cash';
                $object->paid_until = date('Y-m-d', strtotime('+1 month'));
                $object->amount = $request->amount;
                $object->note = 'Created';
                $object->save();

                /* Update customer expire date */
                $customer->expire_date = $object->paid_until;
                $customer->save();
            }
            /* Send Message to the Customer*/
            if ($request->send_message == '1') {
                $user = auth()->guard('sanctum')->user();
                $data = \App\Models\Website_information::where('pop_id', $user->pop_id)->latest()->first();
                if ($user->pop_id === null) {
                    $data = \App\Models\Website_information::whereNull('pop_id')->latest()->first();
                }
                $_app_name = $data->name ?? '';
                $message = "($_app_name)\n\n" . "ID: {$customer->id}\n" . "Name: {$customer->fullname}\n" . "Username: {$customer->username}\n" . "Password: {$customer->password}\n" . "Amount: {$customer->amount}\n" . 'Thanks for your joining';
                /* Create a new Instance*/
                $send_message = new Send_message();
                $send_message->pop_id = $customer->pop_id;
                $send_message->area_id = $customer->area_id;
                $send_message->customer_id = $customer->id;
                $send_message->message = $message;
                $send_message->sent_at = Carbon::now();
                /*Call Send Message Function */
                send_message($customer->phone, $message);
                /* Save to the database table*/
                $send_message->save();
            }
            /* Customer Device Liabilities store database table*/
            if (!empty($request->device_type) && count($request->device_type) > 0) {
                foreach ($request->device_type as $index => $type) {
                    /*check customer device validation ---select--- null data*/
                    if ($type === '---Select---' || empty($type)) {
                        continue;
                    }
                    $customer_device = new Customer_device();
                    $customer_device->customer_id = $customer->id;
                    $customer_device->user_id = auth()->guard('sanctum')->user()->id;
                    $customer_device->device_type = $type;
                    $customer_device->device_name = $request->device_name[$index] ?? null;
                    $customer_device->serial_number = $request->serial_no[$index] ?? null;
                    $customer_device->assigned_date = $request->assign_date[$index] ?? null;
                    $customer_device->pop_id = $request->pop_id;
                    $customer_device->area_id = $request->area_id;
                    $customer_device->save();
                }
            }

            /* Create Customer Log */
            customer_log($customer->id, 'add', auth()->guard('sanctum')->user()->id, 'Customer Created Successfully!');
            /*Check Customer Connection Type*/
            if (!empty($request->connection_type) && isset($request->connection_type)) {
                /*********** Radius Customer Store ****************/
                if ($request->connection_type == 'radius') {
                    /* Create Customer Radius Server */
                    $existing_racheck = \App\Models\Radius\Radcheck::where('username', $request->username)->first();
                    if (!$existing_racheck) {
                        $radius = new \App\Models\Radius\Radcheck();
                        $radius->username = $request->username;
                        $radius->attribute = 'Cleartext-Password';
                        $radius->op = ':=';
                        $radius->value = $request->password;
                        $radius->save();
                    }
                    $existing_radreply = \App\Models\Radius\Radreply::where('username', $request->username)->first();
                    if (!$existing_radreply) {
                        $radreply = new \App\Models\Radius\Radreply();
                        $radreply->username = $request->username;
                        $radreply->attribute = 'MikroTik-Group';
                        $radreply->op = ':=';
                        $radreply->value = Branch_package::find($request->package_id)->name;
                        $radreply->save();
                    }
                }

                /*********** PPPOE Customer Store ****************/
                if ($request->connection_type == 'pppoe') {
                    add_mikrotik_user($customer->id);
                }
                /*********** Hotspot Customer Store ****************/
                if ($request->connection_type == 'hotspot') {
                }
            }
            DB::commit();
            Cache::forget('sidebar_customers');
            return response()->json([
                'success' => true,
                'message' => 'Customer Created Successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Customer Creation Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while creating the customer. Please try again!',
                'error' => $e->getMessage(),
            ]);
        }
    }
    /**
 * @OA\Post(
 *     path="http://isperp.xyz/api/v1/admin/customer/update/{id}",
 *     operationId="updateCustomer",
 *     tags={"Customer"},
 *     summary="Update an existing customer",
 *     description="Updates the details of an existing customer including personal information, package, status, and devices",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the customer to update",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="fullname", type="string", example="John Doe"),
 *             @OA\Property(property="phone", type="string", example="01712345678"),
 *             @OA\Property(property="nid", type="string", example="1234567890"),
 *             @OA\Property(property="address", type="string", example="Dhaka"),
 *             @OA\Property(property="con_charge", type="number", example=0),
 *             @OA\Property(property="amount", type="number", example=500),
 *             @OA\Property(property="username", type="string", example="john_doe"),
 *             @OA\Property(property="password", type="string", example="123456"),
 *             @OA\Property(property="package_id", type="integer", example=3),
 *             @OA\Property(property="pop_id", type="integer", example=1),
 *             @OA\Property(property="area_id", type="integer", example=2),
 *             @OA\Property(property="router_id", type="integer", example=1),
 *             @OA\Property(property="status", type="string", example="active", enum={"active", "online", "offline", "blocked", "expired", "disabled"}),
 *             @OA\Property(property="expire_date", type="string", format="date", example="2025-12-31"),
 *             @OA\Property(property="remarks", type="string", example="Customer Update"),
 *             @OA\Property(property="connection_type", type="string", example="pppoe", enum={"pppoe", "radius", "hotspot"}),
 *             @OA\Property(property="liabilities", type="string", example="NO", enum={"YES", "NO"}),
 *             @OA\Property(
 *                 property="device_type",
 *                 type="array",
 *                 @OA\Items(type="string", example="Router")
 *             ),
 *             @OA\Property(
 *                 property="device_name",
 *                 type="array",
 *                 @OA\Items(type="string", example="TP-Link")
 *             ),
 *             @OA\Property(
 *                 property="serial_no",
 *                 type="array",
 *                 @OA\Items(type="string", example="12345XYZ")
 *             ),
 *             @OA\Property(
 *                 property="assign_date",
 *                 type="array",
 *                 @OA\Items(type="string", format="date", example="2025-10-19")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Customer updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Customer Updated Successfully!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "phone": {"The phone has already been taken."},
 *                     "username": {"The username has already been taken."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Something went wrong while updating the customer. Please try again!"),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */

    public function update(Request $request, $id)
    {
        /* Validate the form data*/
        $this->validateForm($request);

        DB::beginTransaction();

        try {
            /* update Customer */
            $customer = Customer::findOrFail($id);
            $customer->fullname = $request->fullname;
            $customer->phone = $request->phone;
            $customer->nid = $request->nid;
            $customer->address = $request->address;
            $customer->con_charge = $request->con_charge ?? 0;
            $customer->amount = $request->amount ?? 0;
            $customer->username = $request->username;
            $customer->password = $request->password;
            $customer->package_id = $request->package_id;
            $customer->expire_date = $request->expire_date;
            $customer->pop_id = $request->pop_id;
            $customer->area_id = $request->area_id;
            $customer->router_id = $request->router_id;
            $customer->status = $request->status;
            $customer->remarks = $request->remarks;
            $customer->liabilities = $request->liabilities;

            /*********** Mikrotik Info ***************/
            $router = Mikrotik_router::where('status', 'active')->where('id', $request->router_id)->first();

            if (!$router) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Router not found or inactive.',
                ]);
            }

            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port ?? 8728,
            ]);

            /*Load MikroTik profile list*/
            $profileQuery = new Query('/ppp/profile/print');
            $profiles = $client->query($profileQuery)->read();

            /*Get profile name from selected package*/
            $profileName = Branch_package::find($request->package_id)->name;

            /*Check if profile exists in MikroTik*/
            $profileExists = collect($profiles)->pluck('name')->contains($profileName);

            if (!$profileExists) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "MikroTik profile '{$profileName}' does not exist. Please check your package setup.",
                ]);
            }

            /*Find user in MikroTik*/
            $check_Query = new Query('/ppp/secret/print');
            $check_Query->where('name', $request->username);
            $check_customer = $client->query($check_Query)->read();

            if (empty($check_customer)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Customer '{$request->username}' not found in MikroTik router.",
                ]);
            }

            /*Update the MikroTik user's profile*/
            $secretId = $check_customer[0]['.id'];
            $updateQuery = new Query('/ppp/secret/set');
            $updateQuery->equal('.id', $secretId);
            $updateQuery->equal('profile', $profileName);
            $client->query($updateQuery)->read();

            // Optional: Update password too
            $updateQuery->equal('password', $request->password ?? '12345');

            /* Save to database */
            $customer->save();

            DB::commit();

            Cache::forget('sidebar_customers');

            // Log
            customer_log($customer->id, 'edit', auth()->guard('sanctum')->user()->id, 'Customer updated successfully!');

            return response()->json([
                'success' => true,
                'message' => 'Update successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ]);
        }
    }
    /**
 * @OA\Post(
 *     path="http://isperp.xyz/api/v1/admin/customer/change-expire-date",
 *     operationId="customerChangeExpireDate",
 *     tags={"Customer"},
 *     summary="Bulk update customer expire dates",
 *     description="Updates the expire date of multiple customers in a bulk operation and activates the associated router for each customer",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="customer_ids", type="array",
 *                 @OA\Items(type="integer", example=1)
 *             ),
 *             @OA\Property(property="customer_expire_date", type="string", format="date", example="2025-12-31")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Expire dates updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Update Successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "customer_ids": {"The customer ids field is required."},
 *                     "customer_expire_date": {"The customer expire date field is required."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Something went wrong during bulk recharge."),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */

    public function customer_change_expire_date(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'customer_expire_date'  => 'required',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->customer_ids as $customer_id) {
                /*update Customer Expire Date*/
                $customer = Customer::find($customer_id);
                if ($customer && $customer->expire_date) {
                    $customer->expire_date = $request->customer_expire_date;
                    $customer->update();
                }
                /*Activate rouater customer*/
                router_activation($customer_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Update Successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong during bulk recharge.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Post(
 *     path="http://isperp.xyz/api/v1/admin/customer/change-package",
 *     operationId="customerChangePackage",
 *     tags={"Customer"},
 *     summary="Bulk change customer package (and POP/Area)",
 *     description="Updates pop_id, area_id, and package_id for multiple customers in bulk and activates each customer on the router.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"customer_ids","pop_id","area_id","customer_package_id"},
 *             @OA\Property(
 *                 property="customer_ids",
 *                 type="array",
 *                 @OA\Items(type="integer", example=1)
 *             ),
 *             @OA\Property(property="pop_id", type="integer", example=5),
 *             @OA\Property(property="area_id", type="integer", example=12),
 *             @OA\Property(property="customer_package_id", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Packages updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Update Successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "customer_ids": {"The customer ids field is required."},
 *                     "pop_id": {"The pop id field is required."},
 *                     "area_id": {"The area id field is required."},
 *                     "customer_package_id": {"The customer package id field is required."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Something went wrong during bulk recharge."),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */

    public function customer_change_pacakge(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'pop_id'  => 'required',
            'area_id'  => 'required',
            'customer_package_id'  => 'required',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->customer_ids as $customer_id) {
                /*update Customer Expire Date*/
                $customer = Customer::find($customer_id);
                if ($customer && $customer->pop_id && $customer->area_id && $customer->customer_package_id) {
                    $customer->pop_id = $request->pop_id;
                    $customer->area_id = $request->area_id;
                    $customer->package_id = $request->customer_package_id;
                    $customer->update();
                }
                /*Activate rouater customer*/
                router_activation($customer_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Update Successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong during bulk recharge.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Post(
 *     path="http://isperp.xyz/api/v1/admin/customer/bulk-reconnect",
 *     operationId="customerBulkReconnect",
 *     tags={"Customer"},
 *     summary="Bulk reconnect customers (router activation)",
 *     description="Activates the router for each customer provided in the list of customer IDs.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"customer_ids"},
 *             @OA\Property(
 *                 property="customer_ids",
 *                 type="array",
 *                 @OA\Items(type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reconnection completed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Successfully Completed.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "customer_ids": {"The customer ids field is required."},
 *                     "customer_ids.0": {"The selected customer_ids.0 is invalid."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Something went."),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */

    public function bulk_customer_re_connect(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->customer_ids as $customer_id) {
                /*Activate rouater customer*/
                router_activation($customer_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Successfully Completed.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Get(
 *     path="http://isperp.xyz/api/v1/admin/customer/discountinue/{customer_id}",
 *     operationId="customerDiscountinue",
 *     tags={"Customer"},
 *     summary="Discontinue a customer account",
 *     description="Marks the customer as 'discontinue'. For PPPoE, removes active and secret entries from MikroTik. For RADIUS, sets status to 'discontinue' only if no active session is found.",
 *     @OA\Parameter(
 *         name="customer_id",
 *         in="path",
 *         required=true,
 *         description="ID of the customer to discontinue",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Operation completed",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Successfully Completed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Internal server error"),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */

    public function customer_discountinue($customer_id)
    {
        $customers = Customer::where('is_delete', '0')
            ->where('id', $customer_id)
            ->where('status', '!=', 'discontinue')
            ->get();

        foreach ($customers as $customer) {

            // PPPoE Customer
          if ($customer->connection_type == 'pppoe') {
                $router = Mikrotik_router::where('status', 'active')
                    ->where('id', $customer->router_id)
                    ->first();

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

                    /*Remove from PPP Active*/
                    $activeQuery = new Query('/ppp/active/print');
                    $activeQuery->where('name', $customer->username);
                    $activeUsers = $client->query($activeQuery)->read();

                    if (!empty($activeUsers)) {
                        foreach ($activeUsers as $activeUser) {
                            if (isset($activeUser['.id'])) {
                                $removeActive = new Query('/ppp/active/remove');
                                $removeActive->equal('.id', $activeUser['.id']);
                                $client->query($removeActive)->read();
                            }
                        }
                    }
                    $secretQuery = new Query('/ppp/secret/print');
                    $secretQuery->where('name', $customer->username);
                    $secretUsers = $client->query($secretQuery)->read();

                    if (!empty($secretUsers)) {
                        foreach ($secretUsers as $secretUser) {
                            if (isset($secretUser['.id'])) {
                                $removeSecret = new Query('/ppp/secret/remove');
                                $removeSecret->equal('.id', $secretUser['.id']);
                                $client->query($removeSecret)->read();
                            }
                        }
                    }
                    $customer->update(['status' => 'discontinue']);
                } catch (\Exception $e) {
                    $this->error("Connection error for router {$router->ip_address}: " . $e->getMessage());
                }
            }
            /*Radius Customer*/
            elseif ($customer->connection_type == 'radius') {
                $activeSession = \App\Models\Radius\Radacct::where('username', $customer->username)
                    ->whereNull('acctstoptime')
                    ->latest('acctstarttime')
                    ->first();

                if (empty($activeSession)) {
                    $customer->update(['status' => 'discontinue']);
                    $this->info("RADIUS Customer {$customer->username} marked as DISCONTINUE (no active session)");
                } else {
                    $this->info("RADIUS Customer {$customer->username} is still ONLINE (active session found)");
                }
            }
            elseif ($customer->connection_type == 'hotspot') {
            }
        }

        return response(['success' => true, 'message' => 'Successfully Completed']);
    }
    /**
 * @OA\Post(
 *     path="http://isperp.xyz/api/v1/admin/customer/recharge",
 *     operationId="customerRecharge",
 *     tags={"Customer"},
 *     summary="Recharge a customer or record due payment",
 *     description="Handles normal recharges (cash/credit/bkash/etc.) and due payments. Validates months (YYYY-MM), updates paid_until & customer expire_date, optionally sends SMS, and triggers router activation.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"customer_id","pop_id","area_id","payable_amount","recharge_month","transaction_type"},
 *             @OA\Property(property="customer_id", type="integer", example=101),
 *             @OA\Property(property="pop_id", type="integer", example=5),
 *             @OA\Property(property="area_id", type="integer", example=12),
 *             @OA\Property(property="payable_amount", type="number", format="float", example=800.00),
 *             @OA\Property(
 *                 property="recharge_month",
 *                 type="array",
 *                 description="Month(s) to recharge in YYYY-MM format",
 *                 @OA\Items(type="string", pattern="^\d{4}-(0[1-9]|1[0-2])$", example="2025-05")
 *             ),
 *             @OA\Property(
 *                 property="transaction_type",
 *                 type="string",
 *                 description="Recharge type; use 'due_paid' to pay outstanding credit",
 *                 example="cash",
 *                 enum={"cash","credit","bkash","nagad","rocket","bank","due_paid"}
 *             ),
 *             @OA\Property(property="voucher_no", type="string", nullable=true, example="VCH-2025-0001"),
 *             @OA\Property(property="note", type="string", nullable=true, example="Monthly recharge"),
 *             @OA\Property(
 *                 property="send_message",
 *                 type="boolean",
 *                 description="If true, sends a confirmation SMS to the customer",
 *                 example=true
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Recharge or due payment processed",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Recharge successfully.")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Due paid successfully.")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(
 *                         property="errors",
 *                         type="object",
 *                         example={
 *                             "customer_id": {"The customer id field is required."},
 *                             "pop_id": {"The pop id field is required."},
 *                             "area_id": {"The area id field is required."},
 *                             "payable_amount": {"The payable amount must be a number."},
 *                             "recharge_month": {"The recharge month field is required."},
 *                             "transaction_type": {"The transaction type field is required."}
 *                         }
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="Invalid month format: 2025-13. Valid format is YYYY-MM (e.g. 2025-05)")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="Please select at least one month for recharge.")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Business rule conflict (duplicate month, insufficient balance, invalid due amount, etc.)",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="Recharge for month October 2025 already exists.")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="Pop balance is not enough")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="No due found for this customer.")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="Invalid due paid amount.")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="Due already paid for October 2025.")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Server error occurred during recharge."),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */

    public function customer_recharge(Request $request)
    {
        $rules = [
            'customer_id'       => 'required',
            'pop_id'            => 'required',
            'area_id'           => 'required',
            'payable_amount'    => 'required|numeric',
            'recharge_month'    => 'required|array',
            'transaction_type'  => 'required',
            'voucher_no'        => 'nullable',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success'   => false,
                    'errors'    => $validator->errors(),
                ],
                422,
            );
        }
        /*Check if Recharge Month is empty*/
        if (empty($request->recharge_month)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one month for recharge.',
            ]);
        }
        /* Check if Recharge Month is valid */
        $validMonths = [];

        foreach ($request->recharge_month as $monthYear) {
            if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $monthYear)) {
                $validMonths[] = $monthYear;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid month format: $monthYear. Valid format is YYYY-MM (e.g. 2025-05)",
                ]);
            }
        }
        /*When Credit Recharge Will BE Paid*/
        if($request->transaction_type === 'due_paid'){
            if (empty($validMonths)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select at least one month for due payment.',
                ]);
            }

            /* Check if already paid for current month Year*/
            foreach ($request->recharge_month as $monthYear) {
                $existingRecharge = Customer_recharge::where('customer_id', $request->customer_id)
                    ->where('pop_id', $request->pop_id)
                    ->where('area_id', $request->area_id)
                    ->where('transaction_type', 'due_paid')
                     ->where('recharge_month', $monthYear)
                    ->first();

                if ($existingRecharge) {
                    $formattedMonth = Carbon::parse($monthYear)->translatedFormat('F Y');
                    return response()->json([
                        'success' => false,
                        'message' => "Due already paid for $formattedMonth .",
                    ]);
                }
            }

            /* Get total due (credit)*/
            $totalDue = Customer_recharge::where('customer_id', $request->customer_id)
                ->where('pop_id', $request->pop_id)
                ->where('area_id', $request->area_id)
                ->where('transaction_type', 'credit')
                ->sum('amount');

            if ($totalDue <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No due found for this customer.',
                ]);
            }

            if ($request->payable_amount <= 0 || $request->payable_amount > $totalDue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid due paid amount.',
                ]);
            }

            /*Store due paid data*/
            $due_paid                     = new Customer_recharge();
            $due_paid->user_id            = auth()->guard('sanctum')->user()->id;
            $due_paid->customer_id        = $request->customer_id;
            $due_paid->pop_id             = $request->pop_id;
            $due_paid->area_id            = $request->area_id;
            $due_paid->recharge_month     = implode(',', $validMonths);
            $due_paid->transaction_type   = 'due_paid';
            $due_paid->amount             = $request->payable_amount;
            $due_paid->paid_until         = null;
            $due_paid->note               = $request->note ?? 'Due Paid';
            $due_paid->save();


            customer_log($request->customer_id, 'recharge', auth()->guard('sanctum')->user()->id, 'Due Paid Successfully');

            return response()->json([
                'success' => true,
                'message' => 'Due paid successfully.',
            ]);
            exit;
        }

        if ($request->transaction_type !== 'due_paid'){
            DB::beginTransaction();
            /*Check POP/Branch Balance*/
            $pop_balance = check_pop_balance($request->pop_id);

            if ($pop_balance < $request->payable_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pop balance is not enough',
                ]);
                exit();
            }
            $object                     = new Customer_recharge();
            $object->user_id            = auth()->guard('sanctum')->user()->id;
            $object->customer_id        = $request->customer_id;
            $object->pop_id             = $request->pop_id;
            $object->area_id            = $request->area_id;
            $object->recharge_month     = implode(',', $request->recharge_month);
            $object->transaction_type   = $request->transaction_type;
            $object->amount             = $request->payable_amount;
            $object->note               = $request->note;
            $object->voucher_no         = $request->voucher_no;

            $customer = Customer::find($request->customer_id);

            foreach ($request->recharge_month as $monthYear) {
                $existingRecharge = Customer_recharge::where('customer_id', $request->customer_id)
                    ->where('pop_id', $request->pop_id)
                    ->where('area_id', $request->area_id)
                    ->where('recharge_month', $monthYear)
                    ->exists();

                if ($existingRecharge) {
                     $formattedMonth = Carbon::parse($monthYear)->translatedFormat('F Y');
                    return response()->json([
                        'success' => false,
                        'message' => "Recharge for month $formattedMonth already exists.",
                    ]);
                }
            }

            $months_count           = count($request->recharge_month);
            $base_date              = strtotime($customer->expire_date) > time() ? $customer->expire_date : date('Y-m-d');
            $new_expire_date        = date('Y-m-d', strtotime("+$months_count months", strtotime($base_date)));
            $customer->expire_date  = $new_expire_date;
            $object->paid_until     = $new_expire_date;

            $customer->update();
            /*-----------Customer Grace Recharge Start----------------*/
            $get_grace_recharge = Grace_recharge::where('customer_id', $customer->id)->first();
            if ($get_grace_recharge) {
                $customer_data = Customer::find($customer->id);
                /*Remove Grace Recharge Days*/
                if ($customer_data->expire_date) {
                    $customer_data->expire_date = \Carbon\Carbon::parse($customer_data->expire_date)->subDays($get_grace_recharge->days);
                    $object->paid_until     =  $customer_data->expire_date;
                    $customer_data->save();
                }
                /*Delete Grace Rechage**/
                $get_grace_recharge->delete();
                customer_log($object->customer_id, 'recharge', auth()->guard('sanctum')->user()->id, 'Customer Grace Recharge Remove!');
            }
            /*--------Send Message For Customer --------------*/
            if($request->send_message=='1'){
                $package = \App\Models\Branch_package::find($customer->package_id);
                $packageName = $package ? $package->name : '';

                $user = auth()->guard('sanctum')->user();
                $data = \App\Models\Website_information::where('pop_id', $user->pop_id)->latest()->first();
                if ($user->pop_id === null) {
                    $data = \App\Models\Website_information::whereNull('pop_id')->latest()->first();
                }
                $message = "($data->name)\n\n"
                        . "USER: {$customer->username}\n"
                        . "ID: {$customer->id}\n"
                        . "NAME: {$customer->fullname}\n"
                        . "BILL: Tk {$request->payable_amount}\n\n"
                        . "Thanks for your payment";


                /* Create a new Instance*/
                $send_message =new Send_message();
                $send_message->pop_id = $customer->pop_id;
                $send_message->area_id = $customer->area_id;
                $send_message->customer_id = $customer->id;
                $send_message->message =$message;
                $send_message->sent_at = Carbon::now();
                /*Call Send Message Function */
                send_message($customer->phone, $message);
                /* Save to the database table*/
                $send_message->save();
            }
            if ($object->save()) {
                customer_log($object->customer_id, 'recharge', auth()->guard('sanctum')->user()->id, 'Customer Recharge Completed!');

                /*Call Router activation Function*/
                router_activation($object->customer_id);


                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Recharge successfully.',
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Recharge failed. Please try again.',
                ]);
            }
        }
    }
    /**
 * @OA\Get(
 *     path="http://isperp.xyz/api/v1/admin/customer/recharge-undo/{id}",
 *     operationId="customerRechargeUndo",
 *     tags={"Customer"},
 *     summary="Undo a customer recharge",
 *     description="Deletes a specific recharge record. If the record is not a 'due_paid' transaction, the customer's expire_date is rolled back by the number of months contained in that recharge record. Also writes a customer log entry.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the recharge record to undo",
 *         @OA\Schema(type="integer", example=1234)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Undo completed (or not found/error states returned as success=false)",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=true),
 *                     @OA\Property(property="message", type="string", example="Successfully!")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="Not found.")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="message", type="string", example="Something went wrong.")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Something went wrong."),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     ),
 *     security={{"sanctum":{}}}
 * )
 */


    public function customer_recharge_undo($id)
    {
        $object = Customer_recharge::find($id);

        if (empty($object)) {
            return response()->json(['success' => false, 'message' => 'Not found.']);
            exit();
        }

        if ($object->transaction_type !== 'due_paid') {
            /*Update Customer Table Expire date*/
            $recharge_months = explode(',', $object->recharge_month);
            $months_count = count($recharge_months);
            //$new_paid_until = date('Y-m-d', strtotime("-$months_count months", strtotime($object->paid_until)));
            $new_paid_until = date('Y-m-d', strtotime("-$months_count months", strtotime(Customer::find($object->customer_id)->expire_date)));
            $customer = Customer::find($object->customer_id);
            $customer->expire_date = $new_paid_until;
            $customer->update();
        }
        if ($object) {
            $object->delete();
            customer_log($object->customer_id, 'recharge', auth()->guard('admin')->user()->id, 'Customer Recharge Undo!');
            return response()->json(['success' => true, 'message' => 'Successfully!']);
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }
    private function validateForm($request)
    {
        /*Validate the form data*/
        $rules = [
            'fullname' => 'required|string|max:100',
            'phone' => 'required|string|max:15|unique:customers,phone',
            'nid' => 'nullable|string|max:20|unique:customers,nid',
            'address' => 'nullable|string',
            'username' => 'required|string|max:100|unique:customers,username',
            'password' => 'required|string|min:6',
            'package_id' => 'required|exists:branch_packages,id',
            'pop_id' => 'required|exists:pop_branches,id',
            'area_id' => 'required|exists:pop_areas,id',
            'router_id' => 'required|exists:routers,id',
            'status' => 'required|in:active,online,offline,blocked,expired,disabled',
            'liabilities' => 'required|in:YES,NO',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }
    }
}
