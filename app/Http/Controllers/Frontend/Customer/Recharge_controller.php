<?php
namespace App\Http\Controllers\Frontend\Customer;
use App\Http\Controllers\Controller;
use App\Services\BkashService;

use Illuminate\Http\Request;
use App\Models\Branch_package;
use App\Models\Branch_transaction;
use App\Models\Customer;
use App\Models\Customer_device;
use App\Models\Customer_log;
use App\Models\Customer_recharge;
use App\Models\Grace_recharge;
use App\Models\Router as Mikrotik_router;
use App\Models\Send_message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function App\Helpers\check_pop_balance;
use function App\Helpers\customer_log;
use function App\Helpers\formate_uptime;
use function App\Helpers\get_mikrotik_user_info;
use function App\Helpers\send_message;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Facades\Cache;

class Recharge_controller extends Controller
{
    public function __construct(private BkashService $bkash) {}
    public function customer_recharge(Request $request){
        if ($request==true){
            DB::beginTransaction();
            /*Check POP/Branch Balance*/
            // $pop_balance = check_pop_balance(auth()->guard('customer')->user()->pop_id);

            // if ($pop_balance < $request->payable_amount) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Pop balance is not enough',
            //     ]);
            //     exit();
            // }
            try {
                $res = $this->bkash->createPayment([
                    'amount'  => '500',
                    'invoice' => 'adsfasdasd',
                ]);

                // keep paymentID to map later
                session(['bkash_payment_id' => $res['paymentID']]);

                return redirect()->away($res['bkashURL']);
            } catch (\Throwable $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }


            exit;
            $object                     = new Customer_recharge();
            $object->user_id            = null;
            $object->customer_id        = auth()->guard('customer')->user()->id;
            $object->pop_id             = auth()->guard('customer')->user()->pop_id;
            $object->area_id            = auth()->guard('customer')->user()->area_id;
            $object->recharge_month     = implode(',', $request->recharge_month);
            $object->transaction_type   = 'bkash';
            $object->amount             = $request->payable_amount;
            $object->note               = $request->note ?? '';
            $object->voucher_no         = $request->voucher_no ?? '';

            $customer = Customer::find(auth()->guard('customer')->user()->id);

            foreach ($request->recharge_month as $monthYear) {
                $existingRecharge = Customer_recharge::where('customer_id', $request->customer_id)
                    ->where('pop_id', $request->pop_id)
                    ->where('area_id', $request->area_id)
                    ->where('recharge_month', $monthYear)
                    ->exists();

                if ($existingRecharge) {
                     $formattedMonth = Carbon::parse($monthYear)->translatedFormat('F Y');
                    return response()->json([
                        'success' => false,
                        'message' => "Recharge for month $formattedMonth already exists.",
                    ]);
                }
            }

            $months_count           = count($request->recharge_month);
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
            /*--------Send Message For Customer --------------*/
            // if($request->send_message=='1'){
            //     $package = \App\Models\Branch_package::find($customer->package_id);
            //     $packageName = $package ? $package->name : '';

            //     $user = auth()->guard('admin')->user();
            //     $data = \App\Models\Website_information::where('pop_id', $user->pop_id)->latest()->first();
            //     if ($user->pop_id === null) {
            //         $data = \App\Models\Website_information::whereNull('pop_id')->latest()->first();
            //     }
            //     $message = "($data->name)\n\n"
            //             . "USER: {$customer->username}\n"
            //             . "ID: {$customer->id}\n"
            //             . "NAME: {$customer->fullname}\n"
            //             . "BILL: Tk {$request->payable_amount}\n\n"
            //             . "Thanks for your payment";


            //     /* Create a new Instance*/
            //     $send_message =new Send_message();
            //     $send_message->pop_id = $customer->pop_id;
            //     $send_message->area_id = $customer->area_id;
            //     $send_message->customer_id = $customer->id;
            //     $send_message->message =$message;
            //     $send_message->sent_at = Carbon::now();
            //     /*Call Send Message Function */
            //     send_message($customer->phone, $message);
            //     /* Save to the database table*/
            //     $send_message->save();
            // }
            if ($object->save()) {
                customer_log($object->customer_id, 'recharge',null, 'Customer Recharge Completed!');

                /*Call Router activation Function*/
                $this->router_activation($object->customer_id);


                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Recharge successfully.',
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Recharge failed. Please try again.',
                ]);
            }
        }
    }
}
