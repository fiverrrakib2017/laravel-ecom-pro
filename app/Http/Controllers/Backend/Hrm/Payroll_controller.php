<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Employee_advance;
use App\Models\Employee_leave;
use App\Models\Employee_payroll;
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


}
