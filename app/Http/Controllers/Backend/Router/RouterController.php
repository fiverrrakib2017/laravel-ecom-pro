<?php
namespace App\Http\Controllers\Backend\Router;
use App\Http\Controllers\Controller;
use App\Models\Pop_branch;
use App\Models\Pop_area;
use App\Models\Router;
use App\Models\Radius\Nas as nas_server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use RouterOS\Client;
use RouterOS\Query;
use Carbon\Carbon;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::where('status', 'active')->get();

        $mikrotik_data = [];

        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host'     => $router->ip_address,
                    'user'     => $router->username,
                    'pass'     => $router->password,
                    'port'     => (int) $router->port,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);


                $query = new Query('/ppp/active/print');
                $activeUsers = $client->query($query)->read();


                $resourceQuery = new Query('/system/resource/print');
                $resourceDetails = $client->query($resourceQuery)->read();

                $mikrotik_data[] = [
                    'router_id' => $router->id,
                    'router_name' => $router->name,
                    'online_users' => count($activeUsers),
                    'uptime' => $resourceDetails[0]['uptime'] ?? 'N/A',
                    'version' => $resourceDetails[0]['version'] ?? 'N/A',
                    'hardware' => $resourceDetails[0]['hardware'] ?? 'N/A',
                    'cpu' => $resourceDetails[0]['cpu'] ?? 'N/A',
                    'offline_users' => 0,
                ];
            } catch (\Exception $e) {
                $mikrotik_data[] = [
                    'error' => $e->getMessage()
                ];
            }
        }
        $mikrotik_data = collect($mikrotik_data);
        // return $mikrotik_data;
        return view('Backend.Pages.Router.index', compact('routers', 'mikrotik_data'));
    }



    public function store(Request $request)
    {
        /* Validate the form data*/
        $this->validateForm($request);

        /* Create a new Supplier*/
        $object = new Router();
        $object->name = $request->name;
        $object->pop_id = $request->pop_id;
        $object->ip_address = $request->ip_address;
        $object->username = $request->username;
        $object->password = $request->password;
        $object->port = $request->port;
        $object->status = $request->status;
        $object->api_version = $request->api_version;
        $object->location = $request->location;
        $object->remarks = $request->remarks;

        if ($request->radius_server === '1') {
            $nas=new nas_server();
            $nas->nasname=$request->name;
            $nas->shortname=$request->ip_address;
            $nas->ports=$request->port;
            $nas->secret=$request->password;
            $nas->api_user=$request->username;
            $nas->api_password=$request->password;
            $nas->api_ip=$request->ip_address;
            $nas->server=$request->ip_address;
            $nas->save();
        }
        /* Save to the database table*/
        $object->save();

        return response()->json([
            'success' => true,
            'message' => 'Added successfully!'
        ]);
    }
    public function router_log(){
        $routers=Router::where('status', 'active')->get();
        $allLogs = [];
        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host'     => $router->ip_address,
                    'user'     => $router->username,
                    'pass'     => $router->password,
                    'port'     => (int)$router->port,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);

                $query = new Query('/log/print');
                $logs = $client->query($query)->read();

                foreach ($logs as $log) {
                    $allLogs[] = [
                        'router_name' => $router->name,
                        'message'     => $log['message'] ?? '',
                        'time'        =>  Carbon::parse($log['time'])->format('l, F j, Y g:i A') ?? '',
                        'topics'      => $log['topics'] ?? '',
                    ];
                }
            } catch (\Exception $e) {
                $allLogs[] = [
                    'router_name' => $router->name,
                    'message'     => 'Connection failed: ' . $e->getMessage(),
                    'time'        => now(),
                    'topics'      => 'error'
                ];
            }
        }
        return view('Backend.Pages.Mikrotik.log', compact('allLogs'));
    }

    public function router_user_list($router_id){
        $routers=Router::where('status', 'active')->where('id',$router_id)->get();
        $data = [];
        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host'     => $router->ip_address,
                    'user'     => $router->username,
                    'pass'     => $router->password,
                    'port'     => (int)$router->port,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);

                $query = new Query('/ppp/active/print');
                $data = $client->query($query)->read();
                // return $data;
                return view('Backend.Pages.Mikrotik.customers', compact('data'));
            } catch (\Exception $e) {
                $data[] = [
                    'router_name' => $router->name,
                    'message'     => 'Connection failed: ' . $e->getMessage(),
                    'time'        => now(),
                    'topics'      => 'error'
                ];
            }
        }
        // return $data;
        // return view('Backend.Pages.Mikrotik.customers',  ['data' => $data]);
    }


    public function delete(Request $request)
    {
        $object = Router::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' =>true, 'message'=> 'Deleted successfully.']);
    }
    public function edit($id)
    {
        $data = Router::find($id);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
            exit;
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }

    public function update(Request $request, $id){
        $this->validateForm($request);

        $object = Router::findOrFail($id);
        $object->name = $request->name;
        $object->pop_id = $request->pop_id;
        $object->ip_address = $request->ip_address;
        $object->username = $request->username;
        $object->password = $request->password;
        $object->port = $request->port;
        $object->status = $request->status;
        $object->api_version = $request->api_version;
        $object->location = $request->location;
        $object->remarks = $request->remarks;
        $object->update();

        return response()->json([
            'success' => true,
            'message' => 'Update successfully!',
        ]);
    }

    public function get_router_with_pop($pop_id){
        if(isset($pop_id) && !empty($pop_id)){
            $routers=Router::where('status', 'active')->where('pop_id', $pop_id)->get();
            if ($routers) {
                return response()->json(['success' => true, 'data' => $routers]);
                exit;
            }
        }
        if(!isset($pop_id) && empty($pop_id)){
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }
    public function show_nas_server(){
        $routers = nas_server::get();

        $mikrotik_data = [];

        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host'     => $router->api_ip,
                    'user'     => $router->api_user,
                    'pass'     => $router->api_password,
                    'port'     => (int) $router->ports,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);


                $query = new Query('/ppp/active/print');
                $activeUsers = $client->query($query)->read();


                $resourceQuery = new Query('/system/resource/print');
                $resourceDetails = $client->query($resourceQuery)->read();

                $mikrotik_data[] = [
                    'router_id' => $router->id,
                    'router_name' => $router->nasname,
                    'online_users' => count($activeUsers),
                    'uptime' => $resourceDetails[0]['uptime'] ?? 'N/A',
                    'version' => $resourceDetails[0]['version'] ?? 'N/A',
                    'hardware' => $resourceDetails[0]['hardware'] ?? 'N/A',
                    'cpu' => $resourceDetails[0]['cpu'] ?? 'N/A',
                    'offline_users' => 0,
                ];
            } catch (\Exception $e) {
                $mikrotik_data[] = [
                    'error' => $e->getMessage()
                ];
            }
        }
        $mikrotik_data = collect($mikrotik_data);
        // return $mikrotik_data;
        return view('Backend.Pages.Router.nas', compact('routers', 'mikrotik_data'));
    }
    public function router_sync(){
         $routers = Router::where('status', 'active')->get();

        $mikrotik_data = [];

        foreach ($routers as $router) {
            try {
                $client = new Client([
                    'host'     => $router->ip_address,
                    'user'     => $router->username,
                    'pass'     => $router->password,
                    'port'     => (int) $router->port,
                    'timeout'  => 3,
                    'attempts' => 1
                ]);


                $query = new Query('/ppp/active/print');
                $activeUsers = $client->query($query)->read();


                $resourceQuery = new Query('/system/resource/print');
                $resourceDetails = $client->query($resourceQuery)->read();

                $mikrotik_data[] = [
                    'router_id' => $router->id,
                    'router_name' => $router->name,
                    'online_users' => count($activeUsers),
                    'uptime' => $resourceDetails[0]['uptime'] ?? 'N/A',
                    'version' => $resourceDetails[0]['version'] ?? 'N/A',
                    'hardware' => $resourceDetails[0]['hardware'] ?? 'N/A',
                    'cpu' => $resourceDetails[0]['cpu'] ?? 'N/A',
                    'offline_users' => 0,
                ];
            } catch (\Exception $e) {
                $mikrotik_data[] = [
                    'error' => $e->getMessage()
                ];
            }
        }
        $mikrotik_data = collect($mikrotik_data);
        // return $mikrotik_data;
        return view('Backend.Pages.Router.sync', compact('routers', 'mikrotik_data'));
    }
    public function get_mikrotik_user($id){
        $router = Router::findOrFail($id);
         $client = new Client([
            'host'     => $router->ip_address,
            'user'     => $router->username,
            'pass'     => $router->password,
            'port'     => (int) $router->port,
            'timeout'  => 3,
            'attempts' => 1
        ]);

        $response = $client->query('/ppp/secret/print')->read();

        $users = [];
        $existing_usernames = \App\Models\Customer::pluck('username')->toArray();
        foreach ($response as $item) {
            $username = $item['name'] ?? '';
            /* Check if user exists**/
            $already_exists = in_array($username, $existing_usernames);

            /* GET POP/Branch Area **/
            $_get_all_pop_branch=Pop_branch::where('status', '1')->latest()->get();
            $_get_all_pop_area=Pop_area::latest()->get();
            $_pop_options = '';
            $_pop_options .= '<option>--Select---</option>';
            foreach ($_get_all_pop_branch as $pop) {
                $_pop_options .= '<option value="' . $pop->id . '">' . $pop->name . '</option>';
            }

            $_pop_areas_options = '';
             $_pop_areas_options .= '<option>--Select---</option>';
            foreach ($_get_all_pop_area as $area) {
                //$_pop_areas_options .= '<option value="' . $area->id . '">' . $area->name . '</option>';
            }
            $users[] = [
                'username' => $item['name'] ?? '',
                'password' => $item['password'] ?? '',
                'profile' => $item['profile'] ?? '',
                //'comment' => $item['comment'] ?? '',
                'pop' => '<select class="form-control pop-select" name="pop_id" style="style:width:100%;">' . $_pop_options . '</select>',
                'area' => '<select class="form-control area-select" name="area_id" style="style:width:100%;">' . $_pop_areas_options . '</select>',
                'package' =>'<select class="form-control package-select" name="package_id" style="style:width:100%;">---Select---</select>',
                'amount' => '<input type="text" name="amount" class="form-control amount-field" value="0"/>',
                'billing_cycle' => '<input type="text" name="billing_cycle" class="form-control" value="0"/>',
                'create_date' => '<input type="text" name="create_date" class="form-control" value="' . now()->format('Y-m-d') . '">',
                'expire_date' => '<input type="text" name="expire_date" class="form-control" value="' . now()->addMonth()->format('Y-m-d') . '">',
                'add_button' => $already_exists
                ? '<span class="badge bg-success">Exists</span>'
                : '<button class="btn btn-sm btn-primary add-user-btn" data-user=\'' . json_encode($item) . '\'>Add</button>',

            ];
        }

        return response()->json(['users' => $users]);
    }
    private function validateForm($request)
    {

        /*Validate the form data*/
        $rules=[
            'name' => 'required|string|max:100',
            'ip_address' => 'required|ip',
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:100',
            'port' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    }
}
