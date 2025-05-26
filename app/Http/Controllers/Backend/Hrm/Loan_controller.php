<?php

namespace App\Http\Controllers\Backend\Hrm;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Employee_loans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Loan_controller extends Controller
{
    public function index()
    {
        return view('Backend.Pages.Hrm.Loan.index');
    }
    public function create()
    {
        $employees = Employee::latest()->get();
        return view('Backend.Pages.Hrm.Loan.create', compact('employees'));
    }
    public function all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'employee_id', 'employee_id', 'employee_id', 'created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Employee_loans::with(['employee', 'employee.department', 'employee.designation'])->when($search, function ($query) use ($search) {
            $query
                ->where('installment_amount', 'like', "%$search%")
                ->orWhere('total_installments', 'like', "%$search%")
                ->orWhere('paid_installments', 'like', "%$search%")
                ->orWhere('date_issued', 'like', "%$search%")

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
    public function edit($id)
    {
        $data = Employee_loans::findOrFail($id);
        $employees = Employee::latest()->get();
        return view('Backend.Pages.Hrm.Loan.edit', compact('data', 'employees'));
    }
    public function update(Request $request)
    {
        /* Validate the form data*/
        $rules = [
            'employee_id'           => 'required|exists:employees,id',
            'loan_amount'           => 'required|numeric',
            'installment_amount'    => 'required|numeric',
            'total_installments'    => 'required|numeric',
            'paid_installments'     => 'required|numeric',
            'date_issued'           => 'nullable|date',
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
        /* Update the existing Instance*/
        $object = Employee_loans::findOrFail($request->id);
        $object->employee_id            = $request->employee_id;
        $object->loan_amount            = $request->loan_amount;
        $object->installment_amount     = $request->installment_amount;
        $object->total_installments     = $request->total_installments;
        $object->paid_installments      = $request->paid_installments;
        $object->date_issued      = $request->date_issued;
        $object->status      = 'Pending';
        $object->save();

        return response()->json([
            'success' => true,
            'message' => 'Employee Loan updated successfully.',
        ]);
    }

    public function store(Request $request){
        /* Validate the form data*/
        $rules = [
            'employee_id'           => 'required|exists:employees,id',
            'loan_amount'           => 'required|numeric',
            'installment_amount'    => 'required|numeric',
            'total_installments'    => 'required|numeric',
            'paid_installments'     => 'required|numeric',
            'date_issued'           => 'nullable|date',
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
        $object = new Employee_loans();
        $object->employee_id            = $request->employee_id;
        $object->loan_amount            = $request->loan_amount;
        $object->installment_amount     = $request->installment_amount;
        $object->total_installments     = $request->total_installments;
        $object->paid_installments      = $request->paid_installments;
        $object->date_issued      = $request->date_issued;
        $object->status      = 'Pending';
        $object->save();

        return response()->json([
            'success' => true,
            'message' => 'Employee Loan created successfully.',
        ]);
    }

    public function delete(Request $request)
    {
        $object = Employee_loans::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Successfully',
        ]);
    }
}
