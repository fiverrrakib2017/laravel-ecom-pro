@extends('Backend.Layout.App')
@section('title','Dashboard | SMS Report | Admin Panel')
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
@php
    if(!empty($branch_user_id) && $branch_user_id > 0){
        $pop_branches = \App\Models\Pop_branch::where('id',$branch_user_id)->first();
        $areas=\App\Models\Pop_area::where('status','active')->where('pop_id',$branch_user_id)->get();
    }else{
        $pop_branches=\App\Models\Pop_branch::where('status',1)->get();
        $areas=\App\Models\Pop_area::where('status','active')->get();
    }

    /*GET Request POP/Branch View Table*/
    if(!empty($pop_id) && $pop_id > 0 && isset($pop_id)){
        $pop_branches = \App\Models\Pop_branch::where('id',$pop_id)->get();
        $areas = \App\Models\Pop_area::where('status','active')->where('pop_id',$pop_id)->get();
    }else{
        $pop_branches = \App\Models\Pop_branch::where('status',1)->get();
        $areas = \App\Models\Pop_area::where('status','active')->get();
    }


@endphp
@section('script')
  <script type="text/javascript">
    $(document).ready(function(){
/* GET POP-Branch */
        var pop_branches = @json($pop_branches);
        var pop_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select id="search_pop_id" name="search_pop_id" class="form-control form-control-sm select2">
                    <option value="">--Select POP/Branch--</option>`;
        pop_branches.forEach(function(item) {
            pop_filter += `<option value="${item.id}">${item.name}</option>`;
        });
        pop_filter += `</select></div>`;

        /* Get Areas */
        var areas = @json($areas);
        var area_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select id="search_area_id" name="search_area_id" class="form-control form-control-sm select2">
                    <option value="">--Select Area--</option>`;
        areas.forEach(function(item) {
            area_filter += `<option value="${item.id}">${item.name}</option>`;
        });
        area_filter += `</select></div>`;

        setTimeout(() => {
            var filters_wrapper = `
                <div class="row no-gutters mb-0  " style=" row-gap: 0.5rem;">
                    <!-- Left: Per Page -->
                    <div class="col-12 col-md-auto dataTables_length_container d-flex align-items-center mb-2 mb-md-0 pr-md-3"></div>

                    <!-- Middle: Filters -->
                    <div class="col-12 col-md d-flex flex-wrap align-items-center mb-2 mb-md-0" style="gap: 0.5rem;">
                        ${pop_filter + area_filter }
                    </div>

                    <!-- Right: Search Input -->
                    <div class="col-12 col-md-auto dataTables_filter_container d-flex justify-content-md-end"></div>
                </div>
            `;
            /* Append the filters to the DataTable wrapper */
            var tableWrapper = $('#datatable1').closest('.dataTables_wrapper');
            tableWrapper.prepend(filters_wrapper);

            tableWrapper.find('.dataTables_length').appendTo(tableWrapper.find('.dataTables_length_container'));
            tableWrapper.find('.dataTables_filter').appendTo(tableWrapper.find('.dataTables_filter_container'));




            $('#search_pop_id').select2({ width: 'resolve' });
            $('#search_area_id').select2({ width: 'resolve' });
            $('.status_filter').select2({ width: 'resolve' });
        }, 1000);
        $(document).on('change','select[name="search_pop_id"]',function(){
            var areas = @json($areas);
            var selectedPopId = $(this).val();
            var filteredAreas = areas.filter(function(item) {
                return item.pop_id == selectedPopId;
            });
            var areasOptions = '<option value="">--Select Area--</option>';
            filteredAreas.forEach(function(item) {
                areasOptions += '<option value="' + item.id + '">' + item.name + '</option>';
            });
            $('#datatable1').DataTable().ajax.reload(null, false);
            $('select[name="search_area_id"]').html(areasOptions);
        });
        /*Handle Area filter change*/
        $(document).on('change', 'select[name="search_area_id"]', function() {
            $('#datatable1').DataTable().ajax.reload(null, false);
        });
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


