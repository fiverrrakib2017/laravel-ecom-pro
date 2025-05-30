<?php
namespace App\Http\Controllers\Backend\Olt;
use App\Http\Controllers\Controller;
use App\Models\Olt_device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Validator;

class Olt_controller extends Controller
{
    public function index(){
        $device = DB::table('olt_devices')->where('status','active')->first();

        $ip = $device->ip_address;
        $port = $device->port ?? 22;
        $username = $device->username;
        $password = $device->password;



        $ssh = new SSH2($ip, $port);

        if (!$ssh->login($username, $password)) {
            return response()->json(['error' => 'SSH Login failed'], 500);
        }
        $command = 'display ont info 0 0';
        $output = $ssh->exec($command);

        return response()->json([
            'output' => nl2br($output),
        ]);


        return view('Backend.Pages.Olt.index');
    }
    public function create(){
        return view('Backend.Pages.Olt.create');
    }
    public function store(Request $request){
       $rules = [
            'name'          => 'required|unique:olt_devices',
            'ip_address'    => 'required|ip|unique:olt_devices',
            'username'      => 'required',
            'password'      => 'required',
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
        /*Store  data*/
        $object                     = new Olt_device();
        $object->name               = $request->name;
        $object->brand              = $request->brand;
        $object->mode               = $request->mode;
        $object->ip_address         = $request->ip_address;
        $object->port               = $request->port;
        $object->protocol           = $request->protocol;
        $object->snmp_community     = $request->snmp_community;
        $object->snmp_version       = $request->snmp_version;
        $object->username           = $request->username;
        $object->password           = $request->password;
        $object->vendor             = $request->vendor;
        $object->model              = $request->model;
        $object->serial_number      = $request->serial_number;
        $object->firmware_version   = $request->firmware_version;
        $object->location           = $request->location;
        $object->status             = $request->status;
        $object->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully.',
        ]);
        exit;
    }
}
