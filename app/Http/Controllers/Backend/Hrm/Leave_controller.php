<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Employee_leave;
use App\Models\Employee_salaries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Leave_controller extends Controller
{
    public function index()
    {
        $employee=Employee::latest()->get();
       return view('Backend.Pages.Hrm.Leave.index',compact('employee'));
    }
    public function all_data(Request $request){
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'name', 'leave_type','leave_reason','status','start_date', 'end_date'];
        $orderByColumnIndex = $request->order[0]['column'];
        $orderDirection = $request->order[0]['dir'];
        $orderByColumn = $columnsForOrderBy[$orderByColumnIndex];

        /*Start building the query*/
        $query = Employee_leave::with('employee');

        /*Apply the search filter*/
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('leave_type', 'like', "%$search%")
                ->where('leave_reason', 'like', "%$search%")
                  ->orWhere('start_date', 'like', "%$search%")
                  ->orWhere('end_date', 'like', "%$search%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
            });
        }

        /* Get the total count of records*/
        $totalRecords = Employee_leave::count();

        /* Get the count of filtered records*/
        $filteredRecords = $query->count();

        /* Apply ordering, pagination and get the data*/
        $items = $query->orderBy($orderByColumn, $orderDirection)
                    ->skip($request->start)
                    ->take($request->length)
                    ->get();

        /* Return the response in JSON format*/
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $items,
        ]);
    }
    public function store(Request $request){
        /* Validate the form data*/
        $rules = [
            'employee_id' => 'required|integer',
            'leave_type' => 'required|string',
            'leave_reason' => 'required|string',
            'leave_status' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        /* Create a new Instance*/
        $object = new Employee_leave();
        $object->employee_id = $request->employee_id;
        $object->leave_type = $request->leave_type;
        $object->leave_reason = $request->leave_reason;
        $object->leave_status = $request->leave_status;
        $object->start_date = $request->start_date;
        $object->end_date = $request->end_date;

        /*Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added Successfully'
        ]);
    }
    public function get_leave($id){
        $data = Employee_leave::find($id);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    public function update(Request $request){
        /* Validate the form data */
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string',
            'leave_reason' => 'required|string',
            'leave_status' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /* Find the existing instance */
        $object = Employee_leave::find($request->id);
        if (!$object) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 404);
        }

        /* Update the Instance */
        $object->employee_id = $request->employee_id;
        $object->leave_type = $request->leave_type;
        $object->leave_reason = $request->leave_reason;
        $object->leave_status = $request->leave_status;
        $object->start_date = $request->start_date;
        $object->end_date = $request->end_date;

        /* Save the changes to the database table */
        $object->update();

        return response()->json([
            'success' => true,
            'message' => 'Updated Successfully'
        ]);
    }

    public function delete(Request $request){
        $object = Employee_leave::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Successfully'
        ]);
    }
}
