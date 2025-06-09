@extends('Backend.Layout.App')
@section('title','Dashboard | SMS Logs | Admin Panel')
@section('style')
@endsection
@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            <div class="card-body">

                <div class="table-responsive" id="tableStyle">
                    <table id="datatable1" class="table table-striped table-bordered    " cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>POP/Branch</th>
                                <th>Area</th>
                                <th>Customer Name</th>
                                <th>Package</th>
                                <th>Sent Time</th>
                                <th>message</th>
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
        ajax: "{{ route('admin.sms.get_all_sms_logs_data') }}",
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
                "data":"area.name",
            },
            {
                "data":"customer.fullname",
            },
            {
                "data":"customer.package.name",
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


            ],
        order:[
            [0, "desc"]
        ],

        });

    });
  </script>
@endsection
