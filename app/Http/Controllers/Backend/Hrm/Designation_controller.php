<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Designation_controller extends Controller
{
    public function index()
    {
        $department = Department::latest()->get();
        return view('Backend.Pages.Hrm.Designation.index', compact('department'));
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
            'department_id' => 'required|integer',
            'designation_name' => 'required|string|max:255',
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
        $object = new Designation();
        $object->department_id = $request->department_id;
        $object->name = $request->designation_name;

        /*Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added Successfully',
        ]);
    }
    public function get_designation($id)
    {
        $data = Designation::with('department')->find($id);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
    public function update(Request $request)
    {
        /* Validate the form data */
        $rules = [
            'department_id' => 'required|integer',
            'designation_name' => 'required|string|max:255',
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
        $object = Designation::find($request->id);
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
        $object->department_id = $request->department_id;
        $object->name = $request->designation_name;

        /* Save the changes to the database table */
        $object->update();

        return response()->json([
            'success' => true,
            'message' => 'Updated Successfully',
        ]);
    }

    public function delete(Request $request)
    {
        $object = Designation::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Successfully',
        ]);
    }
}
