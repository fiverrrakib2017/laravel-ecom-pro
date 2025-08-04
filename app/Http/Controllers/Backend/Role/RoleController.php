<?php
namespace App\Http\Controllers\Backend\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(){
        return view('Backend.pages.Role.index');
    }
    public function role_rote(Request $request){
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = \Spatie\Permission\Models\Role::create([
            'name' => $request->name,
            'guard_name' => 'admin',
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        return response()->json(['success'=>true, 'message'=>'Role has been created']);
    }

    public function role_delete(Request $request){
        $role = \Spatie\Permission\Models\Role::findOrFail($request->id);

        if ($role->name === 'Admin') {
            return response()->json(['success'=>false, 'message'=>'Admin role cannot be deleted.']);
        }

        $role->delete();
        return response()->json(['success'=>true, 'message'=>'Role deleted successfully.']);
    }
    public function permission(){
        return view('Backend.pages.Role.permission');
    }
}
