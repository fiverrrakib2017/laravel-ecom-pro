<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Customer_Invoice;
use App\Models\Pop_area;
use App\Models\Product;
use App\Models\Product_Order;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\Customer_recharge;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Services\DateService;
use Illuminate\Support\Facades\Cache;
class AdminController extends Controller
{
    protected $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }
    public function login_form(){
        return view('Backend.Pages.Login.Login');
    }
    public function get_data(Request $request)
{
    if ($request->data == 'customer_data') {
        $response_data = Customer::latest()->take(5)->get();
        return response()->json($response_data);
    }

    if ($request->has('date')) {
        $s_date = $this->dateService->getStartDate($request->date);
        $e_date = $this->dateService->getEndDate($request->date);

        $count_entries = function ($model) use ($s_date, $e_date) {
            return $model::whereBetween('created_at', [$s_date, $e_date])->count();
        };

        $sum_invoice_amount = function ($model) use ($s_date, $e_date) {
            return $model::whereDate('created_at', '>=', $s_date)
                         ->whereDate('created_at', '<=', $e_date)
                         ->sum('paid_amount');
        };

        $total_sales_amount = $sum_invoice_amount(Customer_Invoice::class);
        $total_purchase_amount = $sum_invoice_amount(Supplier_Invoice::class);
        $total_customer = $count_entries(Customer::class);
        $total_customer_invoice = $count_entries(Customer_Invoice::class);
        $total_supplier = $count_entries(Supplier::class);
        $total_products = $count_entries(Product::class);

        /* Calculate net profit*/
        $net_profit = $total_sales_amount - $total_purchase_amount;

        $response_data = [
            'total_sales_amount' => intval($total_sales_amount),
            'total_purchase_amount' => intval($total_purchase_amount),
            'total_customer_invoice' => intval($total_customer_invoice),
            'total_customer' => intval($total_customer),
            'total_supplier' => intval($total_supplier),
            'total_products' => intval($total_products),
            'net_profit' => intval($net_profit),
            'total_customer_order' => intval($total_customer_invoice),
            'total_quantity' => intval(Product::sum('qty')),
        ];

        return response()->json($response_data);
    }

    if ($request->data == 'get_top_rated_product') {
        $top_selling_products = DB::table('customer__invoice__details')
        ->select('product_id', DB::raw('SUM(qty) as total_qty'))
        ->groupBy('product_id')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->get();

        $top_selling_products = $top_selling_products->map(function ($item) {
        $product = DB::table('products')->where('id', $item->product_id)->first();
        $product_image = DB::table('product_images')->where('product_id', $item->product_id)->first();
        return [
            'product_id' => $item->product_id,
            'total_qty' => $item->total_qty,
            'product_title' => $product ? $product->title : 'Unknown',
            'product_image' => $product_image ? $product_image->image : 'default_image.jpg',
        ];
    });

    return response()->json($top_selling_products);
    }
}

    public function login_functionality(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            Cache::flush();
            return redirect()->intended(route('admin.dashboard'));
        }else{
            return redirect()->back()->with('error-message','Invalid Email or Password');
        }
    }
    public function dashboard()
    {
        $branch_user_id=Auth::guard('admin')->user()->pop_id ?? null;
        if(!empty($branch_user_id)){
            $total_area=Pop_area::where('pop_id',$branch_user_id)->latest()->count();
            $tickets=Ticket::where('pop_id',$branch_user_id)->latest()->count();
            $ticket_completed=Ticket::where('pop_id',$branch_user_id)->where('status','1')->count();
            $ticket_pending=Ticket::where('pop_id',$branch_user_id)->where('status','0')->count();

            /*Customer Details*/
            $online_customer=Customer::where('pop_id',$branch_user_id)->where('status','online')->count();
            $active_customer=Customer::where('pop_id',$branch_user_id)->where('status','!=', 'disabled')->where('status','!=', 'discontinue')->where('is_delete', '0')->count();
            $expire_customer=Customer::where('pop_id',$branch_user_id)->where('status','expired')->where('is_delete', '0')->count();
            $offline_customer=Customer::where('pop_id',$branch_user_id)->where('status','offline')->where('is_delete', '0')->count();
            $disable_customer=Customer::where('pop_id',$branch_user_id)->where('status','disabled')->where('is_delete', '0')->count();
            $discontinue_customer=Customer::where('pop_id',$branch_user_id)->where('status','discontinue')->where('is_delete', '0')->count();
             $total_customer=Customer::where('pop_id',$branch_user_id)->where('is_delete', '0')->count();
              /*Customer Recharge Details*/
            $total_recharged = Customer_recharge::where('pop_id',$branch_user_id)->where('transaction_type', '!=', 'due_paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount')?? 0;

            $totalPaid = Customer_recharge::where('pop_id',$branch_user_id)->where('transaction_type', '!=', 'credit')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount')?? 0;

            $get_total_due = Customer_recharge::where('pop_id',$branch_user_id)->where('transaction_type', 'credit')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount') ?? 0;

            $duePaid = Customer_recharge::where('pop_id',$branch_user_id)->where('transaction_type', 'due_paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount') ?? 0;
        }
        if(empty($branch_user_id)){
            $total_area=Pop_area::latest()->count();
            $tickets=Ticket::latest()->count();
            $ticket_completed=Ticket::where('status','1')->count();
            $ticket_pending=Ticket::where('status','0')->count();

            /*Customer Details*/
            $online_customer=Customer::where('status','online')->where('is_delete', '0')->count();
            $active_customer=Customer::where('status','!=', 'disabled')->where('status','!=', 'discontinue')->where('is_delete', '0')->count();
            $expire_customer=Customer::where('status','expired')->where('is_delete', '0')->count();
            $offline_customer=Customer::where('status','offline')->where('is_delete', '0')->count();
            $disable_customer=Customer::where('status','disabled')->where('is_delete', '0')->count();
            $discontinue_customer=Customer::where('status','discontinue')->count();
            $total_customer=Customer::where('is_delete', '0')->count();
              /*Customer Recharge Details*/
            $total_recharged = Customer_recharge::where('transaction_type', '!=', 'due_paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount')?? 0;

            $totalPaid = Customer_recharge::where('transaction_type', '!=', 'credit')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount')?? 0;

            $get_total_due = Customer_recharge::where('transaction_type', 'credit')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount') ?? 0;

            $duePaid = Customer_recharge::where('transaction_type', 'due_paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount') ?? 0;
        }


        $totalDue=$get_total_due-$duePaid;
        return view('Backend.Pages.Dashboard.index',compact('total_area','tickets','ticket_completed','ticket_pending','online_customer','active_customer','expire_customer','offline_customer','disable_customer','total_recharged','totalPaid','totalDue','duePaid','discontinue_customer','total_customer'));
    }
    /*Server Information*/
    public function server_info()
    {
        $ramUsage = shell_exec("free -m | awk 'NR==2{printf \"%.2f\", $3*100/$2 }'");
        $cpuUsage = shell_exec("top -bn1 | grep 'Cpu(s)' | awk '{print 100 - $8}'");
        $diskUsage = shell_exec("df -h / | awk 'NR==2{print $5}'");

        $ramInfo = shell_exec("free -m | awk 'NR==2{print $2 \",\" $3}'");
        list($totalRam, $usedRam) = explode(',', trim($ramInfo));

        $cpuCores = trim(shell_exec("nproc"));
        $cpuModel = trim(shell_exec("lscpu | grep 'Model name' | awk -F ':' '{print $2}'"));

        $diskInfo = shell_exec("df -h / | awk 'NR==2{print $2 \",\" $3}'");
        list($diskTotal, $diskUsed) = explode(',', trim($diskInfo));

        $uptime = shell_exec("uptime -p");
        $hostname = shell_exec("hostname");

        return response()->json([
            'ram_usage' => trim($ramUsage) . '%',
            'ram_total' => $totalRam . ' MB',
            'ram_used' => $usedRam . ' MB',

            'cpu_usage' => trim($cpuUsage) . '%',
            'cpu_cores' => $cpuCores,
            'cpu_model' => $cpuModel,

            'disk_usage' => trim($diskUsage),
            'disk_total' => $diskTotal,
            'disk_used' => $diskUsed,

            'uptime' => trim($uptime),
            'hostname' => trim($hostname),
        ]);
    }


    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
