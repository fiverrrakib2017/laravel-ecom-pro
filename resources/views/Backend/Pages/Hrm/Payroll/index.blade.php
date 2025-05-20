@extends('Backend.Layout.App')
@section('title','Employee Leave Management | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
        <div class="card-header">
          <a href="{{route('admin.hr.employee.payroll.create')}}"  class="btn btn-success "><i class="mdi mdi-account-plus"></i>
           Create Employee Payroll</a>
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

                                <th>Month</th>
                                <th>Basic Salary</th>
                                <th>Advance Salary</th>
                                <th>Loan Deduction</th>
                                <th>Tax</th>
                                <th>Net Salary</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
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
            url: "{{ route('admin.hr.employee.payroll.all_data') }}",
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
          "data": "month_year",
        },
        {
          "data": "basic_salary",
        },
        {
          "data": "advance_salary",
        },
        {
          "data": "loan_deduction",
        },
        {
          "data": "tax",
        },
        {
          "data": "net_salary",
        },


        {
          "data": "payment_date",
          "render": function(data, type, row) {
            if(data==null){
                return 'N/A';
            }else{
                return formatDate(data);
            }

          }
        },
        {
          "data": "payment_method",
        },
        {
          "data": "status",
          "render":function(data,type,row){
            if(data=='Paid'){
                  return '<span class="badge bg-success">Paid</span>';
            } else if(data=='Unpaid'){
                return '<span class="badge bg-danger">Unpaid</span>';
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
