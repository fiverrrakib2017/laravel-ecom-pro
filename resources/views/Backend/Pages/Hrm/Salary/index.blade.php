@extends('Backend.Layout.App')
@section('title','Employee Leave Management | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
        <div class="card-header">
          {{-- <button type="button" data-toggle="modal" data-target="#addModal"  class="btn btn-success "><i class="mdi mdi-account-plus"></i>
           Employee Prom</button> --}}
          </div>
            <div class="card-body">
                <div class="table-responsive" id="tableStyle">
                    <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Employee Name</th>
                                <th>Department Name</th>
                                <th>Designation Name</th>
                                <th>Basic Salary</th>
                                <th>House Allowance</th>
                                <th>Medical Allowance</th>
                                <th>Other Allowance</th>
                                <th>Tax</th>
                                <th>Net Salary</th>
                                <th>Effective From</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
 <!-- Add Modal -->
 <div class="modal fade bs-example-modal-lg" id="addModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span
                        class="mdi mdi-account-check mdi-18px"></span> &nbsp;Add New Leave</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.hr.employee.leave.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-2">
                            <label>Employee Name</label>
                            <select name="employee_id" class="form-select" type="text" style="width: 100%;" required>
                                <option >---Select---</option>
                                @foreach ($employee as $item)
                                    <option value="{{$item->id}}">{{$item->name}} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Leave Type</label>
                            <select name="leave_type" class="form-control" type="text" required>
                                <option value="">---Select---</option>
                                <option value="full_day">Full Day</option>
                                <option value="Sick">Sick</option>
                                <option value="half_day">Half Day</option>
                                <option value="Unpaid">Unpaid</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Leave Reason</label>
                            <textarea name="leave_reason" class="form-control" type="text" placeholder="Enter Leave Reason" required></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label>Leave Status</label>
                            <select name="leave_status" class="form-control" type="text" required>
                                <option >---Select---</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Start Date</label>
                            <input name="start_date" class="form-control" type="date"  required>
                        </div>
                        <div class="form-group mb-2">
                            <label>End Date</label>
                            <input name="end_date" class="form-control" type="date"  required>
                        </div>
                        <div class="modal-footer ">
                            <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
     <!-- Edit Modal -->
     <div class="modal fade bs-example-modal-lg" id="editModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span
                        class="mdi mdi-account-check mdi-18px"></span> &nbsp;Update Leave</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                </div>
                <div class="modal-body">
                <form action="{{route('admin.hr.employee.leave.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class="form-group mb-2">
                            <label>Employee Name</label>
                            <input type="text" class="d-none" name="id">
                            <select name="employee_id" class="form-control" type="text" style="width: 100%;" required>
                                @foreach ($employee as $item)
                                    <option value="{{$item->id}}">{{$item->name}} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Leave Type</label>
                            <select name="leave_type" class="form-control" type="text" required>
                                <option value="full_day">Full Day</option>
                                <option value="Sick">Sick</option>
                                <option value="half_day">Half Day</option>
                                <option value="Unpaid">Unpaid</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Leave Reason</label>
                            <textarea name="leave_reason" class="form-control" type="text" placeholder="Enter Leave Reason" required></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label>Leave Status</label>
                            <select name="leave_status" class="form-control" type="text" required>
                                <option >---Select---</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Start Date</label>
                            <input name="start_date" class="form-control" type="date"  required>
                        </div>
                        <div class="form-group mb-2">
                            <label>End Date</label>
                            <input name="end_date" class="form-control" type="date"  required>
                        </div>
                        <div class="modal-footer ">
                            <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<div id="deleteModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <form action="{{route('admin.hr.employee.leave.delete')}}" method="post" enctype="multipart/form-data">
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
<script>
    const basePhotoUrl = "{{ asset('uploads/photos') }}";
    const defaultAvatar = "{{ asset('Backend/images/avatar.png') }}";
</script>

<script type="text/javascript">
  $(document).ready(function(){
    var table = $("#datatable1").DataTable({
      "processing":true,
      "responsive": true,
      "serverSide":true,
      ajax: {
            url: "{{ route('admin.hr.employee.salary.all_data') }}",
            type: 'GET',
            data: function(d) {
              d.class_id = $('#search_class_id').val();
            },
            beforeSend: function(request) {
                request.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
            }
        },
      language: {
        searchPlaceholder: 'Search...',
        sSearch: '',
        lengthMenu: '_MENU_ items/page',
      },
      "columns":[
        {"data":"id"},

        {
        data: null,
            render: function(data, type, row, meta) {
                if (data.employee && data.employee.photo !== null) {
                return `<img src="${basePhotoUrl}/${data.employee.photo}" width="40" height="40" class="rounded-circle">`;
                } else {
                return `<img src="${defaultAvatar}" width="40" height="40" class="rounded-circle">`;
                }
            }
        },
        {"data":"employee.name"},
        {
          "data": "employee.department.name",
        },
        {
          "data": "employee.designation.name",
        },
        {
          "data": "basic_salary",
        },
        {
          "data": "house_allowance",
        },
        {
          "data": "medical_allowance",
        },
        {
          "data": "other_allowance",
        },
        {
          "data": "tax",
        },
        {
          "data": "net_salary",
        },
        {
          "data": "effective_from",
          "render": function(data, type, row) {
            return formatDate(data);
          }
        },
        {
          "data": "is_current",
          "render":function(data,type,row){
            if(data=='1'){
                    return '<span class="badge bg-success">New</span>';
                }
                else{
                     return '<span class="badge bg-primary">Old</span>';
                }
          }
        },
        {
          "data":null,
          render:function(data,type,row){
              return `
              <button type="button" class="btn btn-primary btn-sm" name="edit_button" data-id="${row.id}"><i class="fa fa-edit"></i></button>

              <button class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="${row.id}"><i class="fa fa-trash"></i></button>
            `;
          }
        },
      ],
      order:[ [0, "desc"] ],
    });

    /* Search filter reload*/
    $('#search_class_id').change(function() {
        table.ajax.reload(null, false);
    });
    function formatDate(dateString) {
        if (dateString) {
            const dateObj = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            const formattedDate = dateObj.toLocaleDateString('en-US', options);
            const [day, month, year] = formattedDate.split(' ');

            return `${day} ${month} ${year}`;
        }
        return '';
    }
    /* Initialize select2 for modal dropdowns*/
    function initializeSelect2(modalId) {
      $(modalId).on('show.bs.modal', function (event) {
        if (!$("select[name='employee_id']").hasClass("select2-hidden-accessible")) {
            $("select[name='employee_id']").select2({
                dropdownParent: $(modalId),
                placeholder: "Select Student"
            });
        }
      });
    }

    /* Initialize select2 modals*/
     initializeSelect2("#addModal");
     initializeSelect2("#editModal");

    /* General form submission handler*/
    function handleFormSubmit(modalId, form) {
        $(modalId + ' form').submit(function(e){
            e.preventDefault();
            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            submitBtn.prop('disabled', true);

            var formData = new FormData(this);
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
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

    /* Handle Add and Edit Form */
    handleFormSubmit("#addModal", $('#addModal form'));
    handleFormSubmit("#editModal", $('#editModal form'));

    /* Edit button click handler*/
    $(document).on("click", "button[name='edit_button']", function() {
        var _id = $(this).data("id");
        var editUrl = '{{ route("admin.hr.employee.leave.get_leave", ":id") }}';
        var url = editUrl.replace(':id', _id);
        $.ajax({
          url: url,
          type: "GET",
          dataType: 'json',
          success: function(response) {
              if (response.success) {
                //var data = response.data;
                $('#editModal').modal('show');
                $('#editModal input[name="id"]').val(response.data.id);
                $('#editModal select[name="employee_id"]').val(response.data.employee_id);
                $('#editModal select[name="leave_type"]').val(response.data.leave_type);
                $('#editModal textarea[name="leave_reason"]').val(response.data.leave_reason);
                $('#editModal select[name="leave_status"]').val(response.data.leave_status).trigger('change');
                $('#editModal input[name="start_date"]').val(response.data.start_date);
                $('#editModal input[name="end_date"]').val(response.data.end_date);
              } else {
                  toastr.error("Error fetching data for edit: " + response.message);
              }
          },
          error: function(xhr) {
              toastr.error('Failed to fetch bill collection details.');
          }
        });
    });

    /* Handle Delete button click and form submission*/
    $('#datatable1 tbody').on('click', '.delete-btn', function () {
        var id = $(this).data('id');
        $('#deleteModal').modal('show');
        $("input[name*='id']").val(id);
    });

    $('#deleteModal form').submit(function(e){
        e.preventDefault();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
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
