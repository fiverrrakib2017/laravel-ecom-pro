<?php

namespace App\Http\Controllers\Backend\Hotspot;

use App\Http\Controllers\Controller;
use App\Models\Hotspot_profile;
use App\Models\Hotspot_user;
use Illuminate\Http\Request;
use App\Models\Router as Mikrotik_router;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class HotspotUserController extends Controller
{
    public function hotspot_user_create(){
        $routers=Mikrotik_router::where('status', 'active')->get();
        return view('Backend.Pages.Hotspot.User.Create', compact('routers'));
    }
    public function hotspot_user_index(Request $request)
    {
        $query = Hotspot_user::query()
            ->with([
                'router:id,name',
                'profile:id,name,mikrotik_profile',
            ])
            ->orderByDesc('id');

        // Filter: router
        if ($request->filled('router_id')) {
            $query->where('router_id', $request->integer('router_id'));
        }

        // Filter: profile
        if ($request->filled('hotspot_profile_id')) {
            $query->where('hotspot_profile_id', $request->integer('hotspot_profile_id'));
        }

        // Filter: status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search: username, mac_lock, comment
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('username', 'like', "%{$term}%")
                  ->orWhere('mac_lock', 'like', "%{$term}%")
                  ->orWhere('comment', 'like', "%{$term}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        // Dropdown data
        $routers  = Mikrotik_router::orderBy('name')->get(['id','name']);
        $profiles = collect();
        if ($request->filled('router_id')) {
            $profiles = Hotspot_profile::where('router_id', $request->integer('router_id'))
                        ->orderBy('name')
                        ->get(['id','name','mikrotik_profile']);
        }

        return view('Backend.Pages.Hotspot.User.index', compact('users','routers','profiles'));
    }

    /**
     * Store a newly created hotspot user.
     */
    public function hotspot_user_store(Request $request)
    {
        /* Validate the form data*/
        $rules = [
            'router_id'          => 'required|integer|exists:routers,id',
            'hotspot_profile_id' => [
                'required','integer',
                Rule::exists('hotspot_profiles','id')
                    ->where(fn($q) => $q->where('router_id', $request->router_id)),
            ],
            'username'           => [
                'required','string','max:190',
                Rule::unique('hotspot_users','username')
                    ->where(fn($q) => $q->where('router_id', $request->router_id)),
            ],
            'password'  => 'required|string|min:4|max:190',
            'mac_lock'  => 'nullable|string|max:190',
            // If you want MAC format validation, uncomment next line:
            // 'mac_lock'  => ['nullable','string','max:190','regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
            'status'    => ['required', Rule::in(['active','disabled','expired','blocked'])],
            'expires_at'=> 'nullable|date',
            'comment'   => 'nullable|string|max:500',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        /* Create a new Instance*/
        $object = new Hotspot_user();
        $object->router_id          = (int)$request->router_id;
        $object->hotspot_profile_id = (int)$request->hotspot_profile_id;
        $object->username           = $request->username;
        $object->password_encrypted = Crypt::encryptString($request->password);
        $object->mac_lock           = $request->filled('mac_lock') ? Str::lower($request->mac_lock) : null;
        $object->status             = $request->status;
        $object->expires_at         = $request->filled('expires_at') ? $request->expires_at : null;


        $object->created_by         = optional(Auth::guard('admin')->user())->id;
        $object->comment            = $request->filled('comment') ? $request->comment : null;

        /*Save to the database table*/
        $object->save();

        return response()->json([
            'success' => true,
            'message' => 'Added Successfully'
        ]);
    }
    public function hotspot_user_edit($id){
        $user           = Hotspot_user::with(['router:id,name', 'profile:id,name,mikrotik_profile'])->findOrFail($id);
        $profiles       = Hotspot_profile::where('router_id', $user->router_id)
                            ->orderBy('name')
                            ->get(['id','name','mikrotik_profile']);
        $routers        = Mikrotik_router::orderBy('name')->get(['id','name']);

    return view('Backend.Pages.Hotspot.User.edit', compact('user','routers','profiles'));
    }
    public function hotspot_user_update(Request $request, $id)
    {
        $user = Hotspot_user::findOrFail($id);

        /* Validate the form data*/
        $rules = [
            'router_id'          => 'required|integer|exists:routers,id',
            'hotspot_profile_id' => [
                'required','integer',
                Rule::exists('hotspot_profiles','id')
                    ->where(fn($q) => $q->where('router_id', $request->router_id)),
            ],
            'username'           => [
                'required','string','max:190',
                Rule::unique('hotspot_users','username')
                    ->where(fn($q) => $q->where('router_id', $request->router_id))
                    ->ignore($user->id),
            ],
            'password'  => 'nullable|string|min:4|max:190',
            'mac_lock'  => 'nullable|string|max:190',
            // 'mac_lock' => ['nullable','string','max:190','regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
            'status'    => ['required', Rule::in(['active','disabled','expired','blocked'])],
            'expires_at'=> 'nullable|date',
            'comment'   => 'nullable|string|max:500',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        /*-------------- Update the Instance-------------- */
        $user->router_id          = (int)$request->router_id;
        $user->hotspot_profile_id = (int)$request->hotspot_profile_id;
        $user->username           = $request->username;

        if ($request->filled('password')) {
            $user->password_encrypted = Crypt::encryptString($request->password);
        }

        $user->mac_lock   = $request->filled('mac_lock') ? Str::lower($request->mac_lock) : null;
        $user->status     = $request->status;
        $user->expires_at = $request->filled('expires_at') ? $request->expires_at : null;
        $user->comment    = $request->filled('comment') ? $request->comment : null;

        $user->created_by = $user->created_by ?? optional(Auth::guard('admin')->user())->id;

        /*--------------Save to the database table--------------*/
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Updated Successfully'
        ]);
    }
    /**-------------- Delete Hotspot profile-------------- */
    public function hotspot_user_destroy($id)
    {
        $profile = Hotspot_profile::findOrFail($id);
        $profile->delete();

        return response()->json(['success'=>true,'message'=>'Deleted Successfully']);
    }

    private function _validateForm($request){
        /*--------------Validate the form data--------------*/
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
