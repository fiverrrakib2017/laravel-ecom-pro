<?php
namespace App\Http\Controllers\Backend\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(){

    }
    public function permission(){
        return view('Backend.pages.Role.permission');
    }
}
