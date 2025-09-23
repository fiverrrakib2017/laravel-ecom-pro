<?php
namespace App\Http\Controllers\Backend\Hotspot;
use App\Http\Controllers\Controller;
use App\Models\{Voucher_batch,Voucher,Hotspot_profile,Router};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

class BatchController extends Controller{
    /** Batches list */
    public function index(Request $request)
    {
        $query = Voucher_batch::query()
            ->with(['router:id,name', 'profile:id,name,mikrotik_profile'])
            ->orderByDesc('id');

        if ($request->filled('router_id')) $query->where('router_id', $request->integer('router_id'));
        if ($request->filled('hotspot_profile_id')) $query->where('hotspot_profile_id', $request->integer('hotspot_profile_id'));
        if ($request->has('status') && $request->status !== '') $query->where('status', $request->status);
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function($q) use ($term) {
                $q->where('name','like',"%{$term}%")
                  ->orWhere('code_prefix','like',"%{$term}%");
            });
        }

        $batches = $query->paginate(20)->withQueryString();
        $routers = Router::orderBy('name')->get(['id','name']);
        $profiles = $request->filled('router_id')
            ? Hotspot_profile::where('router_id', $request->integer('router_id'))->orderBy('name')->get(['id','name'])
            : collect();

        return view('Backend.Pages.Hotspot.Vouchers.batch_index', compact('batches','routers','profiles'));
    }

    /** Show Generate Batch form */
    public function create()
    {
        $routers = Router::orderBy('name')->get(['id','name']);
        return view('Backend.Pages.Hotspot.Vouchers.batch_create', compact('routers'));
    }

    /** Generate a batch + its vouchers (AJAX JSON) */
    public function store(Request $request)
    {
        /* Validate the form data*/
        $rules = [
            'router_id'           => 'required|integer|exists:routers,id',
            'hotspot_profile_id'  => [
                'required','integer',
                Rule::exists('hotspot_profiles','id')->where(fn($q) => $q->where('router_id', $request->router_id)),
            ],
            'name'                => 'required|string|max:255',
            'qty'                 => 'required|integer|min:1|max:2000',
            'code_prefix'         => 'nullable|string|max:10',
            'username_length'     => 'required|integer|min:4|max:16',
            'password_length'     => 'required|integer|min:4|max:16',
            'password_same_as_username' => 'nullable|boolean',
            'validity_days_override'    => 'nullable|integer|min:1|max:3650',
            'expires_at'          => 'nullable|date',
            'price_minor'         => 'nullable|integer|min:0',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'errors'=>$validator->errors()], 422);
        }

        $routerId  = (int) $request->router_id;
        $profileId = (int) $request->hotspot_profile_id;
        $qty       = (int) $request->qty;
        $uLen      = (int) $request->username_length;
        $pLen      = (int) $request->password_length;
        $prefix    = trim((string)$request->code_prefix);
        $samePw    = $request->boolean('password_same_as_username');
        $expiresAt = $request->filled('expires_at') ? $request->expires_at : null;

        // username alphabet similar to Mikhmon style (avoid ambiguous)
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $pwChars  = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#';

        try {
            DB::beginTransaction();

            // Create batch (start as draft; set to generated after insert)
            $batch = new Voucher_batch();
            $batch->router_id              = $routerId;
            $batch->hotspot_profile_id     = $profileId;
            $batch->name                   = $request->name;
            $batch->qty                    = $qty;
            $batch->code_prefix            = $prefix ?: null;
            $batch->username_length        = $uLen;
            $batch->password_length        = $pLen;
            $batch->validity_days_override = $request->input('validity_days_override');
            $batch->expires_at             = $expiresAt;
            $batch->price_minor            = $request->input('price_minor', 0);
            $batch->status                 = 'draft';
            $batch->meta                   = json_encode([
                'password_same_as_username' => $samePw,
            ]);
            $batch->save();

            // Gather existing usernames once to reduce collisions
            $existing = Voucher::pluck('username')->all();
            $existingSet = array_fill_keys($existing, true);
            unset($existing);

            $rows = [];
            $attempts = 0;
            $need = $qty;

            while ($need > 0) {
                $attempts++;
                if ($attempts > $qty * 20) { // safety
                    break;
                }

                $user = $prefix . self::randString($alphabet, $uLen);
                if (isset($existingSet[$user])) continue;

                $passPlain = $samePw ? $user : self::randString($pwChars, $pLen);

                $rows[] = [
                    'voucher_batch_id'   => $batch->id,
                    'router_id'          => $routerId,
                    'hotspot_profile_id' => $profileId,
                    'username'           => $user,
                    'password_encrypted' => Crypt::encryptString($passPlain),
                    'status'             => 'new',
                    'expires_at'         => $expiresAt,
                    'hotspot_user_id'    => null,
                    'use_count'          => 0,
                    'meta'               => json_encode(['password_plain_preview' => $passPlain]),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
                $existingSet[$user] = true;
                $need--;
            }

            if (empty($rows)) {
                DB::rollBack();
                return response()->json([
                    'success'=>false,
                    'errors' => ['qty'=>['Could not generate unique vouchers. Try increasing length or removing prefix.']]
                ], 422);
            }

            // Insert in chunks
            foreach (array_chunk($rows, 1000) as $chunk) {
                Voucher::insert($chunk);
            }

            // Mark batch generated
            $batch->status = 'generated';
            $batch->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Batch generated: '.$batch->name.' ('.$qty.' vouchers)',
                'batch_id'=> $batch->id
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success'=>false,
                'errors'=>['server'=>[$e->getMessage()]],
            ], 422);
        }
    }

    /** Helper to create random strings */
    private static function randString(string $chars, int $len): string
    {
        $out = '';
        $max = strlen($chars)-1;
        for ($i=0;$i<$len;$i++) {
            $out .= $chars[random_int(0,$max)];
        }
        return $out;
    }
}
