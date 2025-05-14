@extends('Backend.Layout.App')
@section('title','Dashboard | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
        <div class="card-header">
          <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Class</label>
                            <select name="class_id"  class="form-control">
                                <option value="">---Select---</option>
                                {{-- @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Section </label>
                            <select name="section_id"  class="form-control">
                                <option value="">---Select---</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mt-4">
                        <button type="button" name="submit_btn" class="btn btn-success mt-1"><i class="mdi mdi-magnify"></i> Find Now</button>
                        </div>
                    </div>
            </div>
        </div>
            <div class="card-body">
                <div class="table-responsive" id="tableStyle">
                    <table id="datatable1" class="table table-striped table-bordered    " cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="Custom Checkbox" id="selectAll"> </th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Hire Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr id="no-data">
                            <td colspan="7" class="text-center">No data available</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right d-none">
                <button type="button" id="submitAttendance" class="btn btn-success">Submit Attendance</button>
            </div>
        </div>

    </div>
</div>




@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function(){
    $("select[name='class_id']").select2();
    $("select[name='section_id']").select2();
    
    $(document).on('change','select[name="class_id"]',function(){
        var sections = @json($sections);
        var selectedClassId = $(this).val();
        var filteredSections = sections.filter(function(section) {
            /*Filter sections by class_id*/
            return section.class_id == selectedClassId;
        });

        /* Update Section dropdown*/
        var sectionOptions = '<option value="">--Select Section--</option>';
        filteredSections.forEach(function(section) {
            sectionOptions += '<option value="' + section.id + '">' + section.name + '</option>';
        });

        $('select[name="section_id"]').html(sectionOptions);
        $('select[name="section_id"]').select2();
    });
    $("button[name='submit_btn']").on('click', function(e) {
        e.preventDefault();
        var submitBtn = $('button[name="submit_btn"]');
        var originalBtnText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>');
        submitBtn.prop('disabled', true);
        var class_id = $("select[name='class_id']").val();
        var section_id = $("select[name='section_id']").val();
        var student_id = null;
        if(class_id != ''){
            $.ajax({
                url:"{{ route('admin.student.student_filter') }}",
                type:"POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{class_id:class_id,section_id:section_id, student_id:student_id},
                success:function(data){
                    if(data.code == 200 && data.data.length > 0 && data.data != null && data.data != undefined && data.data != '' && data.data != 'null' && data.data != 'undefined'){
                        $('.card-footer').removeClass('d-none');
                        var students = data.data;
                        var html='';

                        $.each(students, function (index, student) {
                            var viewUrl = "{{ route('admin.student.view', ':id') }}".replace(':id', student.id);
                            html += '<tr>';
                            html += '<td><input type="checkbox"  value="' + student.id + '" name="student_ids[]" class="Custom Checkbox student-checkbox"></td>';
                            html += '<td>' + student.name + '</td>';
                            html += '<td>' + student.current_class.name + '</td>';
                            html += '<td>' + student.section.name + '</td>';
                            html += '<td>' + student.religion + '</td>';
                            html += '<td>' + student.phone + '</td>';
                            html += '<td><a href="' + viewUrl + '" class="btn btn-success btn-sm"><i class="fas fa-eye"></i></a></td>';
                            html += '</tr>';
                        });
                        $('#datatable1 tbody').html(html);
                    }else{
                        $('#datatable1 tbody').html('<tr id="no-data"><td colspan="7" class="text-center">No data available</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred. Please try again.');
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                },
                complete:function(){
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                }
            });
        }else{
            toastr.error('Please select class and section.');
            submitBtn.html(originalBtnText);
            submitBtn.prop('disabled', false);
        }
    });
    /* Select or Deselect All Checkboxes*/
    $(document).on('click', '#selectAll', function() {
    if ($(this).is(':checked')) {
        $('.student-checkbox').prop('checked', true);
    } else {
        $('.student-checkbox').prop('checked', false);
    }
    });
    /* Submit Attendance*/
    $(document).on('click', '#submitAttendance', function() {
        var selectedStudents = [];
        $('.student-checkbox:checked').each(function() {
            selectedStudents.push($(this).val());
        });
        if(selectedStudents.length > 0) {
            $('#submitAttendance').prop('disabled', true).html(
                '<i class="fa fa-spinner fa-spin"></i> Submitting...'
            );
            $.ajax({
                url: "{{ route('admin.student.attendence.store') }}",
                type: "POST",
                data: {
                    student_ids: selectedStudents,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    }
                    if(response.success==false){
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred. Please try again.');
                },

                complete: function() {
                    $('#submitAttendance').prop('disabled', false).html('Submit Attendance');
                }
            });
        } else {
            toastr.error('Please select at least one student!');
        }
    });
});
</script>
@endsection

