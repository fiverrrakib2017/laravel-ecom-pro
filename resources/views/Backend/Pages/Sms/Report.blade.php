@extends('Backend.Layout.App')
@section('title','Dashboard | SMS Report | Admin Panel')
@section('style')
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            <div class="card-body">
                <button data-toggle="modal" data-target="#addSendMessageModal" type="button" class=" btn btn-success mb-2"><i class="mdi mdi-account-plus"></i>
                    Send Message</button>

                <div class="table-responsive" id="tableStyle">
                    <table id="datatable1" class="table table-striped table-bordered    " cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>POP/Branch</th>
                                <th>Customer Name</th>
                                <th>Sent Time</th>
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
@endsection

@section('script')

  <script type="text/javascript">
    $(document).ready(function(){

    var table=$("#datatable1").DataTable({
    "processing":true,
    "responsive": true,
    "serverSide":true,
    beforeSend: function () {},
    complete: function(){},
    ajax: "{{ route('admin.sms.send_message_get_all_data') }}",
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
            "data":"customer.fullname",
          },
          {
            "data":"sent_at",
            "render": function(data, type, row) {
              return moment(data).format('lll');
            }
          },
          {
            "data":"message",
            "render": function(data, type, row) {
              return row.message.length > 50 ? row.message.substring(0, 50) + "..." : row.message;
            }
          },

          {
            data:null,
            render: function (data, type, row) {

              return `
              <button class="btn btn-danger btn-sm mr-3 delete-btn"  data-id="${row.id}"><i class="fa fa-trash"></i></button> `;
            }

          },
        ],
    order:[
        [0, "desc"]
    ],

    });

    });









  </script>
@endsection
