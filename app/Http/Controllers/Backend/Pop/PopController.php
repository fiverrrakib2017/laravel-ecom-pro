<?php
namespace App\Http\Controllers\Backend\Pop;
use App\Http\Controllers\Controller;
use App\Models\Branch_package;
use App\Models\Branch_transaction;
use App\Models\Customer;
use App\Models\Customer_recharge;
use App\Models\Package;
use App\Models\Pop_area;
use App\Models\Pop_branch;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
class PopController extends Controller
{
    public function index()
    {
        return view('Backend.Pages.Pop.index');
    }

    public function get_all_data(Request $request)
{
    $search = $request->search['value'];
    $columnsForOrderBy = ['id', 'name', 'username', 'phone', 'status'];
    $orderByColumn = $request->order[0]['column'];
    $orderDirectection = $request->order[0]['dir'];

    $object = Pop_branch::when($search, function ($query) use ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('username', 'like', "%$search%")
              ->orWhere('phone', 'like', "%$search%");
        });
    })->orderBy($columnsForOrderBy[$orderByColumn], $orderDirectection);

    $total = $object->count();

    $item = $object->skip($request->start)->take($request->length)->get();

    /*Get Branch Customer Details*/
    $item->transform(function ($branch) {
        // $branch->active_customer =Customer::where('pop_id', $branch->id)
        //     ->where('status', 'active')
        //     ->count();
         $branch->active_customer=Customer::where('pop_id', $branch->id)->where('status','!=', 'disabled')->where('status','!=', 'discontinue')->where('is_delete', '0')->count();

        $branch->online = Customer::where('pop_id', $branch->id)
            ->where('status', 'online')
            ->count();

        $branch->offline = Customer::where('pop_id', $branch->id)
            ->where('status', 'offline')
            ->count();

        $branch->expired =Customer::where('pop_id', $branch->id)
            ->where('status', 'expired')
            ->count();

        return $branch;
    });

    return response()->json([
        'draw' => $request->draw,
        'recordsTotal' => $total,
        'recordsFiltered' => $total,
        'data' => $item,
    ]);
}

    public function store(Request $request)
    {
        /* Validate the form data */
        $this->validateForm($request);

        /* Start Transaction */
        DB::beginTransaction();

        try {
            /* Create a new Pop_branch */
            $popBranch = new Pop_branch();
            $popBranch->name = $request->name;
            $popBranch->username = $request->username;
            $popBranch->password = $request->password;
            $popBranch->phone = $request->phone;
            $popBranch->email = $request->email;
            $popBranch->address = $request->address;
            $popBranch->status = $request->status ?? 1;
            $popBranch->save();

            /* Create related Admin login */
            \App\Models\Admin::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'phone' => $request->phone,
                'user_type' => 2,
                'pop_id' => $popBranch->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Added successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Something went wrong: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
    public function branch_package_store(Request $request)
    {
        /*Validate the form data*/
        $rules = [
            'package_id' => 'required|integer',
            'pop_id' => 'required|integer',
            'purchase_price' => 'required',
            'sales_price' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }
        if (Branch_package::where('package_id', $request->package_id)
        ->where('pop_id', $request->pop_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Package Already Added',
            ]);
        }


        /* Create a new Supplier*/
        $object = new Branch_package();
        $object->name = Package::find(intval($request->package_id))->name;
        $object->package_id = $request->package_id;
        $object->pop_id = $request->pop_id;
        $object->purchase_price = $request->purchase_price;
        $object->sales_price = $request->sales_price;

        /* Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
    }
    public function branch_recharge_store(Request $request)
    {
        /*Validate the form data*/
        $rules = [
            'pop_id' => 'required|integer',
            'amount' => 'required',
            'transaction_type' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $object = new Branch_transaction();
        $object->pop_id = $request->pop_id;
        $object->amount = $request->amount;
        $object->transaction_type = $request->transaction_type;
        $object->note = $request->note;

        /* Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
    }
    public function branch_recharge_undo($id)
    {
        $object = Branch_transaction::find($id);
        if ($object) {
            $object->delete();
            return response()->json(['success' => true, 'message' => 'Successfully!']);
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }

    public function branch_package_edit($id)
    {
        $data = Branch_package::find($id);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }
    /*GET Brach Package With Search POP ID*/
    public function get_pop_wise_package($id)
    {
        $data = Branch_package::where('pop_id', $id)->latest()->get();
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }
    /*GET Brach Package Price With Search POP ID*/
    public function get_pop_wise_package_price($id)
    {
        $data = Branch_package::find($id);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }
    public function pop_change_status($id)
    {
        $object = Pop_branch::find($id);
        if (!$object) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'POP/Branch not found!',
                ],
                404,
            );
        }
        $object->status = $object->status == 1 ? 0 : 1;

        /* Update to the database table*/
        $object->update();
        return response()->json([
            'success' => true,
            'message' => 'Status changed successfully!',
            'new_status' => $object->status,
        ]);
    }

    public function branch_package_update(Request $request, $id)
    {
        /*Validate the form data*/
        $rules = [
            'package_id' => 'required|integer',
            'purchase_price' => 'required',
            'sales_price' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $object = Branch_package::find($id);
        $object->package_id = $request->package_id;
        $object->purchase_price = $request->purchase_price;
        $object->sales_price = $request->sales_price;

        /* Update to the database table*/
        $object->update();
        return response()->json([
            'success' => true,
            'message' => 'Package Update successfully!',
        ]);
    }

    public function branch_package_delete(Request $request){
        $object = Branch_package::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Package Delete successfully!',
        ]);
    }

    public function delete(Request $request)
    {
        $object = Pop_branch::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
    public function edit($id)
    {
        $data = Pop_branch::find($id);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }
    public function view($id)
    {
        $pop = Pop_branch::findOrFail($id);

        $due_paid = Branch_transaction::where('pop_id', $id)->where('transaction_type', 'due_paid')->sum('amount');
        $get_total_due = Branch_transaction::where('pop_id', $id)->where('transaction_type', 'credit')->sum('amount');

        $total_paid = Branch_transaction::where('pop_id', $id)->where('transaction_type', '!=', 'credit')->sum('amount');

        $total_due = $get_total_due - $due_paid;
        /*Branch Transaction Current Balance*/
        $customer_recharge_total = Customer_recharge::where('pop_id', $id)->where('transaction_type', '!=', 'due_paid')->sum('amount');

        $branch_transaction_total = Branch_transaction::where('pop_id', $id)->where('transaction_type', '!=', 'due_paid')->sum('amount');

        $current_balance = $branch_transaction_total - $customer_recharge_total;
        /*Tickets Details*/
        $total_area = Pop_area::where('pop_id', $id)->count();
        $tickets = Ticket::where('pop_id', $id)->count();
        $ticket_completed = Ticket::where('pop_id', $id)->where('status', '1')->count();
        $ticket_pending = Ticket::where('pop_id', $id)->where('status', '0')->count();

        /*Customer Details*/
        $online_customer = Customer::where('pop_id', $id)->where('status', 'online')->count();
         $active_customer=Customer::where('pop_id', $id)->where('status','!=', 'disabled')->where('status','!=', 'discontinue')->where('is_delete', '0')->count();
        $expire_customer = Customer::where('pop_id', $id)->where('status', 'expired')->count();
        $offline_customer = Customer::where('pop_id', $id)->where('status', 'offline')->count();
        $disable_customer = Customer::where('pop_id', $id)->where('status', 'disabled')->count();
        return view('Backend.Pages.Pop.View', compact('pop', 'due_paid', 'total_paid', 'total_due', 'total_area', 'tickets', 'ticket_completed', 'ticket_pending', 'online_customer', 'active_customer', 'expire_customer', 'offline_customer', 'disable_customer', 'current_balance'));
    }
    public function auto_login($pop_id){
        if (auth()->guard('admin')->user()->user_type != 1) {
            abort(404);
        }

        if(!isset($pop_id) && empty($pop_id)){
            abort(404);
        }

        $pop_branch=Pop_branch::where('status','1')->where('id',$pop_id)->first();
       // auth()->guard('admin')->logout();
        if (Auth::guard('admin')->attempt(['email' => $pop_branch->email, 'password' => $pop_branch->password])) {
            Cache::flush();
            return redirect()->intended(route('admin.dashboard'));
        }else{
            return redirect()->back()->with('error-message','Invalid Email or Password');
        }

    }

    public function update(Request $request)
    {
        $this->validateForm($request);

        $object = Pop_branch::findOrFail($request->id);
        $object->name = $request->name;
        $object->username = $request->username;
        //$object->password = $request->password;
        $object->phone = $request->phone;
        $object->email = $request->email;
        $object->address = $request->address;
        $object->status = $request->status ?? 1;
        $object->update();

        return response()->json([
            'success' => true,
            'message' => 'Update successfully!',
        ]);
    }
    private function validateForm($request)
    {
        /*Validate the form data*/
        $rules = [
            'name' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'phone' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }
    }
}
