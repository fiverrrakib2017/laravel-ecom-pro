<?php

namespace App\Http\Controllers\Backend\Hotspot;

use App\Http\Controllers\Controller;
use App\Models\Hotspot_profile;
use Illuminate\Http\Request;
use App\Models\Router as Mikrotik_router;
use Illuminate\Support\Facades\Validator;
class HotspotController extends Controller
{
    public function hotspot_dashbaord(){
        return view('Backend.Pages.Hotspot.Dashboard');
    }
    public function hotspot_profile_index(){

        return view('Backend.Pages.Hotspot.Profile.index');
    }
    public function hotspot_profile_create(){
        $routers=Mikrotik_router::where('status', 'active')->get();
        return view('Backend.Pages.Hotspot.Profile.Create',compact('routers'));
    }
    public function hotspot_profile_store(Request $request){
        /****Form Validation*****/
        $this->_validateForm($request);

        /*-----Create a new Instance------*/
        $profile = new Hotspot_profile();
        $profile->router_id         = (int) $request->router_id;
        $profile->name              = $request->name;
        $profile->mikrotik_profile  = $request->mikrotik_profile;
        $profile->rate_limit        = $request->filled('rate_limit') ? $request->rate_limit : null;
        $profile->shared_users      = $request->input('shared_users', 1);
        $profile->idle_timeout      = $request->filled('idle_timeout') ? $request->idle_timeout : null;
        $profile->keepalive_timeout = $request->filled('keepalive_timeout') ? $request->keepalive_timeout : null;
        $profile->session_timeout   = $request->filled('session_timeout') ? $request->session_timeout : null;
        $profile->validity_days     = $request->input('validity_days', 1);
        $profile->price_minor       = $request->input('price_minor', 0);
        $profile->is_active         = $request->boolean('is_active');
        $profile->notes             = $request->filled('notes') ? $request->notes : null;

        // Save to the database table
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Added Successfully',
        ]);


    }
    private function _validateForm($request){
        /*Validate the form data*/
        $rules = [
            'router_id'         => 'required|integer|exists:routers,id',
            'name'              => 'required|string|max:255',
            'mikrotik_profile'  => 'required|string|max:255',
            'rate_limit'        => 'nullable|string|max:255',
            'shared_users'      => 'nullable|integer|min:1',
            'idle_timeout'      => 'nullable|string|max:255',
            'keepalive_timeout' => 'nullable|string|max:255',
            'session_timeout'   => 'nullable|string|max:255',
            'validity_days'     => 'nullable|integer|min:1',
            'price_minor'       => 'nullable|integer|min:0',
            'is_active'         => 'boolean',
            'notes'             => 'nullable|string',
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
