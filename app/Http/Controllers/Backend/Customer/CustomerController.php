<?php
namespace App\Http\Controllers\Backend\Customer;

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
use function App\Helpers\check_pop_balance;
use function App\Helpers\customer_log;
use function App\Helpers\formate_uptime;
use function App\Helpers\get_mikrotik_user_info;
use function App\Helpers\send_message;

use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Facades\Cache;

class CustomerController extends Controller
{
    public function index()
    {
        return view('Backend.Pages.Customer.index');
    }
    public function create()
    {
        return view('Backend.Pages.Customer.create');
    }
    public function customer_restore()
    {
        return view('Backend.Pages.Customer.Restore.index');
    }

    public function customer_comming_expire()
    {
        return view('Backend.Pages.Customer.Expire.comming_expire');
    }
    public function customer_import_from_mikrotik(){
        return view('Backend.Pages.Customer.Import.import_mikrotik');

    }
    function customer_import_from_mikrotik_store(Request $request){
        DB::beginTransaction();
        try {
            /* Create a new Customer */
            $customer = new Customer();
            $customer->fullname = $request->data['username'];
            $customer->phone = $request->data['phone_number']?? '';
            $customer->con_charge = $request->con_charge ?? 0;
            $customer->amount = $request->amount ?? 0;
            $customer->username = $request->data['username'];
            $customer->password =$request->data['password'];
            $customer->package_id = $request->data['package_id'];
            $customer->pop_id =  $request->data['pop_id'];
            $customer->area_id = $request->data['area_id'];
            $customer->router_id = $request->data['router_id'];
            $customer->status = 'online';
            $customer->created_at = $request->data['create_date'];
            $customer->expire_date = $request->data['expire_date'];
            $customer->save();
            /* Create Customer Log */
            customer_log($customer->id, 'add', auth()->guard('admin')->user()->id, 'Customer Created Successfully!');
            /*Check Customer Connection Type*/
            if(!empty($customer->connection_type) && isset($customer->connection_type)){

                /*********** Radius Customer Store ****************/
                if($customer->connection_type=='radius'){
                    /* Create Customer Radius Server */
                        $existing_racheck= \App\Models\Radius\Radcheck::where('username', $request->data['username'])->first();
                        if(!$existing_racheck){
                            $radius = new \App\Models\Radius\Radcheck();
                            $radius->username = $request->data['username'];
                            $radius->attribute = 'Cleartext-Password';
                            $radius->op = ':=';
                            $radius->value = $request->password;
                            $radius->save();
                        }
                        $existing_radreply= \App\Models\Radius\Radreply::where('username', $request->data['username'])->first();
                        if(!$existing_radreply){
                            $radreply = new \App\Models\Radius\Radreply();
                            $radreply->username = $request->data['username'];
                            $radreply->attribute = 'MikroTik-Group';
                            $radreply->op = ':=';
                            $radreply->value = Branch_package::find($request->data['package_id'])->name;
                            $radreply->save();
                        }
                }

                /*********** PPPOE Customer Store ****************/
                if($customer->connection_type=='pppoe'){
                    $router = Mikrotik_router::where('status', 'active')->where('id', $customer->router_id)->first();
                    $client = new Client([
                        'host' => $router->ip_address,
                        'user' => $router->username,
                        'pass' => $router->password,
                        'port' => (int) $router->port ?? 8728,
                    ]);
                    /*Load MikroTik Profile list*/
                    $mikrotik_profile_list = new Query('/ppp/profile/print');
                    $profiles = $client->query($mikrotik_profile_list)->read();
                    /*Find profile name from Branch Package*/
                    $profileName = Branch_package::find($request->data['package_id'])->name;
                    /* Check if the profile name exists in MikroTik*/
                    $profileExists = collect($profiles)->pluck('name')->contains(trim($profileName));

                    if (!$profileExists) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "MikroTik profile '{$profileName}' does not exist. Please check your package configuration.",
                        ]);
                        exit;
                    }
                    /*Check if alreay exist*/
                    $check_Query = new Query('/ppp/secret/print');
                    $check_Query->where('name', $request->data['username']);
                    $check_customer = $client->query($check_Query)->read();

                    if (empty($check_customer)) {
                        $query = new Query('/ppp/secret/add');
                        $query->equal('name', $request->data['username']);
                        $query->equal('password', $request->data['password']);
                        $query->equal('service', 'pppoe');
                        $query->equal('profile', Branch_package::find($request->data['package_id'])->name);
                        $client->query($query)->read();
                    }
                }
                /*********** Hotspot Customer Store ****************/
                if($request->connection_type=='hotspot'){

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
    public function get_all_data(Request $request)
    {
        $pop_id = $request->pop_id;
        $area_id = $request->area_id;
        $status = $request->status;
        $connection_type = $request->connection_type;
        $search = $request->search['value'];
        $columnsForOrderBy = [ 'id', 'fullname', 'package_id', 'amount', 'expire_date', 'username', 'phone', 'pop_id', 'area_id', 'created_at', 'id'];

        $orderByColumn = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';
        /*Check if search value is empty*/
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        /*Check if branch user  value is empty*/
        $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;

        $baseQuery = Customer::with(['pop', 'area', 'package'])
            ->where('is_delete', '!=', 1)
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
            })
            ->when($pop_id, function ($query) use ($pop_id) {
                $query->where('pop_id', $pop_id);
            })
            /*POP/BRANCH Filter*/
            ->when($branch_user_id, function ($query) use ($branch_user_id) {
                $query->where('pop_id', $branch_user_id);
            })
            ->when($area_id, function ($query) use ($area_id) {
                $query->where('area_id', $area_id);
            })
            ->when($connection_type, function ($query) use($connection_type) {
                $query->where('connection_type', $connection_type);
            })
            /*year month type|| new|| expire*/
            // ->when($request->year, function ($query) use ($request) {
            //     $query->whereYear('created_at', $request->year);
            // })
            // ->when($request->month, function ($query) use ($request) {
            //     $query->whereMonth('created_at', date('m', strtotime($request->month)));
            // })
            ->when($request->type === 'expired', function ($query) use ($request) {
                $monthNum = date('m', strtotime($request->month)); // "June" → "06"
                $query->whereMonth('expire_date', $monthNum)
                    ->whereYear('expire_date', $request->year);
            })
            ->when($request->type === 'new', function ($query) use ($request) {
               $query->whereMonth('created_at', date('m', strtotime($request->month)))
                    ->whereYear('created_at', $request->year);
            })
            /*Filter Grace Recharge Data From Customer*/
            ->when($status, function ($query) use ($status) {
                if ($status == 'grace') {
                    $grace_ids = Grace_recharge::pluck('customer_id')->toArray();
                    $query->whereIn('id', $grace_ids);
                } else {
                    $query->where('status', $status);
                }
            });
        $filteredQuery = clone $baseQuery;
        /*Pagination*/
        $paginatedData = $baseQuery
            ->orderBy($columnsForOrderBy[$orderByColumn] ?? 'id', $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => Customer::where('is_delete', '!=', 1)->count(),
            'recordsFiltered' => $filteredQuery->count(),
            'data' => $paginatedData,
        ]);
    }

    public function customer_restore_get_all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'id', 'fullname', 'package', 'amount', 'created_at', 'expire_date', 'username', 'phone', 'pop_id', 'area_id', 'created_at', 'created_at'];

        $orderByColumn = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';

        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        /*Check if branch user  value is empty*/
        $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;

        $query = Customer::with(['pop', 'area', 'package'])
            ->where('is_delete', '!=', 0)
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
            })
            ->when($branch_user_id, function ($query) use ($branch_user_id) {
                $query->where('pop_id', 'like', "%$branch_user_id%");
            });

        /*Pagination*/
        $paginatedData = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirection)->paginate($length, ['*'], 'page', $start / $length + 1);

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => Customer::where('is_delete', '!=', 1)->count(),
            'recordsFiltered' => $paginatedData->total(),
            'data' => $paginatedData->items(),
        ]);
    }
    public function get_customer_info(Request $request)
    {
        /* Define A Default Query */
        $query = Customer::with(['pop', 'area', 'package']);

        if (!empty($request->pop_id)) {
            $query->where('pop_id', $request->pop_id);
        }
        if (!empty($request->area_id)) {
            $query->where('area_id', $request->area_id);
        }
        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        if (!empty($request->expire_days)) {
            $today = now();
            $endDate = now()->addDays($request->expire_days);

            $query->whereBetween('expire_date', [$today, $endDate]);
        }

        $customers = $query->where('is_delete', 0)->get();

        if ($customers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data found',
            ]);
        }

        $html = '';
        foreach ($customers as $row) {
            $package_name = $row->package ? $row->package->name : 'N/A';
            $get_pop_name = $row->pop ? $row->pop->name : 'N/A';
            $get_area_name = $row->area ? $row->area->name : 'N/A';
            //$status_icon = $row->status == 'online'? '<span style="color:green; font-size:20px; margin-right:5px;">&#9679;</span>': '<span style="color:red; font-size:20px; margin-right:5px;">&#9679;</span>';
            $url = route('admin.customer.view', $row->id);
            $status_icon = '';
            switch ($row->status) {
                case 'online':
                    $status_icon = '<i class="fas fa-unlock" style="font-size: 15px; color: green; margin-right: 8px;" title="Online"></i>';
                    break;
                case 'offline':
                    $status_icon = '<i class="fas fa-lock" style="font-size: 15px; color: red; margin-right: 8px;" title="Offline"></i>';
                    break;
                case 'expired':
                    $status_icon = '<i class="fas fa-clock" style="font-size: 15px; color: orange; margin-right: 8px;" title="Expired"></i>';
                    break;
                case 'blocked':
                    $status_icon = '<i class="fas fa-ban" style="font-size: 15px; color: darkred; margin-right: 8px;" title="Blocked"></i>';
                    break;
                case 'disabled':
                    $status_icon = '<i class="fas fa-user-slash" style="font-size: 15px; color: gray; margin-right: 8px;" title="Disabled"></i>';
                    break;
                case 'discontinue':
                    $status_icon = '<i class="fas fa-times-circle" style="font-size: 15px; color: #ff6600; margin-right: 8px;" title="Discontinue"></i>';
                    break;
                default:
                    $status_icon = '<i class="fa fa-question-circle" style="font-size: 18px; color: gray; margin-right: 8px;" title="Unknown"></i>';
                    break;
            }
            $html .= '<tr>';
            $html .= '<td><input type="checkbox" class="customer-checkbox checkSingle" value="' . $row->id . '"></td>';
            $html .= '<td>' . $row->id . '</td>';
            $html .= '<td>' . $status_icon . '<a href="' . $url . '" style="text-decoration:none; color:#007bff;">' . $row->username . '</a></td>';
            $html .= '<td>' . $package_name . '</td>';
            $html .= '<td>' . $row->amount . '</td>';
            $html .= '<td>' . $row->expire_date . '</td>';
            $html .= '<td>' . $get_pop_name . '</td>';
            $html .= '<td>' . $get_area_name . '</td>';
            $html .= '<td>' . $row->phone . '</td>';
            $html .= '<td>' . $row->address . '</td>';
            $html .= '</tr>';
        }

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    public function check_customer_user(Request $request){
        $exists = DB::table('customers')->where('username', $request->username)->exists();

        return response()->json([
            'available' => !$exists
        ]);
    }
    public function store(Request $request)
    {
        /* Validate the form data */
        $this->validateForm($request);
        /* Check Pop Balance */
        $pop_balance = check_pop_balance($request->pop_id);
        if ($pop_balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Pop balance is not enough',
            ]);
        }

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
            $customer->expire_date = date('Y-m-d', strtotime('+1 month'));
            $customer->remarks = $request->remarks;
            $customer->connection_type = $request->connection_type;
            $customer->liabilities = $request->liabilities;
            $customer->save();

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

            /* Send Message to the Customer*/
            if($request->send_message=='1'){
                //$bill_payment_link = "https://sr-wifi.net?clid={$custID}";

                // $message = 'Thank you for joining SR Wi-Fi.
                //             Your Customer ID : {customer_id}
                //             username : {username}
                //             password : {password}
                //             HelpLine : 01821600600
                //             Bill payment link: {bill_payment_link}';
                $message = 'Thank you for joining Wi-Fi.Your Customer ID : {customer_id} username : {username} password : {password} HelpLine : 01971768290';

                $message = str_replace('{customer_id}', $customer->id, $message);
                $message = str_replace('{username}', $customer->username, $message);
                $message = str_replace('{password}', $customer->password, $message);
                //$message = str_replace('{bill_payment_link}', $bill_payment_link, $message);
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
            /* Customer Device Liabilities store database table*/
            if (!empty($request->device_type) && count($request->device_type) > 0) {
                foreach ($request->device_type as $index => $type) {
                    /*check customer device validation ---select--- null data*/
                    if ($type === '---Select---' || empty($type)) {
                        continue;
                    }
                    $customer_device = new Customer_device();
                    $customer_device->customer_id=$customer->id;
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
            if(!empty($request->connection_type) && isset($request->connection_type)){

                /*********** Radius Customer Store ****************/
                if($request->connection_type=='radius'){
                    /* Create Customer Radius Server */
                        $existing_racheck= \App\Models\Radius\Radcheck::where('username', $request->username)->first();
                        if(!$existing_racheck){
                            $radius = new \App\Models\Radius\Radcheck();
                            $radius->username = $request->username;
                            $radius->attribute = 'Cleartext-Password';
                            $radius->op = ':=';
                            $radius->value = $request->password;
                            $radius->save();
                        }
                        $existing_radreply= \App\Models\Radius\Radreply::where('username', $request->username)->first();
                        if(!$existing_radreply){
                            $radreply = new \App\Models\Radius\Radreply();
                            $radreply->username = $request->username;
                            $radreply->attribute = 'MikroTik-Group';
                            $radreply->op = ':=';
                            $radreply->value = Branch_package::find($request->package_id)->name;
                            $radreply->save();
                        }
                }

                /*********** PPPOE Customer Store ****************/
                if($request->connection_type=='pppoe'){
                    $router = Mikrotik_router::where('status', 'active')->where('id', $request->router_id)->first();
                    $client = new Client([
                        'host' => $router->ip_address,
                        'user' => $router->username,
                        'pass' => $router->password,
                        'port' => (int) $router->port ?? 8728,
                    ]);
                    /*Load MikroTik Profile list*/
                    $mikrotik_profile_list = new Query('/ppp/profile/print');
                    $profiles = $client->query($mikrotik_profile_list)->read();
                    /*Find profile name from Branch Package*/
                    $profileName = Branch_package::find($request->package_id)->name;
                    /* Check if the profile name exists in MikroTik*/
                    $profileExists = collect($profiles)->pluck('name')->contains(trim($profileName));

                    if (!$profileExists) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "MikroTik profile '{$profileName}' does not exist. Please check your package configuration.",
                        ]);
                        exit;
                    }
                    /*Check if alreay exist*/
                    $check_Query = new Query('/ppp/secret/print');
                    $check_Query->where('name', $request->username);
                    $check_customer = $client->query($check_Query)->read();

                    if (empty($check_customer)) {
                        $query = new Query('/ppp/secret/add');
                        $query->equal('name', $request->username);
                        $query->equal('password', $request->password);
                        $query->equal('service', 'pppoe');
                        $query->equal('profile', Branch_package::find($request->package_id)->name);
                        $client->query($query)->read();
                    }
                }
                /*********** Hotspot Customer Store ****************/
                if($request->connection_type=='hotspot'){

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
            customer_log($customer->id, 'edit', auth()->guard('admin')->user()->id, 'Customer updated successfully!');

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

    public function customer_credit_recharge_list()
    {
        return view('Backend.Pages.Customer.Credit.recharge_list');
    }
    public function onu_list(){
        return view('Backend.Pages.Customer.Onu.onu_list');
    }

    public function get_customer_onu_list_data(Request $request){
        $pop_id = $request->pop_id;
        $area_id = $request->area_id;
        $onu_type = $request->onu_type;
        $search = $request->search['value'];
        $columnsForOrderBy = ['id'];

        $orderByColumn = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';
        /*Check if search value is empty*/
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        /*Check if branch user  value is empty*/
        $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;

        $baseQuery = Customer::with(['pop', 'area', 'package'])
            ->where('is_delete', '!=', 1)
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
            })
            ->when($pop_id, function ($query) use ($pop_id) {
                $query->where('pop_id', $pop_id);
            })
            /*POP/BRANCH Filter*/
            ->when($branch_user_id, function ($query) use ($branch_user_id) {
                $query->where('pop_id', $branch_user_id);
            })
            ->when($area_id, function ($query) use ($area_id) {
                $query->where('area_id', $area_id);
            })
            ->when($onu_type, function ($query) use ($onu_type) {
                $query->where('onu_type', $onu_type);
            });
        $filteredQuery = clone $baseQuery;
        /*Pagination*/
        $paginatedData = $baseQuery
            ->orderBy($columnsForOrderBy[$orderByColumn] ?? 'id', $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => Customer::where('is_delete', '!=', 1)->count(),
            'recordsFiltered' => $filteredQuery->count(),
            'data' => $paginatedData,
        ]);
    }
    public function delete(Request $request)
    {
        $object = Customer::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->is_delete = 1;
        $object->save();
        customer_log($object->id, 'edit', auth()->guard('admin')->user()->id, 'Customer Update Successfully!');

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
    public function customer_restore_back(Request $request)
    {
        $object = Customer::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->is_delete = 0;
        $object->save();
        /* Create Customer Log */
        customer_log($object->id, 'delete', auth()->guard('admin')->user()->id, 'Customer Restored  Successfully!');
        return response()->json(['success' => true, 'message' => 'Restored successfully.']);
    }
    public function edit($id)
    {
        $data = Customer::find($id);
        if ($data) {
            customer_log($data->id, 'edit', auth()->guard('admin')->user()->id, 'Customer Edit Modal Open!');
            return response()->json(['success' => true, 'data' => $data]);
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }
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

    public function view($id)
    {
        $data = Customer::with(['pop', 'area', 'package', 'router'])->find($id);

        $total_recharged = Customer_recharge::where('customer_id', $id)->where('transaction_type', '!=', 'due_paid')->sum('amount') ?? 0;

        $totalPaid = Customer_recharge::where('customer_id', $id)->where('transaction_type', '!=', 'credit')->sum('amount') ?? 0;

        $get_total_due = Customer_recharge::where('customer_id', $id)->where('transaction_type', 'credit')->sum('amount') ?? 0;
        $duePaid = Customer_recharge::where('customer_id', $id)->where('transaction_type', 'due_paid')->sum('amount') ?? 0;

        $totalDue = $get_total_due - $duePaid;
        /*Include Mikrotik Data Customer Profile*/
        //$router = Mikrotik_router::where('status', 'active')->where('id', $data->router_id)->first();
        /*Get Mikrotik Data via reusable function */
        //$mikrotik_data = $router ? get_mikrotik_user_info($router, $data->username) : null;
        /*Get Onu Information */
        //  $ssh = new SSH2('OLT_IP_ADDRESS');
        //  if (!$ssh->login('username', 'password')) {
        //     return response()->json(['error' => 'Login Failed']);
        //  }
        // /*Send MAC search command*/
        // $response = $ssh->exec("show mac-address-table | include $mikrotik_data['mac']");
        return view('Backend.Pages.Customer.Profile', compact('data', 'totalDue', 'totalPaid', 'duePaid', 'total_recharged', ));
    }
    public function customer_mikrotik_reconnect($id)
    {
        $customer = Customer::find($id);
        $router = Mikrotik_router::where('status', 'active')->where('id', $customer->router_id)->first();
        if (!$router || !$customer) {
            return response()->json(['success' => false, 'message' => 'Router or User not found']);
        }

        try {
            $API = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port ?? 8728,
            ]);

            /*Disconnect user*/
            $disconnectQuery = new Query('/ppp/active/print');
            $disconnectQuery->where('name', $customer->username);
            $activeUser = $API->query($disconnectQuery)->read();

            if (count($activeUser)) {
                $removeId = $activeUser[0]['.id'];
                $removeQuery = new Query('/ppp/active/remove');
                $removeQuery->equal('.id', $removeId);
                $API->query($removeQuery)->read();
            }
            sleep(2);
            $enableQuery = new Query('/ppp/secret/set');
            $enableQuery->equal('.id', $customer->username)->equal('disabled', 'no');
            $API->query($enableQuery)->read();

            return response()->json(['success' => true, 'message' => 'Reconnected!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function customer_change_status(Request $request)
    {
        $object = Customer::find($request->id);

        $router = Mikrotik_router::where('status', 'active')->where('id', $object->router_id)->first();
        if (!$router || !$object) {
            return response()->json(['success' => false, 'message' => 'Router or Customer not found']);
        }

        try {
            $API = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port ?? 8728,
            ]);

            $API->connect();

            /* Find secret ID by username*/
            $secretQuery = new Query('/ppp/secret/print');
            $secretQuery->where('name', $object->username);
            $secrets = $API->query($secretQuery)->read();

            if (empty($secrets)) {
                return response()->json(['success' => false, 'message' => 'PPP Secret not found']);
            }

            $secretId = $secrets[0]['.id'];

            /*Find active session*/
            $activeQuery = new Query('/ppp/active/print');
            $activeQuery->where('name', $object->username);
            $activeUser = $API->query($activeQuery)->read();

            /*Determine action*/
            if ($object->status === 'disabled') {
                /*Enable user*/
                $enableQuery = new Query('/ppp/secret/set');
                $enableQuery->equal('.id', $secretId)->equal('disabled', 'no');
                $API->query($enableQuery)->read();

                $object->status = 'online';
            } else {
                /* Disable user*/
                $disableQuery = new Query('/ppp/secret/set');
                $disableQuery->equal('.id', $secretId)->equal('disabled', 'yes');
                $API->query($disableQuery)->read();

                /*Disconnect if active*/
                if (!empty($activeUser)) {
                    $activeId = $activeUser[0]['.id'];
                    $removeQuery = new Query('/ppp/active/remove');
                    $removeQuery->equal('.id', $activeId);
                    $API->query($removeQuery)->read();
                }

                $object->status = 'disabled';
            }

            $object->save();

            return response()->json([
                'success' => true,
                'message' => 'Successfully Changed',
                'new_status' => $object->status,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function customer_device_return($id){
        try {

            $customer_device = Customer_device::where('id', $id)->first();
            if (!$customer_device) {
                return response()->json(['success' => false, 'message' => 'Customer Device not found']);
            }
            $customer_device->returned_date=date('Y-m-d');
            $customer_device->status='returned';

            $customer_device->update();

            return response()->json([
                'success' => true,
                'message' => 'Successfully Changed',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function customer_live_bandwith_update($id)
    {
        $object = Customer::find($id);
        if (!$object) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }

        $router = Mikrotik_router::where('status', 'active')->where('id', $object->router_id)->first();
        if (!$router) {
            return response()->json(['success' => false, 'message' => 'Router not found']);
        }

        try {
            $client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port ?? 8728,
            ]);

            $interfaces = $client->query(new Query('/interface/print'))->read();
            $sessions = $client->query(new Query('/ppp/active/print'))->read();

            $uptime = 'N/A';
            $ip_address = 'N/A';
            $mac_address = 'N/A';
            foreach ($sessions as $session) {
                if ($session['name'] == $object->username) {
                    $uptime = $session['uptime'] ?? 'N/A';
                    $ip_address = $session['address'] ?? 'N/A';
                    $mac_address = $session['caller-id'] ?? 'N/A';
                    break;
                }
            }
            /* Get MAC from ARP table using IP*/
            if ($ip_address && $ip_address != 'N/A') {
                $arp_entries = $client->query(new Query('/ip/arp/print'))->read();
                foreach ($arp_entries as $entry) {
                    if (isset($entry['address']) && $entry['address'] === $ip_address) {
                        $mac_address = $entry['mac-address'] ?? 'N/A';
                        break;
                    }
                }
            }

            foreach ($interfaces as $intf) {
                if (strpos($intf['name'], $object->username) !== false) {
                    $interface_name = $intf['name'];
                    /* Live bandwidth*/
                    $monitor = $client->query(
                        (new Query('/interface/monitor-traffic'))
                            ->equal('interface', $interface_name)
                            ->equal('once', '')
                    )->read();


                    $rx_speed = isset($monitor[0]['rx-bits-per-second']) ? round($monitor[0]['rx-bits-per-second'] / 1024, 2) : 0; // in Kbps
                    $tx_speed = isset($monitor[0]['tx-bits-per-second']) ? round($monitor[0]['tx-bits-per-second'] / 1024, 2) : 0; // in Kbps

                    return response()->json([
                        'success' => true,
                        'interface_name' => $intf['name'],
                        'type' => $intf['type'],
                        'mac_address' => $mac_address,

                        'rx_mb' => round($intf['rx-byte'] / 1024 / 1024, 2), // total downloaded
                        'tx_mb' => round($intf['tx-byte'] / 1024 / 1024, 2), // total uploaded

                        'rx_speed_kbps' => $rx_speed,
                        'tx_speed_kbps' => $tx_speed,

                        'rx_packet' => $intf['rx-packet'],
                        'tx_packet' => $intf['tx-packet'],
                        'ip_address' => $ip_address,
                        'uptime' => formate_uptime($uptime),
                    ]);
                    // event(new customer_bandwith_update($id, [
                    //     'success' => true,
                    //     'interface_name' => $intf['name'],
                    //     'type' => $intf['type'],
                    //     'mac_address' => $mac_address,
                    //     'rx_mb' => round($intf['rx-byte'] / 1024 / 1024, 2),
                    //     'tx_mb' => round($intf['tx-byte'] / 1024 / 1024, 2),
                    //     'rx_speed_kbps' => $rx_speed,
                    //     'tx_speed_kbps' => $tx_speed,
                    //     'rx_packet' => $intf['rx-packet'],
                    //     'tx_packet' => $intf['tx-packet'],
                    //     'ip_address' => $ip_address,
                    //     'uptime' => formate_uptime($uptime),
                    // ]));

                }
            }

            return response()->json(['success' => false, 'message' => 'Interface not found for this customer']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // public function getMonthlyUsage($username)
    // {
    //     $month = now()->month;
    //     $year = now()->year;

    //     $totalDownload = UserUsage::where('username', $username)->whereMonth('date', $month)->whereYear('date', $year)->sum('download_gb');

    //     $totalUpload = UserUsage::where('username', $username)->whereMonth('date', $month)->whereYear('date', $year)->sum('upload_gb');

    //     return response()->json([
    //         'username' => $username,
    //         'download_gb' => round($totalDownload, 2),
    //         'upload_gb' => round($totalUpload, 2),
    //     ]);
    // }

    public function get_onu_info(Request $request)
    {
        $ip = '160.250.8.8';
        $username = 'admin';
        $password = 'admin';
        //$mac = strtolower(str_replace('-', ':', $request->mac_address));

        $ssh = new SSH2($ip);

        if (!$ssh->login($username, $password)) {
            return response()->json(['error' => 'Login Failed']);
        }

        // MAC search command, depending on your OLT brand
        $command = "show mac-address-table | include $request->mac_address";

        $output = $ssh->exec($command);

        return response()->json([
            'raw_output' => $output,
            'message' => 'Success',
        ]);
    }

    public function customer_recharge_old_method(Request $request)
    {
        /*Validate the form data*/
        $rules = [
            'customer_id' => 'required',
            'pop_id' => 'required',
            'area_id' => 'required',
            'payable_amount' => 'required|numeric',
            'recharge_month' => 'required|array',
            'transaction_type' => 'required',
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

        /*Check Pop Balance*/
        $pop_balance = check_pop_balance($request->pop_id);

        if ($pop_balance < $request->payable_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Pop balance is not enough',
            ]);
            exit();
        }

        try {
            /*Store recharge data*/
            $object = new Customer_recharge();
            $object->user_id = auth()->guard('admin')->user()->id;
            $object->customer_id = $request->customer_id;
            $object->pop_id = $request->pop_id;
            $object->area_id = $request->area_id;
            $object->recharge_month = implode(',', $request->recharge_month);

            if ($request->transaction_type !== 'due_paid') {
                /*Check Recharge is Exist*/
                $existingRecharge = Customer_recharge::where('customer_id', $request->customer_id)->where('pop_id', $request->pop_id)->where('area_id', $request->area_id)->whereIn('recharge_month', $request->recharge_month)->exists();
                if ($existingRecharge) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Recharge for this month already exists.',
                    ]);
                    exit();
                }
                /*Update Customer Table Expire date*/
                $customer = Customer::find($request->customer_id);
                $months_count = count($request->recharge_month);
                $base_date = strtotime($customer->expire_date) > time() ? $customer->expire_date : date('Y-m-d');
                $new_expire_date = date('Y-m-d', strtotime("+$months_count months", strtotime($base_date)));
                $customer->expire_date = $new_expire_date;
                $customer->update();
                $object->paid_until = $new_expire_date;
            }

            $object->transaction_type = $request->transaction_type;
            $object->amount = $request->payable_amount;
            $object->note = $request->note;

            if ($object->save()) {
                customer_log($object->customer_id, 'recharge', auth()->guard('admin')->user()->id, 'Customer Recharge Completed!');
                /*Check This user Mikrotik Router Connection*/
                $router = Mikrotik_router::where('status', 'active')->where('id', $customer->router_id)->first();
                $client = new Client([
                    'host' => $router->ip_address,
                    'user' => $router->username,
                    'pass' => $router->password,
                    'port' => (int) $router->port ?? 8728,
                ]);
                /*Check if username already exists in Mikrotik ppp active list*/
                $checkQuery = (new Query('/ppp/active/print'))->where('name', $customer->username);
                $existingUsers = $client->query($checkQuery)->read();

                if (empty($existingUsers)) {
                    /* User is offline, so enable the secret*/
                    $enableQuery = new Query('/ppp/secret/enable');
                    $enableQuery->equal('numbers', $customer->username);
                    $client->query($enableQuery)->read();
                    /*Update Customer Status After Completed Recharge*/
                    $customer = Customer::find($request->customer_id);
                    $customer->status = 'online';
                    $customer->update();
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Recharge successfully.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Recharge failed. Please try again.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Recharge failed! Error: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
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
            $due_paid->user_id            = auth()->guard('admin')->user()->id;
            $due_paid->customer_id        = $request->customer_id;
            $due_paid->pop_id             = $request->pop_id;
            $due_paid->area_id            = $request->area_id;
            $due_paid->recharge_month     = implode(',', $validMonths);
            $due_paid->transaction_type   = 'due_paid';
            $due_paid->amount             = $request->payable_amount;
            $due_paid->paid_until         = null;
            $due_paid->note               = $request->note ?? 'Due Paid';
            $due_paid->save();


            customer_log($request->customer_id, 'recharge', auth()->guard('admin')->user()->id, 'Due Paid Successfully');

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
            $object->user_id            = auth()->guard('admin')->user()->id;
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

            $get_grace_recharge = Grace_recharge::where('customer_id', $customer->id)->first();
            if ($get_grace_recharge) {
                $customer_data = Customer::find($customer->id);
                /*Remove Grace Recharge Days*/
                if ($customer_data->expire_date) {
                    $customer_data->expire_date = \Carbon\Carbon::parse($customer_data->expire_date)->subDays($get_grace_recharge->days);
                    $customer_data->save();
                }
                /*Delete Grace Rechage**/
                $get_grace_recharge->delete();
                customer_log($object->customer_id, 'recharge', auth()->guard('admin')->user()->id, 'Customer Grace Recharge Remove!');
            }
            if ($object->save()) {
                customer_log($object->customer_id, 'recharge', auth()->guard('admin')->user()->id, 'Customer Recharge Completed!');

                /*Call Router activation Function*/
                $this->router_activation($object->customer_id);


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

    /*Customer Bulk Recharge*/
    public function customer_bulk_recharge(){
        return view('Backend.Pages.Customer.bulk_recharge');
    }
    public function customer_bulk_recharge_store(Request $request)
    {
        $rules = [
            'customer_ids'      => 'required|array|min:1',
            'recharge_month'    => 'required|array|min:1',
            'transaction_type'  => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Validate months
        $validMonths = [];
        foreach ($request->recharge_month as $monthYear) {
            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $monthYear)) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid month format: $monthYear. Use YYYY-MM.",
                ]);
            }
            $validMonths[] = $monthYear;
        }

        if ($request->transaction_type !== 'due_paid') {
            DB::beginTransaction();

            foreach ($request->customer_ids as $customer_id) {
                $customer = Customer::find($customer_id);
                if (!$customer) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Customer with ID $customer_id not found.",
                    ]);
                }

                $totalAmount = $customer->amount * count($validMonths);
                $pop_balance = check_pop_balance($customer->pop_id);

                if ($pop_balance < $totalAmount) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Pop balance is not enough',
                    ]);
                }

                foreach ($validMonths as $monthYear) {
                    $exists = Customer_recharge::where('customer_id', $customer_id)
                        ->where('pop_id', $customer->pop_id)
                        ->where('area_id', $customer->area_id)
                        ->where('recharge_month', $monthYear)
                        ->exists();

                    if ($exists) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Recharge for $monthYear already exists.",
                        ]);
                    }
                    /***** Check Customer Grace Recharge Start******/
                    $get_grace_recharge = Grace_recharge::where('customer_id', $customer->id)->first();

                    if ($get_grace_recharge) {
                        $customer_data = Customer::find($customer->id);

                        /*Remove Grace Recharge Days*/
                        if ($customer_data->expire_date) {
                            $customer_data->expire_date = \Carbon\Carbon::parse($customer_data->expire_date)->subDays($get_grace_recharge->days);
                            $customer_data->save();
                        }

                        /*Delete Grace Rechage**/
                        $get_grace_recharge->delete();
                    }
                    /***** Check Customer Grace Recharge End ******/
                    $object = new Customer_recharge();
                    $object->user_id          = auth()->guard('admin')->id();
                    $object->customer_id      = $customer_id;
                    $object->pop_id           = $customer->pop_id;
                    $object->area_id          = $customer->area_id;
                    $object->recharge_month   = $monthYear;
                    $object->transaction_type = $request->transaction_type;
                    $object->amount           = $customer->amount;
                    $object->note             = $request->note ?? 'Bulk Recharge';

                    $base_date = strtotime($customer->expire_date) > time()
                        ? $customer->expire_date
                        : now()->toDateString();

                    $new_expire_date = date('Y-m-d', strtotime("+".count($validMonths)." months", strtotime($base_date)));
                    $object->paid_until = $new_expire_date;
                    $customer->expire_date = $new_expire_date;

                    $customer->save();
                    $object->save();

                    customer_log($customer_id, 'recharge', auth()->guard('admin')->id(), 'Customer Recharge Completed!');
                    /*Call Router activation Function*/
                    $this->router_activation($customer_id);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Recharge successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid transaction type.']);
    }



    public function customer_grace_recharge_store(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->customer_ids as $customer_id) {
                $existing = Grace_recharge::where('customer_id', $customer_id)->first();

                if (!$existing) {
                    $existing = new Grace_recharge();
                    $existing->customer_id = $customer_id;
                    $existing->days = $request->days;
                    $existing->save();
                } else {
                    $existing->days = $request->days;
                    $existing->updated_at = now();
                    $existing->save();
                }
                /*Increase Customer Expire Date*/
                $customer = Customer::find($customer_id);
                if ($customer && $customer->expire_date) {
                    $customer->expire_date = Carbon::parse($customer->expire_date)->addDays($existing->days);
                    $customer->save();
                }
                /*Activate rouater customer*/
                $this->router_activation($customer_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Recharge Successfully completed.'
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

    public function customer_grace_recharge_remove($customer_id)
    {
        if(empty($customer_id)){
            return response()->json([
                'success' => false,
                'message' => 'Customer id Require.'
            ]);
        }
        try {
            DB::beginTransaction();
            $data = Grace_recharge::where('customer_id', $customer_id)->first();
            /*Decrease Customer Expire Date*/
            $customer = Customer::find($customer_id);
            if ($customer && $customer->expire_date) {
                $customer->expire_date = Carbon::parse($customer->expire_date)->subDays($data->days);
                $customer->save();
            }
            $data->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Delete successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


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
    public function customer_recharge_print($recharge_id)
    {
        $data = Customer_recharge::find($recharge_id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Not found.']);
            exit();
        }
        $html =
            '
        <div style="font-family: monospace; font-size: 12px; text-align: center;">
            <strong>ISP Billing System</strong><br>
            -----------------------------<br>
            Customer Name: ' .
            $data->customer->fullname .
            '<br>
            Customer ID: ' .
            $data->customer->id .
            '<br>
            Date: ' .
            \Carbon\Carbon::parse($data->created_at)->format('d M Y') .
            '<br>
            Months: ' .
            $data->recharge_month .
            '<br>
            Type: ' .
            ucfirst($data->transaction_type) .
            '<br>
            Amount: ' .
            number_format($data->amount, 2) .
            ' BDT<br>
            Paid Until: ' .
            \Carbon\Carbon::parse($data->paid_until)->format('d M Y') .
            '<br>
            Remarks: ' .
            ucfirst($data->note ?? '-') .
            '<br>
            -----------------------------<br>
            Thank You!
        </div>';

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function customer_payment_history()
    {
        return view('Backend.Pages.Customer.Payment.payment_history');
    }
    public function customer_payment_history_get_all_data(Request $request)
    {
        $search = $request->search['value'] ?? null;
        $columnsForOrderBy = ['id', 'created_at', 'id', 'recharge_month', 'transaction_type', 'paid_until', 'amount'];
        $orderByColumn = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';

        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        // Branch User ID
        $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
        $query = Customer_recharge::with(['customer', 'customer.pop', 'customer.area', 'customer.package'])
            ->when($branch_user_id, function ($query) use ($branch_user_id) {
                $query->where('pop_id', $branch_user_id);
            })
            ->when($search, function ($query) use ($search) {
                $query
                    ->where('created_at', 'like', "%$search%")
                    ->orWhere('recharge_month', 'like', "%$search%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('fullname', 'like', "%$search%");
                    })
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('username', 'like', "%$search%");
                    });
            });
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->status_filter) {
            $query->where('transaction_type', $request->status_filter);
        }

        if ($request->bill_collect) {
            $query->where('user_id', $request->bill_collect);
        }
        $totalRecords = $query->count();
        $totalAmount = $query->sum('amount');
        $data = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirection)->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
            'totalAmount' => $totalAmount,
        ]);
    }
    public function customer_log()
    {
        return view('Backend.Pages.Customer.Log.index');
    }
    public function customer_log_get_all_data(Request $request)
    {
        $search = $request->search['value'] ?? null;
        $columnsForOrderBy = ['id', 'created_at', 'id', 'recharge_month', 'transaction_type', 'paid_until', 'amount'];
        $orderByColumn = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';

        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        $query = Customer_log::with(['customer', 'user'])->when($search, function ($query) use ($search) {
            $query
                ->where('created_at', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%")
                ->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('fullname', 'like', "%$search%");
                })
                ->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('username', 'like', "%$search%");
                })
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
        });
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->pop_id) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('pop_id', $request->pop_id);
            });
        }

        $totalRecords = $query->count();

        $data = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirection)->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }
    public function customer_import()
    {
        return view('Backend.Pages.Customer.Import.customer_import');
    }
    public function customer_csv_file_import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        /*Upload CSV File*/
        $file = $request->file('csv_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/csv'), $filename);

        return response()->json([
            'success' => true,
            'message' => 'CSV file uploaded successfully.',
        ]);
        exit();
    }
    public function delete_csv_file($file)
    {
        $file_path = public_path('uploads/csv/' . $file);
        if (file_exists($file_path)) {
            unlink($file_path);
            return back()->with('success', 'File deleted successfully.');
        } else {
            return back()->with('error', 'File not found.');
        }
    }
    public function upload_csv_file()
    {
        $files = glob(public_path('uploads/csv/*'));

        /*Store The Database  table*/
        // Loop through each file
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                // Open the CSV file
                $csvFile = fopen($file, 'r');
                if ($csvFile !== false) {
                    // Skip the header row if necessary
                    $header = fgetcsv($csvFile);

                    // Loop through the rows and insert them into the database
                    while (($row = fgetcsv($csvFile)) !== false) {
                        // Validate and insert the data
                        if (1 == 1) {
                            $data = array_combine($header, $row);
                            DB::beginTransaction();
                            $customer = new Customer();
                            $customer->fullname = $data['fullname'];
                            $customer->phone = $data['phone'];
                            $customer->nid = $data['nid'] ?? null;
                            $customer->address = $data['address'] ?? null;
                            $customer->con_charge = $data['con_charge'] ?? 0;
                            $customer->amount = $data['amount'] ?? 0;
                            $customer->username = $data['username'];
                            $customer->password = $data['password'];
                            $customer->package_id = $data['package_id'];
                            $customer->pop_id = $data['pop_id'];
                            $customer->area_id = $data['area_id'];
                            $customer->router_id = $data['router_id'];
                            if (isset($data['status']) && $data['status'] !== 'active') {
                                $customer->status = 'active';
                            } else {
                                $customer->status = $data['status'] ?? 'active';
                            }
                            $customer->expire_date = $data['expire_date'] ?? date('Y-m-d', strtotime('+1 month'));
                            $customer->created_at = $data['create_date'] ?? date('Y-m-d');
                            $customer->remarks = $data['remarks'] ?? null;
                            $customer->liabilities = $data['liabilities'] ?? 'NO';
                            $customer->is_delete = $data['is_delete'] ?? '0';
                            $customer->save();
                            /* Store Customer Log  */
                            customer_log($customer->id, 'add', auth()->guard('admin')->user()->id, 'Customer Created Successfully!');
                            DB::commit();
                        }
                    }
                    /*Close the CSV file*/
                    fclose($csvFile);
                    /*Delete the CSV file*/
                    unlink($file);
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Server Uploaded CSV file successfully.',
        ]);
        exit();
    }
    private function router_activation($customer_id){
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
