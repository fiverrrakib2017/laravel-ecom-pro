<?php
namespace App\Http\Controllers\Backend\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class BkashSmsController extends Controller
{
    public function receive(Request $request){

        $sms = $request->input('message');
        preg_match('/Tk\s*([\d\.]+)/', $sms, $amountMatch);
        preg_match('/Ref:\s*(\w+)/', $sms, $refMatch);
        preg_match('/TrxID\s*([A-Z0-9]+)/', $sms, $trxMatch);

        $amount = $amountMatch[1] ?? 0;
        $customerId = $refMatch[1] ?? null;
        $trxid = $trxMatch[1] ?? null;

        if(!$customerId || !$trxid){
            return response()->json(['status'=>'error','message'=>'Invalid SMS Format']);
        }

        // Duplicate check
        if(Payment::where('transaction_id', $trxid)->exists()){
            return response()->json(['status'=>'error','message'=>'Duplicate Transaction']);
        }

        $customer = Customer::find($customerId);
        if(!$customer){
            return response()->json(['status'=>'error','message'=>'Customer not found']);
        }
        

        return response()->json(['status'=>'success','message'=>'Payment Added']);
    }
}
