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
         /*------------------ Cusotmer Found For Recharge ----------------------*/
        $customer=Customer::find($customerId);
        if(!$customer){
            //return response()->json(['status'=>'error','message'=>'Customer not found']);
        }

        /*------------------ Duplicate Recharge check ----------------------*/
        if(Customer_recharge::where('recharge_month', date('Y-m'))->where('customer_id',$customer->id)->exists()){
            $this->_send_message($message, $customer);
            exit;
        }
        /* Store recharge data */
        $object = new Customer_recharge();
        $object->user_id = auth()->guard('admin')->user()->id;
        $object->customer_id = $customer->id;
        $object->pop_id = $request->pop_id;
        $object->area_id = $request->area_id;
        $object->recharge_month = implode(',', [date('Y-m')]);
        $object->transaction_type = 'cash';
        $object->paid_until = date('Y-m-d', strtotime('+1 month'));
        $object->amount = $request->amount;
        $object->note = 'Bkash Send Money';
        $object->save();

        /*----------- Send Message ------------*/
         $this->_send_message($message, $customer);






        /*---------------Send Message For Payment Successfull -------------------*/
    }
    protected function _send_message($message, $customer){
            $message = str_replace('{username}', $customer->username, $message);
            /* Create a new Instance*/
            $send_message =new Send_message();
            $send_message->pop_id = $customer->pop_id;
            $send_message->area_id = $customer->area_id;
            $send_message->customer_id = $customer->id;
            $send_message->message =$message;
            $send_message->sent_at = Carbon::now();
            /*-----SMS API-------*/
            send_message($customer->phone, $message);
            $send_message->save();
    }
}
