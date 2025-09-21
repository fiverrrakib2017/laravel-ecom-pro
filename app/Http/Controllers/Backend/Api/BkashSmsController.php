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
    public function receive(Request $request)
    {
        $sms = $request->input('message');

        if (!$sms || !is_string($sms)) {
            return response()->json(['status' => 'error', 'message' => 'SMS message missing']);
        }

        $amount     = $this->_parseAmount($sms);
        $customerId = $this->_parseRef($sms);
        $trxid      = $this->_parseTrxId($sms);

        if (!$customerId || !$trxid) {
            return response()->json(['status' => 'error', 'message' => 'Invalid SMS Format']);
        }

        if (!ctype_digit((string) $customerId)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid customer reference']);
        }

        $customer = Customer::find((int) $customerId);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found']);
        }
        if ($amount <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Invalid amount']);
        }

        /*-------Check Duplicate Recharge For this month------*/
        if (Customer_recharge::where('recharge_month', date('Y-m'))
                ->where('customer_id', $customer->id)
                ->exists()) {
            $this->_send_message("Recharge Already Paid", $customer);
            return response()->json(['success' => false, 'message' => 'Recharge already paid for this month']);
        }

        if (Customer_recharge::where('voucher_no', $trxid)->exists()) {
            $this->_send_message("Duplicate Transaction Detected", $customer);
            return response()->json(['success' =>false, 'message' => 'Duplicate transaction']);
        }

        DB::beginTransaction();
        try {
            $object                   = new Customer_recharge();
            $object->user_id          = null;
            $object->customer_id      = $customer->id;
            $object->pop_id           = $customer->pop_id;
            $object->area_id          = $customer->area_id;
            $object->recharge_month   = date('Y-m');
            $object->transaction_type = 'bkash';
            $object->amount           = $amount;
            $object->note             = 'Bkash Send Money';
            $object->voucher_no       = $trxid;

            $base_date         = (strtotime($customer->expire_date) > time()) ? $customer->expire_date : date('Y-m-d');
            $new_expire_date   = date('Y-m-d', strtotime("+1 months", strtotime($base_date)));


            $customer->expire_date = $new_expire_date;
            $customer->status = 'active';

            $get_grace_recharge = Grace_recharge::where('customer_id', $customer->id)->first();
            if ($get_grace_recharge) {
                if ($customer->expire_date) {
                    $customer->expire_date = Carbon::parse($customer->expire_date)
                        ->subDays((int)$get_grace_recharge->days)
                        ->toDateString();
                }
                $get_grace_recharge->delete();
                customer_log($object->customer_id, 'recharge', null, 'Customer Grace Recharge Remove!');
            }

            $customer->save();

            $object->paid_until = $customer->expire_date;

            if ($object->save()) {
                customer_log($object->customer_id, 'recharge', null, 'Customer Recharge Bkash Completed!');


                // router_activation($object->customer_id);

                DB::commit();

                $this->_send_message("Dear {username}, Your Recharge Has been Successfully Completed", $customer);

                return response()->json([
                    'success' => true,
                    'message' => 'Recharge completed',
                    'paid_until' => $object->paid_until,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Recharge failed. Please try again.',
                ]);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error'   => app()->environment('production') ? null : $e->getMessage(),
            ], 500);
        }
    }

    protected function _send_message($message, $customer)
    {
        $message = str_replace('{username}', $customer->username, $message);

        $send_message              = new Send_message();
        $send_message->pop_id      = $customer->pop_id;
        $send_message->area_id     = $customer->area_id;
        $send_message->customer_id = $customer->id;
        $send_message->message     = $message;
        $send_message->sent_at     = Carbon::now();

        // SMS API
        send_message($customer->phone, $message);

        $send_message->save();
    }

    protected function _parseAmount(string $sms)
    {
        if (preg_match('/Tk\.?\s*([\d\.,]+)/i', $sms, $m) ||
            preg_match('/BDT\s*([\d\.,]+)/i', $sms, $m) ||
            preg_match('/([\d\.,]+)\s*Tk/i', $sms, $m)) {
            $raw = $m[1];
        } elseif (preg_match('/amount[:\s]*([\d\.,]+)/i', $sms, $m)) {
            $raw = $m[1];
        } else {
            return 0;
        }
        $raw = str_replace(',', '', $raw);
        return (float) $raw;
    }

    protected function _parseRef(string $sms)
    {
        if (preg_match('/Ref[:\s#-]*([A-Za-z0-9\-]+)/i', $sms, $m)) {
            return $m[1];
        }
        if (preg_match('/reference[:\s]*([A-Za-z0-9\-]+)/i', $sms, $m)) {
            return $m[1];
        }
        return null;
    }

    protected function _parseTrxId(string $sms)
    {
        if (preg_match('/TrxID[:\s]*([A-Za-z0-9\-]+)/i', $sms, $m)) {
            return $m[1];
        }
        if (preg_match('/Transaction ID[:\s]*([A-Za-z0-9\-]+)/i', $sms, $m)) {
            return $m[1];
        }
        return null;
    }
}
