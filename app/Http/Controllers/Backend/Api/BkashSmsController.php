<?php
namespace App\Http\Controllers\Backend\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Send_message;
use App\Models\Customer_recharge;
use App\Models\Grace_recharge;
use Illuminate\Support\Carbon;
use function App\Helpers\send_message;
use function App\Helpers\router_activation;
use function App\Helpers\customer_log;
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

           $this->_send_message("Account Not Found.", $customer);
        }

        /*------------------ Duplicate Recharge check ----------------------*/
        if(Customer_recharge::where('recharge_month', date('Y-m'))->where('customer_id',$customer->id)->exists()){
            $this->_send_message("Recharge Already Paid", $customer);
            exit;
        }
        /* Store recharge data */
            $object                     = new Customer_recharge();
            $object->user_id            = null;
            $object->customer_id        = $customer->id;
            $object->pop_id             = $customer->pop_id;
            $object->area_id            = $customer->area_id;
            $object->recharge_month     = implode(',', date('Y-m'));
            $object->transaction_type   = 'bkash';
            $object->amount             = $amount;
            $object->note               = 'Bkash Send Money';
            $object->voucher_no         =  '';

            $customer = Customer::find(auth()->guard('customer')->user()->id);



            $months_count           = count($data['recharge_month']);
            $base_date              = strtotime($customer->expire_date) > time() ? $customer->expire_date : date('Y-m-d');
            $new_expire_date        = date('Y-m-d', strtotime("+$months_count months", strtotime($base_date)));
            $customer->expire_date  = $new_expire_date;
            $object->paid_until     = $new_expire_date;

            $customer->update();
            /*-----------Customer Grace Recharge Start----------------*/
            $get_grace_recharge = Grace_recharge::where('customer_id', $customer->id)->first();
            if ($get_grace_recharge) {
                $customer_data = Customer::find($customer->id);
                /*Remove Grace Recharge Days*/
                if ($customer_data->expire_date) {
                    $customer_data->expire_date = \Carbon\Carbon::parse($customer_data->expire_date)->subDays($get_grace_recharge->days);
                    $customer_data->save();
                }
                /*Delete Grace Rechage**/
                $get_grace_recharge->delete();
                customer_log($object->customer_id, 'recharge',null, 'Customer Grace Recharge Remove!');
            }
            if ($object->save()) {
                customer_log($object->customer_id, 'recharge',null, 'Customer Recharge Bkash Completed!');

                /*Call Router activation Function*/
                router_activation($object->customer_id);


                DB::commit();
                 /*----------- Send Message ------------*/
                $this->_send_message("Your Recharge Has benn Successfully Completed", $customer);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Recharge failed. Please try again.',
                ]);
            }









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
