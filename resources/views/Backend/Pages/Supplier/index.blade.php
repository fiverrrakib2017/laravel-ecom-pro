@extends('Backend.Layout.App')
@section('title','Dashboard | Admin Panel')
@section('style')
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            <div class="card-body">
                <button data-toggle="modal" data-target="#supplierModal" type="button" class="btn-sm btn btn-success mb-2"><i class="mdi mdi-account-plus"></i>
                    Add New Supplier</button>

                <div class="table-responsive" id="tableStyle">
                    <table id="datatable1" class="table table-striped table-bordered    " cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Company</th>
                                <th>Mobile</th>
                                <th>Email</th>
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
@include('Backend.Modal.supplier_modal')
@include('Backend.Modal.delete_modal')


@endsection

@section('script')
<script  src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
<script  src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>

  <script type="text/javascript">
    $(document).ready(function(){
    handleSubmit('#supplierForm','#supplierModal');
    var table=$("#datatable1").DataTable({
    "processing":true,
    "responsive": true,
    "serverSide":true,
    beforeSend: function () {},
    complete: function(){},
    ajax: "{{ route('admin.supplier.get_all_data') }}",
    language: {
        searchPlaceholder: 'Search...',
        sSearch: '',
        lengthMenu: '_MENU_ items/page',
    },
    "columns":[
          {
            "data":"id"
          },
          {
            "data":"fullname",
            render:function(data,type,row){
              var link ="{{ route('admin.supplier.view', ':id') }}".replace(':id', row.id);
              return '<a href="'+link+'">'+row.fullname+'</a>';
            }
          },
          {
            "data":"company_name"
          },
          {
            "data":"phone_number"
          },
          {
            "data":"email_address"
          },

          {
            data:null,
            render: function (data, type, row) {

              var viewUrl = "{{ route('admin.supplier.view', ':id') }}".replace(':id', row.id);


              return `<button  class="btn btn-primary btn-sm mr-3 edit-btn" data-id="${row.id}"><i class="fa fa-edit"></i></button>

              <button class="btn btn-danger btn-sm mr-3 delete-btn"  data-id="${row.id}"><i class="fa fa-trash"></i></button>

              <a href="${viewUrl}" class="btn btn-success btn-sm mr-3 edit-btn"><i class="fa fa-eye"></i></a>


              `;
            }

          },
        ],
    order:[
        [0, "desc"]
    ],

    });

    });








    /** Handle Edit button click **/
    $('#datatable1 tbody').on('click', '.edit-btn', function () {
        var id = $(this).data('id');

        // AJAX call to fetch supplier data
        $.ajax({
            url: "{{ route('admin.supplier.edit', ':id') }}".replace(':id', id),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#supplierForm').attr('action', "{{ route('admin.supplier.update', ':id') }}".replace(':id', id));
                    $('#supplierModalLabel').html('<span class="mdi mdi-account-edit mdi-18px"></span> &nbsp;Edit Supplier');
                    $('#supplierForm input[name="fullname"]').val(response.data.fullname);
                    $('#supplierForm input[name="company"]').val(response.data.company_name);
                    $('#supplierForm input[name="phone_number"]').val(response.data.phone_number);
                    $('#supplierForm input[name="email"]').val(response.data.email_address);
                    $('#supplierForm input[name="address"]').val(response.data.address);
                    $('#supplierForm select[name="status"]').val(response.data.status);

                    // Show the modal
                    $('#supplierModal').modal('show');
                } else {
                    toastr.error('Failed to fetch Supplier data.');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    /** Handle Delete button click**/
    $('#datatable1 tbody').on('click', '.delete-btn', function () {
        var id = $(this).data('id');
        var deleteUrl = "{{ route('admin.supplier.delete', ':id') }}".replace(':id', id);

        $('#deleteForm').attr('action', deleteUrl);
        $('#deleteModal').find('input[name="id"]').val(id);
        $('#deleteModal').modal('show');
    });





  </script>
@endsection
