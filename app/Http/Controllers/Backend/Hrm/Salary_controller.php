<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Employee_advance;
use App\Models\Employee_leave;
use App\Models\Employee_salaries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Salary_controller extends Controller
{
     public function index()
    {
        $employee=Employee::latest()->get();
       return view('Backend.Pages.Hrm.Salary.index',compact('employee'));
    }
    public function all_data(Request $request){
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'employee_id', 'employee_id','employee_id','created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Employee_salaries::with(['employee','employee.department', 'employee.designation'])->when($search, function ($query) use ($search) {

            $query->where('basic_salary', 'like', "%$search%")
                  ->orWhere('house_allowance', 'like', "%$search%")
                  ->orWhereHas('employee', function ($query) use ($search) {
                      $query->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('employee.department', function ($query) use ($search) {
                      $query->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('employee.designation', function ($query) use ($search) {
                      $query->where('name', 'like', "%$search%");
                  });
        });


        $total = $query->count();
        $items = $query->orderBy($orderByColumn, $orderDirection)
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
    public function advance_salary(){
         $employee=Employee::latest()->get();
        return view('Backend.Pages.Hrm.Salary.advance_salary',compact('employee'));
    }
    public function advance_salary_all_data(Request $request){
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'employee_id', 'employee_id','employee_id','created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Employee_advance::with(['employee','employee.department', 'employee.designation'])->when($search, function ($query) use ($search) {

            $query->where('amount', 'like', "%$search%")
                  ->orWhere('advance_date', 'like', "%$search%")

                  ->orWhereHas('employee', function ($query) use ($search) {
                      $query->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('employee.department', function ($query) use ($search) {
                      $query->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('employee.designation', function ($query) use ($search) {
                      $query->where('name', 'like', "%$search%");
                  });
        });


        $total = $query->count();
        $items = $query->orderBy($orderByColumn, $orderDirection)
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
    public function advance_salary_store(Request $request){
        /* Validate the form data*/
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'description' => 'nullable|string'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        /* Create a new Instance*/
        $object = new Employee_advance();
        $object->employee_id = $request->employee_id;
        $object->amount = $request->amount;
        $object->advance_date = $request->advance_date;
        $object->description = $request->shift_name;
        /*Save to the database table*/
        $object->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Added Successfully'
        ]);
    }
}
