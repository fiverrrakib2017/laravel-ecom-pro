<?php

namespace App\Http\Controllers\Backend\Settings\Others;

use App\Http\Controllers\Controller;
use App\Models\Website_information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $data = Website_information::latest()->first();
        return view('Backend.Pages.Settings.information', compact('data'));
    }

    public function store(Request $request)
    {
        //return $request->all();
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $object = Website_information::firstOrNew([]);
        $object->name = $request->name;
        $object->address = $request->address;
        $object->phone_number = $request->phone_number;
        $object->email = $request->email;
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Backend/uploads/photos/'), $imageName);
            $object->logo = $imageName;
        }
        $object->save();

        return back()->with('success', 'Settings updated successfully');
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
