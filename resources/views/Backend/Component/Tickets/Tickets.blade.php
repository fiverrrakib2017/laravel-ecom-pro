@php
   //$branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
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
<table id="tickets_datatable1" class="table table-bordered dt-responsive nowrap"
style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th>No.</th>
            <th>Status</th>
            <th>Created</th>
            <th>Priority</th>
            <th>Customer Name</th>
            <th>Phone Number</th>
            <th>POP/Branch</th>
            <th>Area Name</th>
            <th>Issues</th>
            <th>Assigned To</th>
            <th>Ticket For</th>
            <th>Acctual Work</th>
            <th>Percentage</th>
            <th>Note</th>
            <th></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
<script  src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>

<script>

    $(document).ready(function() {
        var customer_id = @json($customer_id ?? '');
        var pop_id_for_ticket = @json($pop_id ?? '');
        var area_id_for_ticket = @json($area_id ?? '');
        var status_for_ticket = @json($status ?? '');

        if (status_for_ticket == null || status_for_ticket === '') {
            const tickets_urlParams = new URLSearchParams(window.location.search);
            const tickets_urlStatus = tickets_urlParams.get('status');
            // console.log('Before logic - status:', status);
            // console.log('From URL - urlStatus:', urlStatus);

            if (tickets_urlStatus === 'completed') {
                status_for_ticket = '1';
            }
            if(tickets_urlStatus === 'pending') {
                status_for_ticket = '0';
            }

        }
        /*When Request Get Area Page*/
       var  filter_dropdown = @json($filter_dropdown ?? true);
        // if (area_page) {
        //     pop_id = @json($pop_id ?? '');
        //     area_id = @json($area_id ?? '');
        //     status = @json($status ?? '');
        // }
        /*When Request Get POP/Branch Page*/

        /* GET POP-Branch */
        var tickets_pop_branches = @json($pop_branches);
        var tickets_pop_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select id="search_ticket_pop_id" name="search_ticket_pop_id" class="form-control form-control-sm select2">
                    <option value="">--Select POP/Branch--</option>`;
        tickets_pop_branches.forEach(function(item) {
            tickets_pop_filter += `<option value="${item.id}">${item.name}</option>`;
        });
        tickets_pop_filter += `</select></div>`;

        /* Get Areas */
        var tickets_areas = @json($areas);
        var tickets_area_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select id="search_ticket_area_id" name="search_ticket_area_id" class="form-control form-control-sm select2">
                    <option value="">--Select Area--</option>`;
        tickets_areas.forEach(function(item) {
            tickets_area_filter += `<option value="${item.id}">${item.name}</option>`;
        });
        tickets_area_filter += `</select></div>`;

        /* Status Filter */
        var ticekts_status_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select class="tickets_status_filter form-control form-control-sm select2">
                    <option value="">--Status--</option>
                    <option value="1">Completed</option>
                    <option value="0">Pending</option>

                </select>
            </div>`;

        setTimeout(() => {
            var tickets_filters_wrapper = `
                <div class="row no-gutters mb-0  " style=" row-gap: 0.5rem;">
                    <!-- Left: Per Page -->
                    <div class="col-12 col-md-auto dataTables_length_container d-flex align-items-center mb-2 mb-md-0 pr-md-3"></div>

                    <!-- Middle: Filters -->
                    <div class="col-12 col-md d-flex flex-wrap align-items-center mb-2 mb-md-0" style="gap: 0.5rem;">
                        ${tickets_pop_filter + tickets_area_filter + ticekts_status_filter}
                    </div>

                    <!-- Right: Search Input -->
                    <div class="col-12 col-md-auto dataTables_filter_container d-flex justify-content-md-end"></div>
                </div>
            `;
            /* Append the filters to the DataTable wrapper */
            if(filter_dropdown==true){
                var tableWrapper = $('#tickets_datatable1').closest('.dataTables_wrapper');
                tableWrapper.prepend(tickets_filters_wrapper);

                tableWrapper.find('.dataTables_length').appendTo(tableWrapper.find('.dataTables_length_container'));
                tableWrapper.find('.dataTables_filter').appendTo(tableWrapper.find('.dataTables_filter_container'));
            }



            $('#search_ticket_pop_id').select2({ width: 'resolve' });
            $('#search_ticket_area_id').select2({ width: 'resolve' });
            $('.tickets_status_filter').select2({ width: 'resolve' });
        }, 1000);




        /*Check Param Values if else */
        if (!pop_id_for_ticket) {
            pop_id_for_ticket = $('#search_ticket_pop_id').val();
        }
        if (!area_id_for_ticket) {
            area_id_for_ticket = $('#search_ticket_area_id').val();
        }
        if (status_for_ticket == null || status_for_ticket == '') {
            status_for_ticket = $('.tickets_status_filter').val();
        }
        $(document).on('change','select[name="search_ticket_pop_id"]',function(){
            var areas = @json($areas);
            var selectedPopId = $(this).val();
            var tickets_filteredAreas = areas.filter(function(item) {
                return item.pop_id == selectedPopId;
            });
            var tickets_areas_Options = '<option value="">--Select Area--</option>';
            tickets_filteredAreas.forEach(function(item) {
                tickets_areas_Options += '<option value="' + item.id + '">' + item.name + '</option>';
            });
            $('#tickets_datatable1').DataTable().ajax.reload(null, false);
            $('select[name="search_ticket_area_id"]').html(tickets_areas_Options);
        });
        /*Handle Area filter change*/
        $(document).on('change', 'select[name="search_ticket_area_id"]', function() {
            $('#tickets_datatable1').DataTable().ajax.reload(null, false);
        });
        /*Handle Status filter change*/
        $(document).on('change', '.tickets_status_filter', function() {
            $('#tickets_datatable1').DataTable().ajax.reload(null, false);
        });

        var table = $("#tickets_datatable1").DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            beforeSend: function() {
                $(".dataTables_empty").html(
                    '<img src="http://103.146.16.154/assets/images/loading.gif" style="background-color: transparent"/>'
                    );
            },
            complete: function() {},
           "ajax": {
                url: "{{ route('admin.tickets.get_all_data') }}",
                type: "GET",
                data: function(d) {
                    d.customer_id = customer_id;

                    d.pop_id        = $('#search_ticket_pop_id').val() || pop_id_for_ticket;
                    d.area_id       = $('#search_ticket_area_id').val() || area_id_for_ticket;
                    d.status        = $('.tickets_status_filter').val() || status_for_ticket;
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
            "columns": [{
                    "data": "id"
                },
                {
                    "data": "status",
                    render: function(data, type, row) {
                        if (row.status == 0) {
                            return '<span class="badge bg-danger">Active</span>';
                        } else if (row.status == 2) {
                            return '<span class="badge bg-warning">Pending</span>';
                        } else if (row.status == 1) {
                            return '<span class="badge bg-success">Completed</span>';
                        }
                    }
                },
                {
                    "data": "created_at",
                    render: function(data, type, row) {
                        return moment(row.created_at).format('D MMMM YYYY');
                    }
                },
                {
                    "data": "priority_id",
                    "render": function(data, type, row) {
                        let priorityLabel = '';
                        let badgeColor = '';

                        switch (row.priority_id) {
                            case 1:
                                priorityLabel = 'Low';
                                badgeColor = 'badge-secondary';
                                break;
                            case 2:
                                priorityLabel = 'Normal';
                                badgeColor = 'badge-info';
                                break;
                            case 3:
                                priorityLabel = 'Standard';
                                badgeColor = 'badge-primary';
                                break;
                            case 4:
                                priorityLabel = 'Medium';
                                badgeColor = 'badge-warning';
                                break;
                            case 5:
                                priorityLabel = 'High';
                                badgeColor = 'badge-danger';
                                break;
                            case 6:
                                priorityLabel = 'Very High';
                                badgeColor = 'badge-dark';
                                break;
                            default:
                                priorityLabel = 'Unknown';
                                badgeColor = 'badge-light';
                                break;
                        }

                        return `<span class="badge ${badgeColor}" style="font-size: 80%;">${priorityLabel}</span>`;
                    }
                },

                {
                    "data": "customer.fullname",
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
                            '<span style="font-size: 16px; font-weight: bold;">' + row
                            .customer.fullname + '</span>' +
                            '</a>';
                    }
                },
                {
                    "data": "customer.phone",
                    "render": function(data, type, row) {
                        return '<i class="fas fa-phone-alt" style="color: #007bff; margin-right: 6px;"></i>' +
                            '<span>' + row.customer.phone + '</span>';
                    }
                },

                {
                    "data": "pop.name",
                    "render": function(data, type, row) {
                        return '<i class="fas fa-broadcast-tower" style="color: #28a745; margin-right: 6px;"></i>' +
                            '<span>' + row.pop.name + '</span>';
                    }
                },

                {
                    "data": "area.name",
                    "render": function(data, type, row) {
                        return '<i class="fas fa-map-marker-alt" style="color: #dc3545; margin-right: 6px;"></i>' +
                            '<span>' + row.area.name + '</span>';
                    }
                },

                {
                    "data": "complain_type.name"
                },
                {
                    "data": "assign.name"
                },
                {
                    "data": "ticket_for",
                    render: function(data, type, row) {
                        if (row.ticket_for == 1) {
                            return `Default`;
                        }
                    }
                },
                {
                    "data": null,
                    render: function(data, type, row) {
                        if (row.updated_at == row.created_at) {
                            return 'N/A';
                        }
                        if (row.updated_at && row.created_at) {
                            let start = moment(row.created_at);
                            let end = moment(row.updated_at);

                            return end.from(start);
                        } else {
                            return 'N/A';
                        }
                    }
                },
                {
                    "data": "percentage"
                },
                {
                    "data": "note"
                },

                {
                    data: null,
                    render: function(data, type, row) {
                        if(data.status == 1){
                            return `
                            <button class="btn btn-primary btn-sm mr-3 tickets_edit_btn" data-id="${row.id}"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm mr-3 tickets_delete_btn"  data-id="${row.id}"><i class="fa fa-trash"></i></button>
                            <button class="btn btn-success btn-sm mr-3 tickets_view_btn"  data-id="${row.id}"><i class="fa fa-eye"></i></button>
                            `;
                        }else{
                            return `
                            <button  class="btn btn-primary btn-sm mr-3 tickets_edit_btn" data-id="${row.id}"><i class="fa fa-edit"></i></button>

                            <button class="btn btn-danger btn-sm mr-3 tickets_delete_btn"  data-id="${row.id}"><i class="fa fa-trash"></i></button>

                            <button class=" btn btn-info btn-sm mr-3 tickets_completed_btn" data-id="${row.id}"> <i class="fas fa-check-circle"></i> </button>

                            `;
                        }

                    }

                },
            ],
            order: [
                [0, "desc"]
            ],

        });

         /** Handle Completed button click**/
        $(document).on("click", ".tickets_completed_btn", function() {
            let id = $(this).data("id");
            let btn = $(this);
            let originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop("disabled", true);
            $.ajax({
                url: "{{ route('admin.tickets.change_status', '') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        btn.html(originalHtml).prop("disabled", false);
                        toastr.success(response.message);
                        $('#datatable1').DataTable().ajax.reload(null, false);
                    } else if (response.success == false) {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error("Something went wrong!");
                },
                complete: function() {
                    btn.prop("disabled", false);
                }
            });
        });
        /** Handle ticket view button click**/

        $(document).on("click", ".tickets_view_btn", function() {
             let id = $(this).data("id");

        });

    });
</script>
