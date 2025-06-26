<?php

namespace App\Http\Controllers\Backend\Tickets;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Customer_Invoice;
use App\Models\Customer_Transaction_History;
use App\Models\Ticket;
use App\Models\Send_message;
use App\Models\Ticket_complain_type;
use App\Models\User;
use App\Models\Ticket_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function App\Helpers\send_message;

class Ticket_controller extends Controller
{
    public function index()
    {
        $branch_user_id=Auth::guard('admin')->user()->pop_id ?? null;
        if(!empty($branch_user_id)){
            $tickets=Ticket::where('pop_id',$branch_user_id)->count();
            $ticket_completed=Ticket::where('status','1')->where('pop_id',$branch_user_id)->count();
            $ticket_pending=Ticket::where('status','0')->where('pop_id',$branch_user_id)->count();
        }else{
            $tickets=Ticket::count();
            $ticket_completed=Ticket::where('status','1')->count();
            $ticket_pending=Ticket::where('status','0')->count();
        }

        return view('Backend.Pages.Tickets.index',compact('tickets','ticket_completed','ticket_pending'));
    }

    public function get_all_data(Request $request)
    {
        $search = $request->search['value'] ?? null;
        $customer_id = $request->customer_id;
        $pop_id = $request->pop_id;
        $area_id = $request->area_id;
        $status = $request->status;
        $columnsForOrderBy = ['id', 'status', 'created_at', 'priority_id', 'customer_id', 'customer_id', 'customer_id', 'customer_id', 'customer_id', 'customer_id', 'customer_id', 'created_at'];


        $orderByColumn = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';
        /*Check if search value is empty*/
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        /*Check if branch user  value is empty*/
        $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;

        $baseQuery =  Ticket::with(['customer', 'assign', 'complain_type', 'pop', 'area'])
            ->when($search, function ($query) use ($search) {
                $query
                    ->where('status', 'like', "%$search%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('fullname', 'like', "%$search%")->orWhere('phone', 'like', "%$search%");
                    })
                    ->orWhereHas('complain_type', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('pop', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('area', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('assign', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            })
            ->when($pop_id, function ($query) use ($pop_id) {
                $query->where('pop_id', $pop_id);
            })
            /*Customer Filter*/
            ->when($customer_id, function ($query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            /*POP/BRANCH Filter*/
            ->when($branch_user_id, function ($query) use ($branch_user_id) {
                $query->where('pop_id', $branch_user_id);
            })
            ->when($area_id, function ($query) use ($area_id) {
                $query->where('area_id', $area_id);
            })
             ->when($status !== null, function ($query) use ($status) {
                $query->where('status', $status);
            });
        $filteredQuery = clone $baseQuery;
        /*Pagination*/
        $paginatedData = $baseQuery
            ->orderBy($columnsForOrderBy[$orderByColumn] ?? 'id', $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => Ticket::count(),
            'recordsFiltered' => $filteredQuery->count(),
            'data' => $paginatedData,
        ]);
    }



    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            /*GET Customer POP & Area ID*/
            $customer = Customer::find($request->customer_id);
            $request->merge(['pop_id' => $customer->pop_id]);
            $request->merge(['area_id' => $customer->area_id]);

            /*Validate the form data*/
            $this->validateForm($request);

            /*Check Ticket Already Exist*/
            $check = Ticket::where('customer_id', $request->customer_id)->where('status', 0)->first();
            if ($check) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket already exists!',
                ]);
            }

            /* Save Ticket */
            $object = new Ticket();
            $object->customer_id = $request->customer_id;
            $object->ticket_for = $request->ticket_for;
            $object->ticket_assign_id = $request->ticket_assign_id;
            $object->ticket_complain_id = $request->ticket_complain_id;
            $object->priority_id = $request->priority_id;
            $object->pop_id = $request->pop_id;
            $object->area_id = $request->area_id;
            $object->note = $request->note;
            $object->percentage = $request->percentage ?? '0%';
            $object->status = $request->status_id;
            $object->save();

            /* Send Message to the Customer */
            if ($request->send_message == '1') {
                $message = "প্রিয় {username}, আপনার টিকিট (#{ticket_id}) গ্রহণ করা হয়েছে। আমরা খুব দ্রুত আপনার সাথে যোগাযোগ করব।";
                $message = str_replace('{ticket_id}', $object->id, $message);
                $message = str_replace('{username}', $customer->username, $message);
                $message = str_replace('{customer_id}', $customer->id, $message);

                $send_message = new Send_message();
                $send_message->pop_id = $customer->pop_id;
                $send_message->area_id = $customer->area_id;
                $send_message->customer_id = $customer->id;
                $send_message->message = $message;
                $send_message->sent_at = now();
                $send_message->save();

                send_message($customer->phone, $message);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Added successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function change_status($id)
    {
        $object = Ticket::find($id);
        $object->status = $object->status == 1 ? 0 : 1;
        $object->update();
        return response()->json([
            'success' => true,
            'message' => 'Completed successfully!',
        ]);
    }

    public function delete(Request $request)
    {
        $object = Ticket::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
    public function edit($id)
    {
        $data = Ticket::find($id);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }
    public function view($id)
    {
        $data = Ticket::with('customer', 'assign','complain_type', 'pop', 'area')->find($id);
        $ticekts_details=Ticket_details::where('ticket_id', $id)->get();
        if ($data) {
           return view('Backend.Pages.Tickets.Profile',compact('data','ticekts_details'));
            exit();
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }
    public function add_ticekts_activity(Request $request){
        /*Validate the form data*/
        $rules = [
            'ticket_id' => 'required|integer',
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
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
        DB::beginTransaction();

        try {

            /* Save Ticket */
            $object = new Ticket_details();
            $object->ticket_id = $request->ticket_id;
            $object->action_title = $request->title;
            $object->description = $request->description;
            $object->status = $request->status;
            $object->save();

            /*Update Ticket Table When status is completed*/
            if(!empty($request->status) && $request->status=='completed')
            $tickets=Ticket::find($request->ticket_id);
            $tickets->status=1;
            $tickets->update();

            /*Message Rule Validation*/
            if ($request->send_message == '1' && empty($request->message)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please type a message!',
                ]);
            }


            /* Send Message to the Customer */
            if ($request->send_message == '1') {
                $customer=Customer::find($request->customer_id);
                $send_message = new Send_message();
                $send_message->pop_id = $customer->pop_id;
                $send_message->area_id = $customer->area_id;
                $send_message->customer_id = $customer->id;
                $send_message->message = $request->message;
                $send_message->sent_at = now();
                $send_message->save();

                send_message($customer->phone, $request->message);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank You.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function get_customer_tickets($customer_id){
        if(!empty($customer_id) && isset($customer_id)){
          $data=  Ticket::with(['customer', 'assign', 'complain_type', 'pop', 'area'])->where('customer_id',$customer_id)->latest()->get();
            if ($data) {
                return response()->json(['success' => true, 'data' => $data]);
                exit();
            } else {
                return response()->json(['success' => false, 'message' => 'Not found.']);
            }
        }
        exit;
    }

    public function update(Request $request, $id)
    {
        $this->validateForm($request);

        $object = Ticket::findOrFail($id);
        $object->customer_id = $request->customer_id;
        $object->ticket_for = $request->ticket_for;
        $object->ticket_assign_id = $request->ticket_assign_id;
        $object->ticket_complain_id = $request->ticket_complain_id;
        $object->priority_id = $request->priority_id;

        $object->note = $request->note;
        $object->percentage = $request->percentage ?? '0%';
        $object->status = $request->status_id;
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
            'customer_id' => 'required|integer',
            'ticket_for' => 'required|integer',
            'ticket_assign_id' => 'required|integer',
            'ticket_complain_id' => 'required|integer',
            'priority_id' => 'required|integer',
            'status_id' => 'required|integer',
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
