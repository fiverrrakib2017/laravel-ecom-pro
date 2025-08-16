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
use Illuminate\Validation\Rules\Password;
class UserController extends Controller
{
    public function index()
    {
        $data = Admin::latest()->get();
        $roles = Role::all();
        return view('Backend.Pages.User.index', compact('data', 'roles'));
    }
    public function store(Request $request)
    {
        /* Validate the form data */
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username',
            'phone' => 'required|string|max:255|unique:admins,phone',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);
        // Create admin
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->phone = $request->phone;
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);
        $admin->user_type = $request->user_type ?? 1;
        $admin->save();
        $admin->syncRoles([$request->role]);
        return response()->json([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
    }

    public function get_user($id)
    {
        $admin = Admin::findOrFail($id);
        $roles = Role::all();

        return response()->json([
            'admin' => $admin,
            'roles' => $roles,
            'current_role' => $admin->roles?->pluck('name')->first() ?? '',
        ]);
    }
    public function update(Request $request)
    {
        /* Validation rules*/
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username,' . $request->id,
            'phone' => 'required|string|max:255|unique:admins,phone,'.$request->id,
            'email' => 'required|email|max:255|unique:admins,email,' . $request->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $admin = Admin::findOrFail($request->id);

        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;

        if (!empty($request->password)) {
            $admin->password = bcrypt($request->password);
        }

        $admin->save();

        $admin->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully!'
        ]);
    }

    public function delete(Request $request)
    {
        $admin = Admin::findOrFail($request->id);
        $admin->delete();
        return response()->json(['success' => true, 'message' => 'Delete Successfully']);
    }
    private function validateForm($request)
    {
        /*Validate the form data*/
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ];

        $messages = [
            'password.confirmed' => 'Password and confirmation do not match.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

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
