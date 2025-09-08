<?php

namespace App\Http\Controllers\Backend\Settings\Others;

use App\Http\Controllers\Controller;
use App\Models\Website_information;
use App\Models\Payment_method;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Pop_branch;

class SettingsController extends Controller
{
    public function index()
    {
        if (auth()->guard('admin')->user()->pop_id == null) {
            $data = Website_information::latest()->where('pop_id',null)->first();
            return view('Backend.Pages.Settings.information', compact('data'));
        }else{
            $data = Website_information::where('pop_id',auth()->guard('admin')->user()->pop_id)->latest()->first();
            return view('Backend.Pages.Settings.information', compact('data'));
        }
        abort(403);
    }
    public function password_change_index()
    {
        return view('Backend.Pages.Settings.password_change');
    }
    public function password_change_store(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string|max:255',
            'new_password'          => 'required|string|max:255|min:6',
            'confirm_new_password'  => 'required|string|same:new_password',
        ]);

        $admin = auth()->guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return response([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ]);
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();
        if($admin->pop_id !=null){
            $pop_branch=Pop_branch::where('status','1')->where('id',$admin->pop_id)->first();
            $pop_branch->password=$request->new_password;
            $pop_branch->save();
        }

        return response([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function store(Request $request)
    {
        //return $request->all();
        $request->validate([
            'name'          => 'required|string|max:255',
            'address'       => 'nullable|string|max:500',
            'phone_number'  => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'logo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $object = Website_information::find($request->id) ?? new Website_information();

        if (auth()->guard('admin')->user()->pop_id !== null) {
            $object->pop_id = auth()->guard('admin')->user()->pop_id;
        }
        $object->name           = $request->name;
        $object->address        = $request->address;
        $object->phone_number   = $request->phone_number;
        $object->email          = $request->email;
        if ($request->hasFile('logo')) {
            $image              = $request->file('logo');
            $imageName          = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Backend/uploads/photos/'), $imageName);
            $object->logo       = $imageName;
        }
        $object->save();

        return response(['success'=>true,'message'=>'Settings updated successfully']);
    }
    public function payment_method_create(){
        if (auth()->guard('admin')->user()->pop_id == null) {
            $data = Payment_method::latest()->where('pop_id',null)->first();
            return view('Backend.Pages.Settings.Payment_method.create', compact('data'));
        }else{
            $data = Payment_method::where('pop_id',auth()->guard('admin')->user()->pop_id)->latest()->first();
            return view('Backend.Pages.Settings.Payment_method.create', compact('data'));
        }
        abort(403);
    }
    public function payment_method_store(Request $request)
    {
        $object = Payment_method::find($request->id) ?? new Payment_method();
        if (auth()->guard('admin')->user()->pop_id !== null) {
            $object->pop_id = auth()->guard('admin')->user()->pop_id;
        }
        $object->url            = $request->bkash_url;
        $object->name           = $request->name;
        $object->account_number = $request->bkash_number;
        $object->api_key        = $request->bkash_api_key;
        $object->api_secret     = $request->bkash_api_secret;
        $object->username       = $request->bkash_username;
        $object->password       = $request->bkash_password;
        $object->callback_url   = $request->bkash_callback_url;
        $object->status         = $request->bkash_status;
        $object->save();

        return response(['success'=>true,'message'=>'Payment Method Add Successfully']);
    }

    private function validateForm($request)
    {
        /*Validate the form data*/
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
