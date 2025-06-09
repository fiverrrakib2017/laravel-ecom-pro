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
                    <table id="datatable1" class="table table-striped table-bordered" cellspacing="0" width="100%">
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
            "ajax": {
                url: "{{ route('admin.sms.get_all_sms_logs_data') }}",
                type: "GET",
                data: function(d) {
                    d.pop_id        = $('#search_pop_id').val() ;
                    d.area_id       = $('#search_area_id').val() ;
                }
            },
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_ items/page',
                 processing: `<div class="spinner-grow text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                            </div>
                            <div class="spinner-grow text-secondary" role="status">
                            <span class="sr-only">Loading...</span>
                            </div>
                            <div class="spinner-grow text-success" role="status">
                            <span class="sr-only">Loading...</span>
                            </div>
                            <div class="spinner-grow text-danger" role="status">
                            <span class="sr-only">Loading...</span>
                            </div>`,
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
                "render": function(data, type, row) {
                        var viewUrl = '{{ route('admin.customer.view', ':id') }}'.replace(':id',
                            row.customer.id);
                        /*Set the icon based on the status*/
                        var icon = '';
                        var color = '';

                        if (row.customer.status === 'online') {
                            icon =
                                '<i class="fas fa-unlock" style="font-size: 15px; color: green; margin-right: 8px;"></i>';
                        } else if (row.customer.status === 'offline') {
                            icon =
                                '<i class="fas fa-lock" style="font-size: 15px; color: red; margin-right: 8px;"></i>';
                        } else {
                            icon =
                                '<i class="fa fa-question-circle" style="font-size: 18px; color: gray; margin-right: 8px;"></i>';
                        }

                        return '<a href="' + viewUrl +
                            '" style="display: flex; align-items: center; text-decoration: none; color: #333;">' +
                            icon +
                            '<span style="font-size: 16px; font-weight: bold;">' + row.customer
                            .fullname + '</span>' +
                            '</a>';
                    }
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
