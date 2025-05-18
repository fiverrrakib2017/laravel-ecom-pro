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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Salary_controller extends Controller
{
    public function index()
    {
        $employee = Employee::latest()->get();
        return view('Backend.Pages.Hrm.Salary.index', compact('employee'));
    }
    public function all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'employee_id', 'employee_id', 'employee_id', 'created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Employee_salaries::with(['employee', 'employee.department', 'employee.designation'])->when($search, function ($query) use ($search) {
            $query
                ->where('basic_salary', 'like', "%$search%")
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
        $items = $query->orderBy($orderByColumn, $orderDirection)->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }

    /*************************** Advance Salary  Start *************************************************/
    public function advance_salary()
    {
        $employee = Employee::latest()->get();
        return view('Backend.Pages.Hrm.Salary.advance_salary', compact('employee'));
    }
    public function advance_salary_all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'employee_id', 'employee_id', 'employee_id', 'created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Employee_advance::with(['employee', 'employee.department', 'employee.designation'])->when($search, function ($query) use ($search) {
            $query
                ->where('amount', 'like', "%$search%")
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
        $items = $query->orderBy($orderByColumn, $orderDirection)->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }
    public function advance_salary_store(Request $request)
    {
        /* Validate the form data*/
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'description' => 'nullable|string',
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
        $object = new Employee_advance();
        $object->employee_id = $request->employee_id;
        $object->amount = $request->amount;
        $object->advance_date = $request->advance_date;
        $object->description = $request->description;
        /*Save to the database table*/
        $object->save();

        return response()->json([
            'success' => true,
            'message' => 'Added Successfully',
        ]);
    }
    public function get_advance_salary($request_id)
    {
        if (empty($request_id)) {
            return response()->json(['success' => false, 'message' => 'ID Not Provide']);
            exit();
        }
        $data = Employee_advance::find($request_id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
    public function update_advance_salary(Request $request)
    {
        /* Validate the form data */
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'description' => 'nullable|string',
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

        /* Find the existing instance */
        $object = Employee_advance::find($request->id);
        if (!$object) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Not found',
                ],
                404,
            );
        }

        /* Update the Instance */
        $object->employee_id = $request->employee_id;
        $object->amount = $request->amount;
        $object->advance_date = $request->advance_date;
        $object->description = $request->description;

        /* Save the changes to the database table */
        $object->update();

        return response()->json([
            'success' => true,
            'message' => 'Updated Successfully',
        ]);
    }

    public function delete(Request $request)
    {
        $object = Employee_advance::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Successfully',
        ]);
    }

    public function advance_salary_report()
    {
        return view('Backend.Pages.Hrm.Salary.report');
    }
    public function fetch_advance_salary_report_data(Request $request)
    {
        $fromDate = $request->from_date;
        $endDate = $request->end_date;

        $advances = DB::table('employee_advances')
            ->join('employees', 'employee_advances.employee_id', '=', 'employees.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('admins', 'employee_advances.approved_by', '=', 'admins.id')
            ->whereBetween('advance_date', [$fromDate, $endDate])
            ->select('employee_advances.*', 'employees.name as employee_name', 'departments.name as department', 'designations.name as designation', 'admins.name as approved_by_name')
            ->get();

        $totalAmount = $advances->sum('amount');

        if ($advances->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No data found',
            ]);
        }

        $html = '';
        $i = 1;

        foreach ($advances as $advance) {
            $status = '';

            if ($advance->status == 'Pending') {
                $status = '<span class="badge bg-danger ">Pending</span>';
            } elseif ($advance->status == 'Approved') {
                $status = '<span class="badge bg-success">Approved</span>';
            } elseif ($advance->status == 'Rejected') {
                $status = '<span class="badge bg-danger">Rejected</span>';
            }

            $html .=
                '<tr>
                <td>' .
                $i++ .
                '</td>
                <td><a href="' .
                route('admin.hr.employee.view', $advance->employee_id) .
                '" target="_blank">' .
                $advance->employee_name .
                '</a></td>
                <td>' .
                ($advance->department ?? '-') .
                '</td>
                <td>' .
                ($advance->designation ?? '-') .
                '</td>
                <td>' .
                number_format($advance->amount, 2) .
                '</td>
                <td>' .
                $advance->advance_date .
                '</td>
                <td>' .
                ($advance->approved_date ?? '-') .
                '</td>
                <td>' .
                ($advance->approved_by_name ?? '-') .
                '</td>
                <td>' .
                $status .
                '</td>
                <td>' .
                ($advance->description ?? '-') .
                '</td>
            </tr>';
        }

        // Add total row
        $html .=
            '<tr>
            <td colspan="4" class="text-end"><strong class="text-success">Total</strong></td>
            <td><strong class="text-success">' .
            number_format($totalAmount, 2) .
            '</strong></td>
            <td colspan="5"></td>
        </tr>';

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }
    /*************************** Advance Salary  END *************************************************/
}
