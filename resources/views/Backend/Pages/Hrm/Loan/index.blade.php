@extends('Backend.Layout.App')
@section('title','Employee Leave Management | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
        <div class="card-header">
          <a href="{{route('admin.hr.employee.loan.create')}}"  class="btn btn-success "><i class="mdi mdi-account-plus"></i>
           Apply Loan</a>
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

                                <th>Loan Amount</th>
                                <th>Installment Amount</th>
                                <th>Total Installment</th>
                                <th>Paid Installment</th>
                                <th>Issu Date</th>
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


<div id="deleteModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <form action="{{route('admin.hr.employee.loan.delete')}}" method="post" enctype="multipart/form-data">
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
            url: "{{ route('admin.hr.employee.loan.all_data') }}",
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
          "data": "loan_amount",
        },
        {
          "data": "installment_amount",
        },
        {
          "data": "total_installments",
        },
        {
          "data": "paid_installments",
        },


        {
          "data": "date_issued",
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
            if(data=='Approved'){
                  return '<span class="badge bg-success">Approved</span>';
            } else if(data=='Pending'){
                return '<span class="badge bg-danger">Pending</span>';
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
            let editUrl = `{{ route('admin.hr.employee.loan.edit', ':id') }}`.replace(':id', row.id);

            return `<button class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="${row.id}"><i class="fa fa-trash"></i></button>

            <a href="${editUrl}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>`;
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
