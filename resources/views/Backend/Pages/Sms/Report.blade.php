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
    const from_date = `
            <div class="form-group mb-0 mr-2">
                <label for="from_date" class="sr-only">From</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="date" id="from_date" class="form-control from_date" placeholder="From Date">
                </div>
            </div>`;

        const to_date = `
            <div class="form-group mb-0 mr-2">
                <label for="to_date" class="sr-only">To</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="date" id="to_date" class="form-control to_date" placeholder="To Date">
                </div>
            </div>`;

        setTimeout(() => {
            const filters_wrapper = `
                <div class="row align-items-center mb-3">
                    <!-- Left: Per Page -->
                    <div class="col-12 col-md-auto dataTables_length_container mb-2 mb-md-0"></div>

                    <!-- Middle: Date Filters -->
                    <div class="col-12 col-md-auto d-flex flex-wrap align-items-center mb-2 mb-md-0">
                        ${from_date + to_date}
                    </div>

                    <!-- Right: Search -->
                    <div class="col-12 col-md dataTables_filter_container d-flex justify-content-md-end mb-2 mb-md-0"></div>
                </div>
            `;

            const tableWrapper = $('#datatable1').closest('.dataTables_wrapper');
            tableWrapper.prepend(filters_wrapper);

            tableWrapper.find('.dataTables_length').appendTo(tableWrapper.find('.dataTables_length_container'));

            tableWrapper.find('.dataTables_filter').appendTo(tableWrapper.find('.dataTables_filter_container'));
        }, 300);

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
                    d.from_date = $('.from_date').val();
                    d.to_date = $('.to_date').val();
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
         /* Filter Change Event*/
        $(document).on('change','.from_date, .to_date',function(){
            $('#datatable1').DataTable().ajax.reload();
        });

    });
  </script>
@endsection
