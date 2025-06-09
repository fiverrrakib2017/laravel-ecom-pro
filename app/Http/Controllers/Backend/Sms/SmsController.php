<?php
namespace App\Http\Controllers\Backend\Sms;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Message_template;
use App\Models\Pop_area;
use App\Models\Pop_branch;
use App\Models\Send_message;
use App\Models\Sms_configuration;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\send_message;

class SmsController extends Controller
{
    public function config()
    {
       $data= Sms_configuration::latest()->first();
        return view('Backend.Pages.Sms.Config',compact('data'));
    }
    public function sms_template_list()
    {

        return view('Backend.Pages.Sms.Template');
    }
    public function message_send_list()
    {
        return view('Backend.Pages.Sms.Send_list');
    }
    public function bulk_message_send_list(){
        return view('Backend.Pages.Sms.Bulk_send_list');
    }
    public function sms_template_get_all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'pop_id', 'name', 'message'];
        $orderByColumn = $request->order[0]['column'];
        $orderDirectection = $request->order[0]['dir'];

        $query = Message_template::with(['pop'])->when($search, function ($query) use ($search) {
            $query
                ->where('name', 'like', "%$search%")
                ->orWhere('message', 'like', "%$search%")
                ->orWhereHas('pop', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
        });

        $total = $query->count();

        $query = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirectection);

        $items = $query->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }
    public function sms_template_get($id){
        $data = Message_template::with(['pop'])->find($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
    public function config_store(Request $request)
    {
       /*Validate the form data*/
       $rules = [
        'api_url' => 'required|string',
        'api_key' => 'required|string',
        'sender_id' => 'required|string',
        'default_country_code' => 'required',
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

        /* Create a new Instance*/
        $object = Sms_configuration::firstOrNew([]);
        $object->api_url = $request->api_url;
        $object->api_key = $request->api_key;
        $object->sender_id = $request->sender_id;
        $object->default_country_code = $request->default_country_code;

        /* Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
    }

    public function sms_template_Store(Request $request){

        /*Validate the form data*/
       $rules = [
        'pop_id' => 'required|integer',
        'name' => 'required|string',
        'message' => 'required|string',
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
     /* Create a new Instance*/
     $object =new Message_template();
     $object->pop_id = $request->pop_id;
     $object->name = $request->name;
     $object->message = $request->message;

     /* Save to the database table*/
     $object->save();
     return response()->json([
         'success' => true,
         'message' => 'Added successfully!',
     ]);

    }
    public function send_message_store(Request $request){
        /*Validate the form data*/
        $rules = [
            'message' => 'required|string',
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

        if(empty($request->customer_ids) && isset($request->customer_ids)){
            return response()->json(['success'=>false, 'message'=>'Customer Not Found']);
        }
        foreach($request->customer_ids as $customer_id){
            /*Get POP ID From Customer table*/
            $customer=Customer::find($customer_id);
            /* Create a new Instance*/
            $object =new Send_message();
            $object->pop_id = $customer->pop_id;
            $object->area_id = $customer->area_id;
            $object->customer_id = $customer_id;
            $object->message = $request->message;
            $object->sent_at = Carbon::now();


            /*Call Send Message Function */
           send_message($customer->phone, $request->message);
            /* Save to the database table*/
            $object->save();
        }


        return response()->json([
            'success' => true,
            'message' => 'Added successfully!',
        ]);
    }
    public function send_message_get_all_data(Request $request){
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'pop_id', 'name', 'message'];
        $orderByColumn = $request->order[0]['column'];
        $orderDirectection = $request->order[0]['dir'];

        $query = Send_message::with(['pop','customer'])->when($search, function ($query) use ($search) {
            $query
                ->where('message', 'like', "%$search%")
                // ->orWhere('message', 'like', "%$search%")
                ->orWhereHas('pop', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('fullname', 'like', "%$search%");
                    $query->where('username', 'like', "%$search%");
                });
        });

        $total = $query->count();

        $query = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirectection);

        $items = $query->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }
    public function sms_template_delete(Request $request)
    {
        $object = Message_template::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
    public function send_message_delete(Request $request){
        $object = Send_message::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
    /*********************** SMS Logs   ******************************/
    public function sms_logs(){
        return view('Backend.Pages.Sms.Logs');
    }
    public function get_all_sms_logs_data(Request $request)
{
    $pop_id = $request->pop_id;
    $area_id = $request->area_id;
    $search = $request->search['value'];
    $columnsForOrderBy = ['id', 'pop_id', 'name', 'message'];
    $orderByColumn = $request->order[0]['column'];
    $orderDirectection = $request->order[0]['dir'];

    /*Check if branch user value is empty*/
    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;

    $query = Send_message::with(['pop', 'area', 'customer', 'customer.package']);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('message', 'like', "%$search%")
              ->orWhereHas('pop', function ($q) use ($search) {
                  $q->where('name', 'like', "%$search%");
              })
              ->orWhereHas('customer', function ($q) use ($search) {
                  $q->where('fullname', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%");
              });
        });
    }

    if ($pop_id) {
        $query->where('pop_id', $pop_id);
    }

    if ($branch_user_id) {
        $query->where('pop_id', $branch_user_id);
    }

    // Filter by area
    if ($area_id) {
        $query->where('area_id', $area_id);
    }
     if ($request->from_date) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->to_date) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    $total = $query->count();

    $items = $query->orderBy($columnsForOrderBy[$orderByColumn], $orderDirectection)
                   ->skip($request->start)
                   ->take($request->length)
                   ->get();

    return response()->json([
        'draw' => $request->draw,
        'recordsTotal' => $total,
        'recordsFiltered' => $total,
        'data' => $items,
    ]);
}

    /*********************** SMS Report   ******************************/
    public function sms_report(){
          return view('Backend.Pages.Sms.Report');
    }

}
