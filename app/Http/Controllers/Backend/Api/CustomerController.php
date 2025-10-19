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

use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;
class CustomerController extends Controller
{
    /**
     * @OA\Get(
     *     path="https://isperp.xyz/admin/customer/all-data",
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
 *     path="https://isperp.xyz/v1/admin/customer/search",
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
 *     path="http://isperp.xyz/v1/admin/customer/store",
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
                $object->user_id = auth()->guard('admin')->user()->id;
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
                $user = auth()->guard('admin')->user();
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
                    $customer_device->user_id = auth()->guard('admin')->user()->id;
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
            customer_log($customer->id, 'add', auth()->guard('admin')->user()->id, 'Customer Created Successfully!');
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
