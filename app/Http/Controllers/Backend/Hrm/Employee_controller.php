<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Employee_salaries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Employee_controller extends Controller
{
    public function create()
    {
        $department = Department::latest()->get();
        $designation = Designation::latest()->get();
        return view('Backend.Pages.Hrm.Employee.create', compact('department','designation'));
    }
    public function index()
    {
        $employees=Employee::with('department', 'designation')->latest()->get();
        return view('Backend.Pages.Hrm.Employee.index', compact('employees'));
    }
    public function all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'department_id', 'name'];
        $orderByColumnIndex = $request->order[0]['column'];
        $orderDirection = $request->order[0]['dir'];
        $orderByColumn = $columnsForOrderBy[$orderByColumnIndex];

        // Start building the query
        $query = Designation::with('department');

        // Apply the search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")->orWhereHas('department', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }

        // Get the total count of records
        $totalRecords = Designation::count();

        // Get the count of filtered records
        $filteredRecords = $query->count();

        // Apply ordering, pagination and get the data
        $items = $query->orderBy($orderByColumn, $orderDirection)->skip($request->start)->take($request->length)->get();

        // Return the response in JSON format
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        /* Validate the form data*/
        $rules = [
        'name'                  => 'required|string|max:255',
        'email'                 => 'required|email|unique:employees,email',
        'phone'                 => 'required',
        'hire_date'             => 'required|date',
        'address'               => 'required|string',
        'father_name'           => 'required|string',
        'mother_name'           => 'required|string',
        'gender'                => 'required',
        'birth_date'            => 'required|date',
        'national_id'           => 'required|unique:employees,national_id',
        'religion'              => 'required|string',
        'highest_education'     => 'required|string',
        'department_id'         => 'required|exists:departments,id',
        'designation_id'        => 'required|exists:designations,id',
        'salary'                => 'required|numeric',
        'emergency_contact_name'=> 'required|string',
        'emergency_contact_phone'=> 'required|string',
        'status'                => 'required|in:active,inactive,resigned',

        /* Salary inputs*/
        'basic_salary'          => 'required|numeric',
        'house_allowance'       => 'required|numeric',
        'medical_allowance'     => 'required|numeric',
        'other_allowance'       => 'required|numeric',
        'tax'                   => 'required|numeric',
        'effective_from'        => 'nullable|date',
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
        $employee = new Employee();
        $employee->name                     = $request->name;
        $employee->email                    = $request->email;
        $employee->phone                    = $request->phone;
        $employee->phone_2                  = $request->phone_2;
        $employee->hire_date                = $request->hire_date;
        $employee->address                  = $request->address;
        $employee->father_name              = $request->father_name;
        $employee->mother_name              = $request->mother_name;
        $employee->gender                   = $request->gender;
        $employee->birth_date               = $request->birth_date;
        $employee->national_id              = $request->national_id;
        $employee->religion                 = $request->religion;
        $employee->blood_group              = $request->blood_group;
        $employee->highest_education        = $request->highest_education;
        $employee->previous_school          = $request->previous_school;
        $employee->department_id            = $request->department_id;
        $employee->designation_id           = $request->designation_id;
        $employee->salary                   = $request->salary;
        $employee->emergency_contact_name   = $request->emergency_contact_name;
        $employee->emergency_contact_phone  = $request->emergency_contact_phone;
        $employee->remarks                  = $request->remarks;
        $employee->status                   = $request->status;


        /* Handle photo upload if available*/

        if ($request->hasFile('photo')) {
            $file       = $request->file('photo');
            $filename   = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/photos'), $filename);
            $employee->photo= $filename;
        }
        $employee->save();

        /* Create Employee Salary*/
        $netSalary =
        $request->basic_salary +
        $request->house_allowance +
        $request->medical_allowance +
        $request->other_allowance -
        $request->tax;

        $salary                     = new Employee_salaries();
        $salary->employee_id        = $employee->id;
        $salary->basic_salary       = $request->basic_salary;
        $salary->house_allowance    = $request->house_allowance;
        $salary->medical_allowance  = $request->medical_allowance;
        $salary->other_allowance    = $request->other_allowance;
        $salary->tax                = $request->tax;
        $salary->net_salary         = $netSalary;
        $salary->effective_from     = $request->effective_from ?? now();
        $salary->is_current         = true;
        $salary->save();
        return response()->json([
            'success' => true,
            'message' => 'Added Successfully',
        ]);
    }
    public function get_employee($id)
    {
        $data = Employee::with('department', 'designation')->find($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
    public function edit($id)
    {
        $data = Employee::find($id);
        $department = Department::latest()->get();
        $designation = Designation::latest()->get();
        return view('Backend.Pages.Hrm.Employee.edit', compact('department','designation', 'data'));
    }

    public function update(Request $request)
    {
        /* Validate the form data */
        $rules = [
            'name'                      => 'required|string|max:255',
            'email'                     => 'required|email|unique:employees,email,' . $request->id,
            'phone'                     => 'required',
            'hire_date'                 => 'required|date',
            'address'                   => 'required|string',
            'father_name'               => 'required|string',
            'mother_name'               => 'required|string',
            'gender'                    => 'required',
            'birth_date'                => 'required|date',
            'national_id'               => 'required|unique:employees,national_id,' . $request->id,
            'religion'                  => 'required|string',
            'highest_education'         => 'required|string',
            'department_id'             => 'required|exists:departments,id',
            'designation_id'            => 'required|exists:designations,id',
            'salary'                    => 'required|numeric',
            'emergency_contact_name'    => 'required|string',
            'emergency_contact_phone'   => 'required|string',
            'status'                    => 'required|in:active,inactive,resigned',
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
        $employee = Employee::find($request->id);
        if (!$employee) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Not found',
                ],
                404,
            );
            exit;
        }

        /* Update the Instance */
        $employee->name                     = $request->name;
        $employee->email                    = $request->email;
        $employee->phone                    = $request->phone;
        $employee->phone_2                  = $request->phone_2;
        $employee->hire_date                = $request->hire_date;
        $employee->address                  = $request->address;
        $employee->father_name              = $request->father_name;
        $employee->mother_name              = $request->mother_name;
        $employee->gender                   = $request->gender;
        $employee->birth_date               = $request->birth_date;
        $employee->national_id              = $request->national_id;
        $employee->religion                 = $request->religion;
        $employee->blood_group              = $request->blood_group;
        $employee->highest_education        = $request->highest_education;
        $employee->previous_school          = $request->previous_school;
        $employee->department_id            = $request->department_id;
        $employee->designation_id           = $request->designation_id;
        $employee->salary                   = $request->salary;
        $employee->emergency_contact_name   = $request->emergency_contact_name;
        $employee->emergency_contact_phone  = $request->emergency_contact_phone;
        $employee->remarks                  = $request->remarks;
        $employee->status                   = $request->status;

        /* Handle photo upload if available*/
        if ($request->hasFile('photo')) {
            $file       = $request->file('photo');
            $filename   = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/photos'), $filename);
            $employee->photo= $filename;
        }

        /* Save the changes to the database table */
        $employee->update();

        return response()->json([
            'success' => true,
            'message' => 'Updated Successfully',
        ]);
    }

    public function view($id)
    {
        $data = Employee::find($id);
        $department = Department::latest()->get();
        $designation = Designation::latest()->get();
        return view('Backend.Pages.Hrm.Employee.view', compact('department','designation', 'data'));
    }

    public function delete(Request $request)
    {
        $object = Employee::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Successfully',
        ]);
    }
}
