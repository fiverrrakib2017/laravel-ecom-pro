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
    public function hotspot_user_bulk_create(Request $request){
        $routers=Mikrotik_router::where('status', 'active')->get();
        return view('Backend.Pages.Hotspot.User.Bulk_create', compact('routers'));
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
    public function hotspot_user_bulk_import(Request $request){
        $routers = Mikrotik_router::orderBy('name')->get(['id','name']);
        return view('Backend.Pages.Hotspot.User.Bulk_import', compact('routers'));
    }
    public function hotspot_user_bulk_import_store(Request $request){
       /* Validate the form data */
        $rules = [
            'router_id'          => 'required|integer|exists:routers,id',
            'hotspot_profile_id' => [
                'required','integer',
                Rule::exists('hotspot_profiles','id')
                    ->where(fn($q) => $q->where('router_id', $request->router_id)),
            ],
            'csv_file'           => 'required|file|mimes:csv,txt|max:10240', // 10MB
            'has_header'         => 'nullable|boolean',
            'delimiter'          => ['nullable', Rule::in([',',';','\t','auto'])],
            'status_override'    => ['nullable', Rule::in(['active','disabled','expired','blocked'])],
            'expires_default'    => 'nullable|date',
            'comment_default'    => 'nullable|string|max:500',

            // password fallback when CSV password column is empty
            'password_fallback'  => ['required', Rule::in(['none','username','fixed'])],
            'fixed_password'     => 'required_if:password_fallback,fixed|nullable|string|min:4|max:190',

            'dry_run'            => ['required', Rule::in(['0','1'])],
        ];

        $validator = Validator::make($request->all(), $rules, [
            'csv_file.mimes' => 'Please upload a .csv (or .txt with CSV data).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $routerId   = (int) $request->router_id;
        $profileId  = (int) $request->hotspot_profile_id;
        $dryRun     = $request->dry_run === '1';
        $hasHeader  = (bool) $request->boolean('has_header');
        $delimiter  = $request->input('delimiter', 'auto');
        $statusOv   = $request->input('status_override');   // nullable
        $expiresDef = $request->input('expires_default');   // nullable
        $commentDef = $request->input('comment_default');   // nullable
        $pwFallback = $request->input('password_fallback'); // none|username|fixed
        $fixedPw    = $request->input('fixed_password');

        // --- Read file into rows ---
        $file = $request->file('csv_file');
        if (!$file->isValid()) {
            return response()->json(['success'=>false,'errors'=>['csv_file'=>['Invalid file upload.']]], 422);
        }

        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return response()->json(['success'=>false,'errors'=>['csv_file'=>['Unable to read file.']]], 422);
        }

        // Peek the first line to auto-detect delimiter if needed
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return response()->json(['success'=>false,'errors'=>['csv_file'=>['Empty file.']]], 422);
        }

        $dlm = $delimiter;
        if ($dlm === 'auto') {
            $candidates = ["," => substr_count($firstLine, ","), ";" => substr_count($firstLine, ";"), "\t" => substr_count($firstLine, "\t")];
            arsort($candidates);
            $dlm = key($candidates); // winner
        }
        // reset pointer and start reading again
        rewind($handle);

        // helper to get row array
        $getRow = function() use ($handle, $dlm) {
            return fgetcsv($handle, 0, $dlm);
        };

        // read header if present
        $header = null;
        if ($hasHeader) {
            $header = $getRow();
            if ($header) {
                $header = array_map(function($h){
                    $h = trim(mb_strtolower((string)$h));
                    // normalize some common names
                    return match ($h) {
                        'mac' => 'mac_lock',
                        default => $h,
                    };
                }, $header);
            }
        }

        // If no header, we will map by index: 0=username, 1=password, 2=mac_lock, 3=status, 4=expires_at, 5=comment
        $colIndex = [
            'username'   => null,
            'password'   => null,
            'mac_lock'   => null,
            'status'     => null,
            'expires_at' => null,
            'comment'    => null,
        ];

        if ($header) {
            foreach ($header as $i => $h) {
                if (array_key_exists($h, $colIndex)) {
                    $colIndex[$h] = $i;
                }
            }
            // username must exist in header
            if ($colIndex['username'] === null) {
                fclose($handle);
                return response()->json([
                    'success'=>false,
                    'errors'=>['csv_file'=>['Header must include a "username" column.']]
                ], 422);
            }
        } else {
            // default index mapping
            $colIndex = ['username'=>0,'password'=>1,'mac_lock'=>2,'status'=>3,'expires_at'=>4,'comment'=>5];
        }

        // preload existing usernames for duplicate check
        $allNewUsernames = [];
        $rowsRaw = [];
        $lineNo  = 0;

        // Read rows (cap to avoid memory abuse)
        $maxRows = 5000;
        while (($row = $getRow()) !== false) {
            $lineNo++;
            if ($hasHeader && $lineNo === 1) continue; // skip header line already read
            if (count($row) === 1 && trim((string)$row[0]) === '') continue; // skip blank lines

            $rowsRaw[] = $row;
            if (count($rowsRaw) >= $maxRows) break;
        }
        fclose($handle);

        // collect usernames for DB check
        foreach ($rowsRaw as $row) {
            $u = $colIndex['username'] !== null ? trim((string)($row[$colIndex['username']] ?? '')) : '';
            if ($u !== '') {
                $allNewUsernames[] = $u;
            }
        }
        $allNewUsernames = array_values(array_unique($allNewUsernames));

        $existing = Hotspot_user::where('router_id', $routerId)
                    ->whereIn('username', $allNewUsernames)
                    ->pluck('username')
                    ->all();
        $existing = array_flip($existing); // set for quick lookup

        // Process rows
        $createdRows  = []; // [{username,password}]
        $rowsToInsert = [];
        $skipped      = []; // [{username, reason}]
        $seen         = []; // in-file duplicates

        $adminId = optional(Auth::guard('admin')->user())->id;
        $allowedStatus = ['active','disabled','expired','blocked'];

        foreach ($rowsRaw as $idx => $row) {
            $username = $colIndex['username'] !== null ? trim((string)($row[$colIndex['username']] ?? '')) : '';
            if ($username === '') {
                $skipped[] = ['username'=>'(line '.($hasHeader?($idx+2):($idx+1)).')','reason'=>'Missing username'];
                continue;
            }

            if (isset($seen[$username])) {
                $skipped[] = ['username'=>$username,'reason'=>'Duplicate in file'];
                continue;
            }
            $seen[$username] = true;

            if (isset($existing[$username])) {
                $skipped[] = ['username'=>$username,'reason'=>'Already exists'];
                continue;
            }

            // password
            $password = $colIndex['password'] !== null ? trim((string)($row[$colIndex['password']] ?? '')) : '';
            if ($password === '') {
                if ($pwFallback === 'username') $password = $username;
                elseif ($pwFallback === 'fixed') $password = $fixedPw;
            }
            if ($password === '' || mb_strlen($password) < 4 || mb_strlen($password) > 190) {
                $skipped[] = ['username'=>$username,'reason'=>'Invalid password'];
                continue;
            }

            // status
            $status = $statusOv ?: ($colIndex['status'] !== null ? trim(mb_strtolower((string)($row[$colIndex['status']] ?? ''))) : 'active');
            if (!in_array($status, $allowedStatus, true)) $status = 'active';

            // expires_at
            $expiresAtStr = $expiresDef ?: ($colIndex['expires_at'] !== null ? trim((string)($row[$colIndex['expires_at']] ?? '')) : null);
            $expiresAt = null;
            if ($expiresAtStr !== null && $expiresAtStr !== '') {
                try { $expiresAt = Carbon::parse($expiresAtStr); } catch (\Throwable $e) { $expiresAt = null; }
            }

            $mac = $colIndex['mac_lock'] !== null ? trim((string)($row[$colIndex['mac_lock']] ?? '')) : null;
            $mac = $mac !== '' ? Str::lower($mac) : null;

            $comment = $commentDef ?? ($colIndex['comment'] !== null ? trim((string)($row[$colIndex['comment']] ?? '')) : null);
            $comment = $comment !== '' ? $comment : null;

            $rowsToInsert[] = [
                'router_id'          => $routerId,
                'hotspot_profile_id' => $profileId,
                'username'           => $username,
                'password_encrypted' => Crypt::encryptString($password),
                'mac_lock'           => $mac,
                'status'             => $status,
                'expires_at'         => $expiresAt,
                'last_seen_at'       => null,
                'upload_bytes'       => 0,
                'download_bytes'     => 0,
                'uptime_seconds'     => 0,
                'created_by'         => $adminId,
                'comment'            => $comment,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];

            $createdRows[] = ['username'=>$username, 'password'=>$password];
        }

        if ($dryRun) {
            return response()->json([
                'success'        => true,
                'message'        => 'Preview generated. Valid: '.count($rowsToInsert).', Skipped: '.count($skipped).'.',
                'valid_count'    => count($rowsToInsert),
                'skipped_count'  => count($skipped),
                'created'        => array_slice($createdRows, 0, 1000), // preview up to 1000 rows
                'skipped'        => array_slice($skipped, 0, 1000),
            ]);
        }

        if (empty($rowsToInsert)) {
            return response()->json([
                'success'=>false,
                'errors'=>['csv_file'=>['Nothing to import (all invalid or duplicates).']]
            ], 422);
        }

        // Insert in chunks
        foreach (array_chunk($rowsToInsert, 1000) as $chunk) {
            Hotspot_user::insert($chunk);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Imported: '.count($rowsToInsert).' user(s). Skipped: '.count($skipped).'.',
            'created_count' => count($rowsToInsert),
            'skipped_count' => count($skipped),
            'created'       => array_slice($createdRows, 0, 1000),
            'skipped'       => array_slice($skipped, 0, 1000),
        ]);
    }

    /**
     * Store bulk users (JSON response).
     */
    public function hotspot_user_bulk_store(Request $request)
    {
        /* Validate the form data*/
        $rules = [
            'router_id'          => 'required|integer|exists:routers,id',
            'hotspot_profile_id' => [
                'required','integer',
                Rule::exists('hotspot_profiles','id')
                    ->where(fn($q) => $q->where('router_id', $request->router_id)),
            ],
            'mode'               => ['required', Rule::in(['pattern','list'])],

            // pattern mode fields
            'prefix'     => 'nullable|string|max:50',
            'start_from' => 'required_if:mode,pattern|integer|min:1',
            'count'      => 'required_if:mode,pattern|integer|min:1|max:1000',
            'pad'        => 'nullable|integer|min:1|max:10',

            // list mode fields
            'usernames_text' => 'required_if:mode,list|nullable|string',

            // password modes
            'password_mode'   => ['required', Rule::in(['same','fixed','random','list'])],
            'fixed_password'  => 'required_if:password_mode,fixed|nullable|string|min:4|max:190',
            'password_length' => 'required_if:password_mode,random|nullable|integer|min:4|max:32',
            'passwords_text'  => 'required_if:password_mode,list|nullable|string',

            'status'     => ['required', Rule::in(['active','disabled','expired','blocked'])],
            'expires_at' => 'nullable|date',
            'comment'    => 'nullable|string|max:500',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'usernames_text.required_if' => 'Please paste usernames (one per line).',
            'passwords_text.required_if' => 'Please paste passwords (one per line).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        /* Build username list */
        $mode      = $request->mode;
        $routerId  = (int) $request->router_id;
        $profileId = (int) $request->hotspot_profile_id;
        $pad       = (int) ($request->pad ?? 3);

        $usernames = [];

        if ($mode === 'pattern') {
            $prefix = (string) ($request->prefix ?? '');
            $start  = (int) $request->start_from;
            $count  = (int) $request->count;

            for ($i = 0; $i < $count; $i++) {
                $num = $start + $i;
                $usernames[] = $prefix . str_pad((string)$num, $pad, '0', STR_PAD_LEFT);
            }
        } else {
            // list mode
            $lines = preg_split('/\r\n|\r|\n/', (string)$request->usernames_text);
            foreach ($lines as $ln) {
                $u = trim($ln);
                if ($u !== '') $usernames[] = $u;
            }
        }

        // sanitize + unique (preserve order)
        $seen = [];
        $usernames = array_values(array_filter($usernames, function ($u) use (&$seen) {
            $u = trim($u);
            if ($u === '') return false;
            if (mb_strlen($u) > 190) return false;
            if (isset($seen[$u])) return false;
            $seen[$u] = true;
            return true;
        }));

        if (empty($usernames)) {
            return response()->json([
                'success' => false,
                'errors'  => ['usernames' => ['No valid usernames to create.']]
            ], 422);
        }

        // enforce hard cap (safety)
        if (count($usernames) > 1000) {
            $usernames = array_slice($usernames, 0, 1000);
        }

        /* Passwords */
        $passwordMode = $request->password_mode;
        $passwordMap  = []; // username => plain password

        if ($passwordMode === 'same') {
            foreach ($usernames as $u) $passwordMap[$u] = $u;
        } elseif ($passwordMode === 'fixed') {
            $fixed = $request->fixed_password;
            foreach ($usernames as $u) $passwordMap[$u] = $fixed;
        } elseif ($passwordMode === 'random') {
            $len = (int) $request->password_length;
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#';
            foreach ($usernames as $u) {
                $pw = '';
                for ($i=0;$i<$len;$i++) $pw .= $chars[random_int(0, strlen($chars)-1)];
                $passwordMap[$u] = $pw;
            }
        } else { // list
            $plines = preg_split('/\r\n|\r|\n/', (string)$request->passwords_text);
            $passwords = [];
            foreach ($plines as $ln) {
                $p = trim($ln);
                if ($p !== '') $passwords[] = $p;
            }
            if (count($passwords) !== count($usernames)) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['passwords_text' => ['Passwords count must match usernames count.']]
                ], 422);
            }
            foreach ($usernames as $idx => $u) {
                $passwordMap[$u] = $passwords[$idx];
            }
        }

        /* Check duplicates on DB */
        $existing = Hotspot_user::where('router_id', $routerId)
                    ->whereIn('username', $usernames)
                    ->pluck('username')
                    ->all();
        $existing = array_flip($existing); 

        $rowsToInsert = [];
        $printRows    = []; 
        $skipped      = []; 

        $nowAdminId = optional(Auth::guard('admin')->user())->id;

        foreach ($usernames as $u) {
            if (isset($existing[$u])) {
                $skipped[] = ['username' => $u, 'reason' => 'Already exists'];
                continue;
            }
            $plain = $passwordMap[$u] ?? null;
            if (!$plain || mb_strlen($plain) < 4 || mb_strlen($plain) > 190) {
                $skipped[] = ['username' => $u, 'reason' => 'Invalid password'];
                continue;
            }

            $rowsToInsert[] = [
                'router_id'          => $routerId,
                'hotspot_profile_id' => $profileId,
                'username'           => $u,
                'password_encrypted' => Crypt::encryptString($plain),
                'mac_lock'           => null,
                'status'             => $request->status,
                'expires_at'         => $request->filled('expires_at') ? $request->expires_at : null,
                'last_seen_at'       => null,
                'upload_bytes'       => 0,
                'download_bytes'     => 0,
                'uptime_seconds'     => 0,
                'created_by'         => $nowAdminId,
                'comment'            => $request->filled('comment') ? $request->comment : null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
            $printRows[] = ['username' => $u, 'password' => $plain];
        }

        if (empty($rowsToInsert)) {
            return response()->json([
                'success' => false,
                'errors'  => ['usernames' => ['Nothing to create (all duplicates or invalid).']],
            ], 422);
        }

  
        Hotspot_user::insert($rowsToInsert);

        return response()->json([
            'success'       => true,
            'message'       => 'Bulk created: '.count($rowsToInsert).' user(s). Skipped: '.count($skipped).'.',
            'created_count' => count($rowsToInsert),
            'skipped_count' => count($skipped),
            'created'       => $printRows, 
            'skipped'       => $skipped
        ]);
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
    /**-------------- Delete Hotspot user-------------- */
    public function hotspot_user_destroy($id)
    {
        $user = Hotspot_user::findOrFail($id);
        $user->delete();

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
