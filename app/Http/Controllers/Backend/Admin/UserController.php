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

    public function get_user($id)
    {
        $admin = Admin::findOrFail($id);
        $roles = Role::all();

        return response()->json([
            'admin' => $admin,
            'roles' => $roles,
            'current_role' => $admin->roles?->pluck('name')->first() ?? ''
        ]);
    }
    public function update(Request $request)
    {
        $admin = Admin::findOrFail($request->id);

        $admin->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ]);

        if ($request->password) {
            $admin->update(['password' => bcrypt($request->password)]);
        }

        $admin->syncRoles([$request->role]);

        return response()->json(['success' => true]);
    }

}
