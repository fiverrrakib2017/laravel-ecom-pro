<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\CustomerProfit;
use App\Models\Customer;
use App\Models\IpBlock;
use Toastr;
use Image;
use File;
use Auth;
use Hash;
class CustomerManageController extends Controller
{
    public function index(Request $request){
        if($request->keyword){
            $show_data = Customer::orWhere('phone',$request->keyword)->orWhere('name',$request->keyword)->paginate(20);
        }else{
             $show_data = Customer::paginate(20);
        }

        return view('backEnd.customer.index',compact('show_data'));
    }

    public function edit($id){
        $edit_data = Customer::find($id);
        return view('backEnd.customer.edit',compact('edit_data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:customers,email,' . $request->hidden_id,
        ]);

        $customer = Customer::findOrFail($request->hidden_id);

        $input = $request->except('hidden_id');

        // Password
        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->password);
        } else {
            unset($input['password']);
        }

        // Image
        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $name = time() . '-' . strtolower(str_replace(' ', '-', pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))) . '.webp';

            $path = public_path('uploads/customer/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $img = Image::make($image->getRealPath());

            $img->encode('webp', 90);

            $width = 100;
            $height = 100;

            if ($img->height() > $img->width()) {
                $width = null;
            } else {
                $height = null;
            }

            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });

            $img->save($path . $name);

            if ($customer->image && File::exists(public_path($customer->image))) {
                File::delete(public_path($customer->image));
            }

            $input['image'] = 'uploads/customer/' . $name;
        }

        $input['status'] = $request->boolean('status');
        $input['address'] = $request->input('address');

        $customer->update($input);

        Toastr::success('Success', 'Data updated successfully');

        return redirect()->route('customers.index');
    }

    public function inactive(Request $request){
        $inactive = Customer::find($request->hidden_id);
        $inactive->status = 'inactive';
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request){
        $active = Customer::find($request->hidden_id);
        $active->status = 'active';
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function profile(Request $request){
        $profile = Customer::with('orders')->find($request->id);
        return view('backEnd.customer.profile',compact('profile'));
    }
    public function adminlog(Request $request){
        $customer = Customer::find($request->hidden_id);
        Auth::guard('customer')->loginUsingId($customer->id);
        return redirect()->route('customer.account');
    }
    public function ip_block(Request $request){
        $data = IpBlock::get();
        return view('backEnd.reports.ipblock',compact('data'));
    }
    public function ipblock_store(Request $request){

        $store_data = new IpBlock();
        $store_data->ip_no = $request->ip_no;
        $store_data->reason = $request->reason;
        $store_data->save();
        Toastr::success('Success','IP address add successfully');
        return redirect()->back();
    }
    public function ipblock_update(Request $request){
        $update_data = IpBlock::find($request->id);
        $update_data->ip_no = $request->ip_no;
        $update_data->reason = $request->reason;
        $update_data->save();
        Toastr::success('Success','IP address update successfully');
        return redirect()->back();
    }
    public function ipblock_destroy(Request $request){
        $delete_data = IpBlock::find($request->id)->delete();
        Toastr::success('Success','IP address delete successfully');
        return redirect()->back();
    }
}
