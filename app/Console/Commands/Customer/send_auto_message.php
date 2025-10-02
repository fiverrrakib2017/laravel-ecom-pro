<?php

namespace App\Console\Commands\Customer;
use App\Models\Auto_message;
use App\Models\Customer_recharge;
use App\Models\Branch_package;
use App\Models\Send_message;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use function App\Helpers\send_message;
class send_auto_message extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send_auto_message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = now()->addDays(2)->toDateString();
        $get_message_template = Auto_message::where('key', 'bill_due_reminder')
        ->where('is_active', true)
        ->first();

        if(empty($get_message_template)){
            $this->warn('Template bill_due_reminder not found or inactive.');
            exit;
        }
        $_all_customer = Customer::whereDate('expire_date', $targetDate)
        ->whereNotNull('phone')
        ->whereRaw("phone REGEXP '^[0-9]{11}$'")
        ->where('is_delete', '0')
        ->whereNotIn('status', ['expired', 'disabled', 'discontinue'])->get();
        foreach($_all_customer as $customer){
            /*--------- Get customer -----------*/
            $customer = Customer::find($customer->id);
         
            if(!$customer) continue;

            /*--------- Get customer Due Calculation -----------*/
            $credit_recharges = Customer_recharge::where('customer_id', $customer->id)
            ->where('transaction_type', 'credit')
            ->get(['recharge_month', 'amount']);

            $due_paids = Customer_recharge::where('customer_id', $customer->id)
            ->where('transaction_type', 'due_paid')
            ->get(['recharge_month', 'amount']);

            $paid_months = $due_paids->pluck('recharge_month')->toArray();
            $total_due = 0;
            foreach ($credit_recharges as $credit) {
                if (!in_array($credit->recharge_month, $paid_months)) {
                    $total_due += $credit->amount;
                }
            }
            if(!preg_match('/^(?:\+88)?01[3-9]\d{8}$/', $customer->phone)){
                continue;
            }

            /* Prepare dynamic message */
            $message = str_replace(
                ['{username}', '{mobile}', '{area}', '{package}', '{expire_date}', '{pop}','{due}'],
                [
                    $customer->username ?? '',
                    $customer->phone ?? '',
                    $customer->area->name ?? '',
                    Branch_package::find($customer->package_id)->name ?? '',
                    $customer->expire_date ?? '',
                    $customer->pop->name ?? '',
                    $total_due > 0 ? $total_due : 0
                ],
                $get_message_template->body
            );


            /*------------ Create a new Instance ---------*/
            $object = new Send_message();
            $object->pop_id = $customer->pop_id;
            $object->area_id = $customer->area_id;
            $object->customer_id = $customer->id;
            $object->message = $message;
            $object->sent_at = Carbon::now();

            /*-------- Call Send Message Function --------*/
            send_message($customer->phone, $message);

            /* Save to the database table */
            $object->save();
        }

    }
}
