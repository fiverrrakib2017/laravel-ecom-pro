<?php
namespace App\Http\Controllers\Backend\Hotspot;

use App\Models\{Voucher_batch,Voucher,Hotspot_profile,Router};
use App\Services\Router_api_service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class VoucherController extends Controller
{

public function createBatch(){
for ($i=0; $i<$batch->qty; $i++) {
    $uname = ($batch->code_prefix ? $batch->code_prefix.'-' : '').self::rnd($alphabet, $batch->username_length);
    $pwd = self::rnd($alphabet, $batch->password_length);
    Voucher::create([
    'voucher_batch_id'=>$batch->id,
    'router_id'=>$profile->router_id,
    'hotspot_profile_id'=>$profile->id,
    'username'=>$uname,
    'password_encrypted'=>encrypt($pwd),
    ]);
    }
    });
}

    $batch->update(['status'=>'generated']);
    return redirect()->route('hotspot.vouchers.batch.show',$batch->id)->with('ok','Vouchers generated');
    }


    private static function rnd(string $alphabet, int $len): string
    {
    $out = '';
    $max = strlen($alphabet)-1;
    for ($i=0; $i<$len; $i++) { $out .=$alphabet[random_int(0,$max)]; } return $out; } public function
        pushBatch(RouterOsApiService $api, int $batchId){ $batch=VoucherBatch::with(['profile','router','vouchers'])->
        findOrFail($batchId);
        foreach ($batch->vouchers as $v) {
        $api->addHotspotUser($batch->router,[
        'username'=>$v->username,
        'password'=>decrypt($v->password_encrypted),
        'profile'=>$batch->profile->mikrotik_profile,
        'comment'=>'voucher:'.$batch->id,
        ]);
        }
        $batch->update(['status'=>'pushed']);
        return back()->with('ok','Batch pushed to router');
        }


        public function exportCSV(int $batchId){
        $batch = VoucherBatch::with('vouchers')->findOrFail($batchId);
        $csv = implode(",", ['username','password','profile','validity_days'])."\n";
        foreach ($batch->vouchers as $v) {
        $csv .= implode(",", [
        $v->username,
        decrypt($v->password_encrypted),
        $batch->profile->name,
        $batch->validity_days_override ?? $batch->profile->validity_days,
        ])."\n";
        }
        return response($csv,200,[
        'Content-Type'=>'text/csv',
        'Content-Disposition'=>'attachment; filename="vouchers-'.$batchId.'.csv"'
        ]);
        }

}
