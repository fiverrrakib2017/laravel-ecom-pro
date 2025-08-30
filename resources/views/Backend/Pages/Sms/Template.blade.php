@extends('Backend.Layout.App')
@section('title','Dashboard | SMS Template | Admin Panel')
@section('style')
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            <div class="card-body">
                <button data-toggle="modal" data-target="#addSmsTemplateModal" type="button" class=" btn btn-success mb-2"><i class="mdi mdi-account-plus"></i>
                    Add New Template</button>

                <div class="table-responsive" id="tableStyle">
                    <table id="datatable1" class="table table-striped table-bordered    " cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>POP/Branch</th>
                                <th>Name</th>
                                <th>message</th>
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
@include('Backend.Modal.Sms.Template_modal')
@include('Backend.Modal.delete_modal')


@endsection

@section('script')
<script  src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
<script  src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>

  <script type="text/javascript">
    $(document).ready(function(){
    handleSubmit('#SmsTemplateForm','#addSmsTemplateModal');
    var table=$("#datatable1").DataTable({
    "processing":true,
    "responsive": true,
    "serverSide":true,
    beforeSend: function () {},
    complete: function(){},
    ajax: "{{ route('admin.sms.template_get_all_data') }}",
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
            "data":"pop.name",
          },
          {
            "data":"name",
          },
          {
            data: "message",
                render: function (data, type, row) {
                    const full = row.message || '';
                    const short = full.length > 50 ? full.substring(0, 50) + '…' : full;

                    const esc = $('<div/>').text(full).html()
                    .replace(/'/g, '&#39;').replace(/"/g, '&quot;');

                    return `<span title='${esc}'>${short}</span>`;
                }
            },


          {
            data:null,
            render: function (data, type, row) {

              return `
              <button class="btn btn-success btn-sm mr-3 edit-btn"  data-id="${row.id}"><i class="fa fa-edit"></i></button>
              <button class="btn btn-danger btn-sm mr-3 delete-btn"  data-id="${row.id}"><i class="fa fa-trash"></i></button> `;
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
        $.ajax({
            url: "{{ route('admin.tickets.assign.edit', ':id') }}".replace(':id', id),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#assignForm').attr('action', "{{ route('admin.tickets.assign.update', ':id') }}".replace(':id', id));
                    $('#assignModalLabel').html('<span class="mdi mdi-account-edit mdi-18px"></span> &nbsp;Edit Assign To');
                    $('#assignForm input[name="name"]').val(response.data.name);
                    $('#assignForm select[name="pop_id"]').val(response.data.pop_id).trigger('change');

                    // Show the modal
                    $('#assignModal').modal('show');
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
        var deleteUrl = "{{ route('admin.sms.template_delete', ':id') }}".replace(':id', id);

        $('#deleteForm').attr('action', deleteUrl);
        $('#deleteModal').find('input[name="id"]').val(id);
        $('#deleteModal').modal('show');
    });





  </script>
@endsection
