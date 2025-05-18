@extends('Backend.Layout.App')
@section('title','Employee Leave Management | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
        <div class="card-header">
          <button type="button" data-toggle="modal" data-target="#addModal"  class="btn btn-success "><i class="mdi mdi-account-plus"></i>
           Create Employee Advance Salary</button>
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

                                <th>Advance Amount</th>
                                <th>Approve Date</th>
                                <th>Status</th>
                                <th>Create Date</th>
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
                        class="mdi mdi-account-check mdi-18px"></span> &nbsp;Add New Advance Salary </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.hr.employee.salary.advance.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label>Advance Amount</label>
                            <input name="amount" class="form-control" type="text" placeholder="Enter Advance Amount" required></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Description</label>
                            <textarea name="description" class="form-control" type="text" placeholder="Enter Description" required></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label>Advance Salary Date</label>
                            <input name="advance_date" class="form-control" type="date" required></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Status</label>
                            <select name="status" class="form-control" type="text" required>
                                <option >---Select---</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Approved Date</label>
                            <input name="approved_date" class="form-control" type="date"  required>
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
                        class="mdi mdi-account-check mdi-18px"></span> &nbsp;Update Advance Salary</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                </div>
                <div class="modal-body">
                <form action="{{route('admin.hr.employee.advance.advance_salary')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                         <div class="form-group mb-2">
                            <label>Employee Name</label>
                            <input type="hidden" name="id">
                            <select name="employee_id" class="form-select" type="text" style="width: 100%;" required>
                                <option >---Select---</option>
                                @foreach ($employee as $item)
                                    <option value="{{$item->id}}">{{$item->name}} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Advance Amount</label>
                            <input name="amount" class="form-control" type="text" placeholder="Enter Advance Amount" required></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Description</label>
                            <textarea name="description" class="form-control" type="text" placeholder="Enter Description" required></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label>Advance Salary Date</label>
                            <input name="advance_date" class="form-control" type="text" required></input>
                        </div>
                        <div class="form-group mb-2">
                            <label>Status</label>
                            <select name="status" class="form-control" type="text" required>
                                <option >---Select---</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Approved Date</label>
                            <input name="approved_date" class="form-control" type="date"  >
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
        <form action="{{route('admin.hr.employee.advance.delete')}}" method="post" enctype="multipart/form-data">
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
            url: "{{ route('admin.hr.employee.advance.salary.all_data') }}",
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
          "data": "amount",
        },


        {
          "data": "approved_date",
          "render": function(data, type, row) {
            if(data==null){
                return 'N/A';
            }else{
                return formatDate(data);
            }

          }
        },
        {
          "data": "status",
          "render":function(data,type,row){
            if(data=='Pending'){
                return '<span class="badge bg-warning">Pending</span>';
            }else if(data=='Approved'){
                  return '<span class="badge bg-success">Approved</span>';
            } else if(data=='Rejected'){
                return '<span class="badge bg-danger">Rejected</span>';
            }
          }
        },
         {
          "data": "created_at",
          "render": function(data, type, row) {
            return formatDate(data);
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
        var editUrl = '{{ route("admin.hr.employee.advance.get_advance_salary", ":id") }}';
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
                $('#editModal select[name="employee_id"]').val(response.data.employee_id).trigger('change');
                $('#editModal input[name="amount"]').val(response.data.amount);
                $('#editModal textarea[name="description"]').val(response.data.description);
                $('#editModal input[name="advance_date"]').val(response.data.advance_date).trigger('change');
                $('#editModal select[name="status"]').val(response.data.status).trigger('change');
                $('#editModal input[name="approved_date"]').val(response.data.approved_date);
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
