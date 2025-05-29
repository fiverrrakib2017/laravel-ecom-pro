<?php
namespace App\Http\Controllers\Backend\Olt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Olt_controller extends Controller
{
    public function index(){
        $oltDevices = DB::table('olt_devices')->get();
        return view('Backend.Pages.Olt.index', compact('oltDevices'));
    }
    public function create(){
        return view('Backend.Pages.Olt.create');
    }
}
