<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Employee_advance;
use App\Models\Employee_leave;
use App\Models\Employee_payroll;
use App\Models\Employee_salaries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Payroll_controller extends Controller
{
    public function index()
    {
        return view('Backend.Pages.Hrm.Payroll.index');
    }
    public function create()
    {
        $employees = Employee::latest()->get();
        return view('Backend.Pages.Hrm.Payroll.create', compact('employees'));
    }
    public function all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'employee_id', 'employee_id', 'employee_id', 'created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Employee_payroll::with(['employee', 'employee.department', 'employee.designation'])->when($search, function ($query) use ($search) {
            $query
                ->where('basic_salary', 'like', "%$search%")
                ->orWhere('month_year', 'like', "%$search%")
                ->orWhere('advance_salary', 'like', "%$search%")
                ->orWhere('loan_deduction', 'like', "%$search%")
                ->orWhere('payment_date', 'like', "%$search%")

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
        $items = $query->orderBy($orderByColumn, $orderDirection)->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }

    public function store(Request $request){
        /* Validate the form data*/
        $rules = [
            'employee_id'     => 'required|exists:employees,id',
            'month_year'      => 'required|date',
            'payment_date'    => 'nullable|date',
            'payment_method'  => 'required|in:Cash,Bank',
            'status'          => 'required|in:Paid,Unpaid',
            'advance_salary'  => 'required|numeric',
            'loan_deduction'  => 'required|numeric',
            'net_salary'      => 'required|numeric',
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

        /*Get employee salary info*/
        $salary = Employee_salaries::where('employee_id', $request->employee_id)->where('is_current',true)->first();

        if (!$salary) {
            return response()->json([
                'success' => true,
                'message' => 'Employee salary structure not found.',
            ]);
        }
        /* Create a new Instance*/
        $payroll = new Employee_payroll();
        $payroll->employee_id     = $request->employee_id;
        $payroll->salary_id       = $salary->id;
        $payroll->month_year      = $request->month_year;
        $payroll->basic_salary    = $request->basic_salary;
        $payroll->advance_salary  = $request->advance_salary;
        $payroll->loan_deduction  = $request->loan_deduction;
        $payroll->tax             = $salary->tax ?? 0;
        $payroll->net_salary      = $request->net_salary;
        $payroll->payment_date    = $request->payment_date;
        $payroll->payment_method  = $request->payment_method;
        $payroll->status          = $request->status;
        $payroll->save();

        return response()->json([
            'success' => true,
            'message' => 'Employee payroll created successfully.',
        ]);
    }

    public function delete(Request $request)
    {
        $object = Employee_payroll::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Successfully',
        ]);
    }
}
