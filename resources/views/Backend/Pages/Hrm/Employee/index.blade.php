@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')

@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('admin.hr.employee.create') }}" class="btn btn-success "><i class="fas fa-users"></i>
                        Add New Employee</a>
                        <button type="button" id="printBtn" class="btn btn-primary "><i class="fas fa-print"></i>
                            Generate ID Card</button>
                </div>
                <div class="card-body">

                    <div class="table-responsive" id="tableStyle">
                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="" id="employee_select_all"/></th>
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
                                @foreach ($employees as $employee)
                                    <tr>
                                          <td><input type="checkbox" class="employee-checkbox" value="{{ $employee->id }}" name="employee_ids[]"/></td>
                                        <td>

                                            @if (!empty($employee->photo))
                                                <img src="{{ asset('uploads/photos/' . $employee->photo) }}" width="40"
                                                    height="40" class="rounded-circle">
                                            @else
                                                <img src="{{ asset('Backend/images/avatar.png') }}" width="40"
                                                    height="40" class="rounded-circle">
                                            @endif
                                        </td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->phone }}</td>
                                        <td>{{ $employee->department->name ?? '' }}</td>
                                        <td>{{ $employee->designation->name ?? '' }}</td>
                                        <td>{{ $employee->hire_date }}</td>
                                        <td>
                                            @if ($employee->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($employee->status == 'inactive')
                                                <span class="badge bg-warning">Inactive</span>
                                            @else
                                                <span class="badge bg-danger">Resigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-danger ml-1 delete-btn"><i class="fas fa-trash"></i></button>
                                                <a href="{{ route('admin.hr.employee.edit', $employee->id) }}"
                                                    class="btn btn-sm btn-primary ml-1"><i class="fas fa-edit"></i></a>
                                                <a href="{{ route('admin.hr.employee.view', $employee->id) }}"
                                                    class="btn btn-sm btn-success ml-1"><i class="fas fa-eye"></i></a>

                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>




    <div id="deleteModal" class="modal fade">
        <div class="modal-dialog modal-confirm">
            <form action="{{ route('admin.hr.designation.delete') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header flex-column">
                        <div class="icon-box">
                            <i class="fas fa-trash"></i>
                        </div>
                        <h4 class="modal-title w-100">Are you sure?</h4>
                        <input type="hidden" name="id" value="">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to delete these records? This process cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change','#employee_select_all',function(){
                if ($(this).is(':checked')) {
                    $(".employee-checkbox").prop('checked', true);
                } else {
                    $(".employee-checkbox").prop('checked', false);
                }
            });
            $(document).on('click', '#printBtn', function() {
            /*keep reference*/
            let print_button = $(this);

            /* Show spinner & disable button*/
            print_button.html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...`
            );
            print_button.prop('disabled', true);

            /* Delay spinner for 1s*/
            setTimeout(() => {
                var selectedIds = [];
                $(".employee-checkbox:checked").each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    var url = "{{ route('admin.hr.employee.card.print', [ 'employee_ids' => ':employee_ids']) }}";
                    var employee_ids = selectedIds.join(',');
                    url = url.replace(':employee_ids', employee_ids);

                    window.open(url, '_blank');
                } else {
                    toastr.error("Please select at least one Employee.");
                }

                /* Restore button after task done*/
                print_button.html(`<i class="fas fa-print"></i> Generate ID Card`);
                /* Enable button*/
                print_button.prop('disabled', false);

            }, 1000);
        });
            var table = $("#datatable1").DataTable();
            /* General form submission handler*/
            function handleFormSubmit(modalId, form) {
                $(modalId + ' form').submit(function(e) {
                    e.preventDefault();
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalBtnText = submitBtn.html();
                    submitBtn.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                        );
                    submitBtn.prop('disabled', true);

                    var formData = new FormData(this);
                    $.ajax({
                        type: $(this).attr('method'),
                        url: $(this).attr('action'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                table.ajax.reload(null, false);
                                $(modalId).modal('hide');
                                form[0].reset();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(field, messages) {
                                    $.each(messages, function(index, message) {
                                        toastr.error(message);
                                    });
                                });
                            } else {
                                toastr.error('An error occurred. Please try again.');
                            }
                        },
                        complete: function() {
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                        }
                    });
                });
            }



            /* Handle Delete button click and form submission*/
            $('#datatable1 tbody').on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                $('#deleteModal').modal('show');
                $("input[name*='id']").val(id);
            });

            $('#deleteModal form').submit(function(e) {
                e.preventDefault();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalBtnText = submitBtn.html();
                submitBtn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    );
                var form = $(this);
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#deleteModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseText);
                    },
                    complete: function() {
                        submitBtn.html(originalBtnText);
                    }
                });
            });
        });
    </script>


@endsection
