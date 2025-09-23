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
class VoucherController extends Controller{
    /** Print Sheets UI for a single batch-------- */
    public function print(Request $request)
    {
        
        $batchId = $request->query('batch_id');
        $batch   = Voucher_batch::with(['router:id,name','profile:id,name,mikrotik_profile'])->findOrFail($batchId);
        // fetch all vouchers of the batch (limit safety)
        $vouchers = Voucher::where('voucher_batch_id', $batch->id)
            ->orderBy('id')->get(['id','username','password_encrypted','status','meta']);

        return view('Backend.Pages.Hotspot.Vouchers.print', compact('batch','vouchers'));
    }

    /** Report: Sold / Activated list */
    public function sales(Request $request)
    {
        $query = Voucher::query()
            ->with(['batch:id,name','router:id,name','profile:id,name'])
            ->whereIn('status', ['sold','activated'])
            ->orderByDesc('id');

        if ($request->filled('router_id'))  $query->where('router_id', $request->integer('router_id'));
        if ($request->filled('batch_id'))   $query->where('voucher_batch_id', $request->integer('batch_id'));
        if ($request->has('status') && $request->status !== '') $query->where('status', $request->status);
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where('username','like',"%{$term}%");
        }

        $vouchers = $query->paginate(30)->withQueryString();

        $routers  = Router::orderBy('name')->get(['id','name']);
        $batches  = Voucher_batch::orderByDesc('id')->get(['id','name']);

        return view('Backend.Pages.Hotspot.Vouchers.sales', compact('vouchers','routers','batches'));
    }

    /**------------ Export CSV ------------- */
    public function export(Request $request): StreamedResponse
    {
        $filename = 'vouchers_export_'.now()->format('Ymd_His').'.csv';

        $query = Voucher::query()
            ->with(['batch:id,name'])
            ->orderBy('id');

        if ($request->filled('batch_id')) $query->where('voucher_batch_id', $request->integer('batch_id'));
        if ($request->has('status') && $request->status !== '') $query->where('status', $request->status);

        $callback = function() use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['batch','username','password','status','expires_at']);

            $query->chunk(1000, function($chunk) use ($out) {
                foreach ($chunk as $v) {
                    $plain = null;
                    try {
                        $meta = json_decode($v->meta ?? '[]', true);
                        // prefer plain from meta saved at creation
                        $plain = $meta['password_plain_preview'] ?? null;
                    } catch (\Throwable $e) {}
                    fputcsv($out, [
                        optional($v->batch)->name,
                        $v->username,
                        $plain, 
                        $v->status,
                        optional($v->expires_at)->format('Y-m-d H:i:s'),
                    ]);
                }
            });
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
