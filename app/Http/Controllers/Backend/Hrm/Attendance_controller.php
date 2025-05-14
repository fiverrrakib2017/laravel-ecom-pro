<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Employee_leave;
use App\Models\Employee_salaries;
use App\Models\Employee_shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Attendance_controller extends Controller
{
     public function index()
    {
        $department=Department::latest()->get();
        $designation=Designation::latest()->get();
       $shift=Employee_shift::get();
       $employee=Employee::get();
       return view('Backend.Pages.Hrm.Attendance.index',compact('employee','shift','department','designation'));
    }
    public function all_data(Request $request){
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'name', 'department_id','designation_id','created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Employee::with(['department', 'designation'])->when($search, function ($query) use ($search) {

            $query->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhereHas('department', function ($query) use ($search) {
                      $query->where('name', 'like', "%$search%");
                  })
                  ->orWhereHas('designation', function ($query) use ($search) {
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

    public function store(Request $request){
        $studentIds = $request->input('student_ids');
        $employees = Employee::whereIn('id', $studentIds)->get();

        $first_class_check=$employees->first()->current_class;
        $first_section_check=$employees->first()->section_id;

        foreach ($employees as $employee) {
            if ($employee->current_class != $first_class_check) {
                return response()->json([
                    'success' => false,
                    'message' => 'All students must be in the same class.'
                ], 400);
            }
            if ($student->section_id != $first_section_check) {
                return response()->json([
                    'success' => false,
                    'message' => 'All students must be in the same section.'
                ], 400);
            }
        }
        $all_students = Student::where([
            'current_class' => $first_class_check,
            'section_id' => $first_section_check
        ])->pluck('id')->toArray();

        $absentStudents = array_diff($all_students, $studentIds);
        $today = now()->format('Y-m-d');

        if (!empty($studentIds)) {
            foreach ($studentIds as $studentId) {

                /*To check student has attended today*/
                $attendanceExists = Student_attendance::where([
                    ['student_id', '=', $studentId],
                    ['attendance_date', '=', $today]
                ])->exists();
                /*if not present*/
                if (!$attendanceExists) {
                    $object = new Student_attendance();
                    $object->student_id = $studentId;
                    $object->attendance_date = $today;
                    $object->shift_id = 1;
                    $object->time_in = Carbon::now()->format('H:i:s');
                    $object->time_out = Carbon::now()->format('H:i:s');
                    $object->status = 'Present';
                    $object->save();
                }
            }
        }

        if (!empty($absentStudents)) {
            foreach ($absentStudents as $absentStudentId) {
                $attendanceExists = Student_attendance::where([
                    ['student_id', '=', $absentStudentId],
                    ['attendance_date', '=', $today]
                ])->exists();

                /*If Not Absent Record Exists*/
                if (!$attendanceExists) {
                    $is_student_leave= Student_leave::where([
                        ['student_id', '=', $absentStudentId],
                        ['start_date', '<=', $today],
                        ['end_date', '>=', $today],
                        ['leave_status', '=', 'approved']
                    ])->exists();

                    if ($is_student_leave) {
                        $object = new Student_attendance();
                        $object->student_id = $absentStudentId;
                        $object->attendance_date = $today;
                        $object->shift_id = 1;
                        $object->status = 'Leave';
                        $object->save();
                    } else {
                        $object = new Student_attendance();
                        $object->student_id = $absentStudentId;
                        $object->attendance_date = $today;
                        $object->shift_id = 1;
                        $object->status = 'Absent';
                        $object->save();
                    }
                }
            }
        }

        return response()->json([
            'success'=>true,
            'message' => 'Attendance marked successfully'
        ]);
    }
    public function get_attendance($id){
        $data = Student_attendance::find($id);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function attendance_log(){
        $sections=Section::latest()->get();
        $shift=Student_shift::get();
        $student=Student::get();
         $classes=Student_class::with('section')->latest()->get();
        return view('Backend.Pages.Student.Attendance.Log',compact('student','shift','classes','sections'));
    }
    public function attendance_log_all_data(Request $request) {
        $search = $request->search['value'];

        $columnsForOrderBy = ['id', 'student.name', 'student.currentClass.name', 'student.section.name', 'status', 'time_in', 'created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Student_attendance::with(['student', 'student.currentClass', 'student.section'])
            ->when($search, function ($query) use ($search) {
                $query->where('status', 'like', "%$search%")
                    ->orWhere('time_in', 'like', "%$search%")
                    ->orWhereHas('student.currentClass', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('student', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('student.section', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            });

        if ($request->has('class_id') && !empty($request->class_id)) {
            $query->whereHas('student.currentClass', function ($query) use ($request) {
                $query->where('id', $request->class_id);
            });
        }

        if ($request->has('section_id') && !empty($request->section_id)) {
            $query->whereHas('student.section', function ($query) use ($request) {
                $query->where('id', $request->section_id);
            });
        }
        if ($request->has('date_range') && !empty($request->date_range)) {
            $dateRange = explode(' - ', $request->date_range);
            $startDate = trim($dateRange[0]);
            $endDate = trim($dateRange[1]);
            $query->whereBetween('attendance_date', [$startDate, $endDate]);
        }
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




    public function delete(Request $request){
        $object = Student_attendance::find($request->id);
        $object->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete Successfully'
        ]);
    }
    public function attendance_report(Request $request){
        $filters = [];

        if (isset($request->class_id) && !empty($request->class_id)) {
            $filters['current_class'] = $request->class_id;
        }
        if (isset($request->section_id) && !empty($request->section_id)) {
            $filters['section_id'] = $request->section_id;
        }
        // if (isset($request->attendance_type) && !empty($request->attendance_type)) {
        //     $filters['status'] = "$request->attendance_type";
        // }

        $startDate = null;
        $endDate = null;

        if (isset($request->date_range) && !empty($request->date_range)) {
            $dateRange = explode(' - ', $request->date_range);
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
        }
        $students = Student::where($filters)->get();
        $students_data = [];

        foreach ($students as $student) {
            $attendanceQuery = Student_attendance::with('student', 'student.currentClass', 'student.section')->where('student_id', $student->id);

            if ($startDate && $endDate) {
                $attendanceQuery->whereBetween('attendance_date', [$startDate, $endDate]);
            }
            if(isset($request->attendance_type) && !empty($request->attendance_type)){
                $attendanceQuery->where('status', $request->attendance_type);
            }
            $attendance = $attendanceQuery->get();
            $students_data[] = $attendance;
        }

        if ($students->isEmpty()) {
            return response()->json([
                'success' => false,
                'code' => 200,
                'data' => []
            ]);
        }
        return response()->json([
            'success' => true,
            'code' => 200,
            'data' => $students_data
        ]);
    }
}
