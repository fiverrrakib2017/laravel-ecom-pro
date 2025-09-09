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
            $pop_balance = check_pop_balance(auth()->guard('customer')->user()->pop_id);

            if ($pop_balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pop balance is not enough',
                ]);
                exit();
            }
            return $request->all(); exit;
            $_invoice_number =  'INV-' . date('Ymd') . '-' . mt_rand(1000, 9999);
            try {
                $res = $this->bkash->createPayment([
                    'amount'  => $request->amount,
                    'invoice' => $_invoice_number,
                ]);

                /**---------Save Payment Id for later-------****/
                session([
                    'bkash_payment_id' => $res['paymentID'],
                    'bkash_invoice'    => $_invoice_number,
                    'recharge_data'    => $request->all(),
                ]);
                DB::commit();
                /**--------------Redirect to bKash page-------------***/
                return redirect()->away($res['bkashURL']);
            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }

        }
    }
    public function bkash_callback(Request $request){
        $paymentID  = session('bkash_payment_id');
        $data       = session('recharge_data');

        if (!$paymentID) {
            return redirect()->route('customer.portal')
                ->with('error', 'Invalid payment request!');
        }

        try {
        /***----------- bKash execute----------****/
        $execute = $this->bkash->executePayment($paymentID);

        if (!empty($execute['transactionStatus']) && $execute['transactionStatus'] === 'Completed') {

            $object                     = new Customer_recharge();
            $object->user_id            = null;
            $object->customer_id        = auth()->guard('customer')->user()->id;
            $object->pop_id             = auth()->guard('customer')->user()->pop_id;
            $object->area_id            = auth()->guard('customer')->user()->area_id;
            $object->recharge_month     = implode(',', $data['recharge_month']);
            $object->transaction_type   = 'bkash';
            $object->amount             = $data['amount'];
            $object->note               = $data['note'] ?? '';
            $object->voucher_no         = $execute['merchantInvoiceNumber'] ?? '';

            $customer = Customer::find(auth()->guard('customer')->user()->id);

            foreach ($data['recharge_month'] as $monthYear) {
                $existingRecharge = Customer_recharge::where('customer_id', auth()->guard('customer')->user()->id)
                    ->where('pop_id', auth()->guard('customer')->user()->pop_id)
                    ->where('area_id', auth()->guard('customer')->user()->area_id)
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
                //$this->router_activation($object->customer_id);


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
        } else {
            return redirect()->route('customer.portal')
                ->with('error', 'bKash payment failed or cancelled!');
        }
    } catch (\Throwable $e) {
        return redirect()->route('customer.portal')
            ->with('error', $e->getMessage());
    }

    }
}
