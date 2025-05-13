<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Department_controller extends Controller
{
    public function index()
    {
       return view('Backend.Pages.Hrm.Department.index');
    }
    public function all_data(Request $request){
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'name'];
        $orderByColumnIndex = $request->order[0]['column'];
        $orderDirection = $request->order[0]['dir'];
        $orderByColumn = $columnsForOrderBy[$orderByColumnIndex];

        /*Start building the query*/
        $query = Department::query();

        /*Apply the search filter*/
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")

                  //   ->orWhereHas('student', function($q) use ($search) {
                //       $q->where('name', 'like', "%$search%");
                //   });
                ;
            });
        }

        /* Get the total count of records*/
        $totalRecords = Department::count();

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
            'department_name' => 'required|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        /* Create a new Instance*/
        $object = new Department();
        $object->name = $request->department_name;


        /*Save to the database table*/
        $object->save();
        return response()->json([
            'success' => true,
            'message' => 'Added Successfully'
        ]);
    }
    public function get_department($id){
        $data = Department::find($id);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    public function update(Request $request){
        /* Validate the form data */
        $rules = [
            'department_name' => 'required|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /* Find the existing instance */
        $object = Department::find($request->id);
        if (!$object) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found'
            ], 404);
        }

        /* Update the Instance */
        $object->name = $request->department_name;

        /* Save the changes to the database table */
        $object->update();

        return response()->json([
            'success' => true,
            'message' => 'Updated Successfully'
        ]);
    }

    public function delete(Request $request){
        $object = Department::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Successfully'
        ]);
    }
}
