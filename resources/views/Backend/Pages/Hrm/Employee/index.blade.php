@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')

@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('admin.hr.employee.create') }}" class="btn btn-success "><i class="fas fa-users"></i>
                        Add New Employee</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
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
                                                <a href="{{ route('admin.hr.employee.edit', $employee->id) }}"
                                                    class="btn btn-sm btn-primary ml-1">Edit</a>
                                                <a href="{{ route('admin.hr.employee.edit', $employee->id) }}"
                                                    class="btn btn-sm btn-info ml-1">View</a>
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
                            table.ajax.reload(null, false);
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
