<?php

namespace App\Http\Controllers\Backend\Admin;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
class UserController extends Controller
{
    public function index(){
        $data=Admin::latest()->get();
        $roles = Role::all();
        return view('Backend.Pages.User.index',compact('data','roles'));
    }
}
