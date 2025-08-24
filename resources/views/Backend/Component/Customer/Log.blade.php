<table id="datatable1" class="table table-bordered dt-responsive nowrap"
    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Customer Username</th>
            <th>Ip Address</th>
            <th>Operation By</th>
            <th>Type</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<style>
    .dataTables_filter {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
        color: #333;
    }

    .dataTables_filter input,
    .dataTables_filter select {
        height: 35px;
        border-radius: 5px;
        border: 1px solid #ddd;
        padding: 5px;
    }

    .select2-container--default .select2-selection--single {
        height: 35px !important;
        line-height: 35px !important;
        border-radius: 5px;
    }
</style>
<script src="{{ asset('Backend/assets/js/render_customer_column.js') }}"></script>
<script type="text/javascript">
 var baseUrl = "{{ url('/') }}";
 let branch_user_id = @json(Auth::guard('admin')->user()->pop_id ?? null);
    $(document).ready(function() {
        /* From Date */
        var from_date = `<label>
                         <span>From:</span>
                         <input class="from_date form-control" type="date" value="">
                     </label>`;

        /* To Date */
        var to_date = `<label>
                         <span>To:</span>
                         <input class="to_date form-control" type="date" value="">
                     </label>`;
        var _type_filter = `
            <div class="form-group " style='margin-top:4px;'>
                <select class="_type_filter form-control">
                        <option value="">All</option>
                        <option value="recharge">Recharge</option>
                        <option value="add">Add</option>
                        <option value="edit">Edit</option>
                        <option value="package_change">Package Change</option>
                        <option value="suspend">Suspend</option>
                        <option value="reconnect">Reconnect</option>
                        <option value="delete">Delete</option>
                </select>
            </div>`;

        setTimeout(() => {
            let filterContainer = $('.dataTables_filter');
            let lengthContainer = $('.dataTables_length');

            lengthContainer.parent().removeClass('col-sm-12 col-md-6');
            filterContainer.parent().removeClass('col-sm-12 col-md-6');

            filterContainer.append(from_date);
            filterContainer.append(to_date);
            filterContainer.append(_type_filter);


            //$('._type_filter').select2({ width: 'resolve' });
        }, 500);

        var table = $("#datatable1").DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            ajax: {
                url: "{{ route('admin.customer.log.get_all_data') }}",
                data: function(d) {
                    d.start = d.start || 0;
                    d.length = d.length || 10;
                    d.from_date = $('.from_date').val();
                    d.to_date = $('.to_date').val();
                    d.action_type = $('._type_filter').val();
                    d.pop_id = branch_user_id;
                },
            },
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_ items/page',
            },
            "columns": [{
                    "data": "id"
                },
                {
                    "data": "created_at",
                    "render": function(data, type, row) {
                        var date = new Date(data);

                        var dateOptions = {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit'
                        };
                        var formattedDate = date.toLocaleDateString('en-GB', dateOptions);

                        var timeOptions = {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        };
                        var formattedTime = date.toLocaleTimeString('en-GB', timeOptions);

                        return formattedDate + "<br><span class='text-success'>" + formattedTime + "</span>";
                    }
                },

                {
                    "data": "customer.fullname",
                    "render": render_customer_column
                },
                {
                    "data": "ip_address"
                },
                {
                    "data": "user.name"
                },
                {
                    "data": "action_type",
                    "render": function(data, type, row) {
                        if (data == 'add') {
                            return '<span class="badge bg-success">Add</span>';
                        } else if (data == 'edit') {
                            return '<span class="badge bg-danger">Edit</span>';
                        } else if (data == 'package_change') {
                            return '<span class="badge bg-success">Package Change</span>';
                        } else if (data == 'reconnect') {
                            return '<span class="badge bg-success">Reconnect</span>';
                        } else if (data == 'recharge') {
                            return '<span class="badge bg-success">Recharge</span>';
                        } else if (data == 'delete') {
                            return '<span class="badge bg-danger">Deleted</span>';
                        } else {
                            return '<span class="badge bg-danger">N/A</span>';
                        }
                    }
                },
                {
                    "data": "description",
                },

            ],
            order: [
                [0, "desc"]
            ],
        });
        /* Filter Change Event*/
        $(document).on('change', '.from_date, .to_date', function() {
            $('#datatable1').DataTable().ajax.reload();
        });
        $(document).on('change','._type_filter',function(){
           $('#datatable1').DataTable().ajax.reload();
        });
    });
</script>
