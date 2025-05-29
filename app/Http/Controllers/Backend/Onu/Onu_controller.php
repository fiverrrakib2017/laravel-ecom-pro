<?php
namespace App\Http\Controllers\Backend\Onu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Onu_controller extends Controller
{
    public function index(){
        $oltDevices = DB::table('olt_devices')->get();
        return view('Backend.Pages.Onu.index', compact('oltDevices'));
    }
   
}
