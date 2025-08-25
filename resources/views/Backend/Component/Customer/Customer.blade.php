@php
   $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
    if(!empty($branch_user_id) && $branch_user_id > 0){
        $pop_branches = \App\Models\Pop_branch::where('id',$branch_user_id)->get();
        $areas=\App\Models\Pop_area::where('status','active')->where('pop_id',$branch_user_id)->get();
    }else{
        $pop_branches=\App\Models\Pop_branch::where('status',1)->get();
        $areas=\App\Models\Pop_area::where('status','active')->get();
    }

    /*GET Request POP/Branch View Table*/
    if(!empty($pop_id) && $pop_id > 0 && isset($pop_id)){
        $pop_branches = \App\Models\Pop_branch::where('id',$pop_id)->get();
        $areas = \App\Models\Pop_area::where('status','active')->where('pop_id',$pop_id)->get();
    }


@endphp
    <div class="col-6 nav justify-content-end" id="export_buttonscc"></div>
        <table id="customer_datatable1" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
            <tr>
                {{-- <th>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customer_select_all">
                        <label class="custom-control-label" for="customer_select_all"></label>
                    </div>
                </th> --}}

                <th>ID</th>
                <th>Name</th>
                <th>Package</th>
                <th>Amount</th>

                <th>Expired Date</th>
                <th>User Name</th>
                <th>Mobile no.</th>
                <th>POP/Branch</th>
                <th>Area/Location</th>
                <th>Create Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>



<div id="deleteModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <form method="post" enctype="multipart/form-data" id="deleteForm">
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
<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var filter_dropdown = true;
        var connection_type_dropdown = true;
        var hostpot = false;
        /*Get Url Param Recevied*/
        var pop_id = @json($pop_id ?? '');
        var area_id = @json($area_id ?? '');
        var status = @json($status ?? '');


        /*Year Month  examample :20 JUN 2025 Type="new or expired"*/
        let get_year = null;
        let get_month = null;
        let get_type = null;

        if(status == null || status == ''){
            const urlParams = new URLSearchParams(window.location.search);
            status = urlParams.get('status');
        }
        if(get_year ==null || get_month==null || get_type== null){
            const urlParams = new URLSearchParams(window.location.search);
            get_year = urlParams.get('year');
            get_month = urlParams.get('month');
            get_type = urlParams.get('type');
        }

        filter_dropdown = @json($filter_dropdown ?? true);
        connection_type_dropdown = @json($connection_type_dropdown ?? true);
        hostpot = @json($hostpot ?? false);


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

        /* Status Filter */
        var status_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select class="status_filter form-control form-control-sm select2">
                    <option value="">--Status--</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="expired">Expired</option>
                    <option value="grace">Grace</option>

                    <option value="active">Active</option>
                    <option value="disabled">Disabled</option>
                    <option value="discontinue">Discontinue</option>
                </select>
            </div>`;
        /* connection_type_filter  Filter */
        if(connection_type_dropdown==true){
            var connection_type_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select class="connection_type_filter form-control form-control-sm select2">
                    <option value="">Connection Type</option>
                    <option value="pppoe">PPPOE</option>
                    <option value="radius">Radius</option>
                    <option value="hostpot">Hostpot</option>
                </select>
            </div>`;
        }


        setTimeout(() => {
            var filters_wrapper = `
                <div class="row no-gutters mb-0  " style=" row-gap: 0.5rem;">
                    <!-- Left: Per Page -->
                    <div class="col-12 col-md-auto dataTables_length_container d-flex align-items-center mb-2 mb-md-0 pr-md-3"></div>

                    <!-- Middle: Filters -->
                    <div class="col-12 col-md d-flex flex-wrap align-items-center mb-2 mb-md-0" style="gap: 0.5rem;">
                        ${(pop_filter   ?? '')}
                        ${(area_filter  ?? '')}
                        ${(status_filter ?? '')}
                        ${(connection_type_filter ?? '')}
                    </div>

                    <!-- Right: Search Input -->
                    <div class="col-12 col-md-auto dataTables_filter_container d-flex justify-content-md-end"></div>
                </div>
            `;
            /* Append the filters to the DataTable wrapper */
            if(filter_dropdown==true){
                var tableWrapper = $('#customer_datatable1').closest('.dataTables_wrapper');
                tableWrapper.prepend(filters_wrapper);

                tableWrapper.find('.dataTables_length').appendTo(tableWrapper.find('.dataTables_length_container'));
                tableWrapper.find('.dataTables_filter').appendTo(tableWrapper.find('.dataTables_filter_container'));
            }



            $('#search_pop_id').select2({ width: 'resolve' });
            $('#search_area_id').select2({ width: 'resolve' });
            $('.status_filter').select2({ width: 'resolve' });
            $('.connection_type_filter').select2({ width: 'resolve' });
        }, 1000);




        /*Check Param Values if else */
        if (!pop_id) {
            pop_id = $('#search_pop_id').val();
        }
        if (!area_id) {
            area_id = $('#search_area_id').val();
        }
        if (status == null || status == '') {
            status = $('.status_filter').val();
        }

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
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
            $('select[name="search_area_id"]').html(areasOptions);
        });
        /*Handle Area filter change*/
        $(document).on('change', 'select[name="search_area_id"]', function() {
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
        });
        /*Handle Status filter change*/
        $(document).on('change', '.status_filter', function() {
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
        });
        /*Handle Connection Type filter change*/
        $(document).on('change', '.connection_type_filter', function() {
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
        });
        /*Check Box Selected*/
        $(document).on('change', '#customer_select_all', function() {
            let checked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', checked);
        });
        /*Show Button if at least One Check box*/
        $(document).on('change', '.row-checkbox, #customer_select_all', function () {
            let anyChecked = $('.row-checkbox:checked').length > 0;

            if (anyChecked) {
                $('#bulk_recharge').removeClass('d-none').fadeIn(400);
                $('#send_message').removeClass('d-none').fadeIn(400);
                $('#change_billing').removeClass('d-none').fadeIn(400);
            } else {
                $('#bulk_recharge').addClass('d-none').fadeOut(400);
                $('#send_message').addClass('d-none').fadeOut(400);
                $('#change_billing').addClass('d-none').fadeOut(400);
            }
        });
        var customer_table = $("#customer_datatable1").DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            beforeSend: function() {

            },
            complete: function() {

            },
            "ajax": {
                url: "{{ route('admin.customer.get_all_data') }}",
                type: "GET",
                data: function(d) {
                    d.pop_id            = $('#search_pop_id').val() || pop_id;
                    d.area_id           = $('#search_area_id').val() || area_id;
                    d.status            = $('.status_filter').val() || status;
                    d.connection_type   = $('.connection_type_filter').val() || (hostpot === true ? 'hostpot' : '');
                    d.year              = get_year || null;
                    d.month             = get_month || null;
                    d.type              = get_type || null;
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
            "columns": [
                // {
                //     data: 'id',
                //     orderable: false,
                //     searchable: false,
                //     render: function(data, type, row, meta) {
                //         return `<div class="custom-control custom-checkbox">
                //                     <input type="checkbox" class="custom-control-input row-checkbox" id="row_checkbox_${data}" value="${data}">
                //                     <label class="custom-control-label" for="row_checkbox_${data}"></label>
                //                 </div>`;
                //     }
                // },

                {
                    "data": "id"
                },
                {
                    "data": "fullname",
                    "render": function(data, type, row) {
                        var viewUrl = '{{ route('admin.customer.view', ':id') }}'.replace(':id',
                            row.id);
                        /*Set the icon based on the status*/
                        var icon = '';
                        var color = '';
                        var last_seen = '';

                        if (row.status === 'online') {
                            icon = '<i class="fas fa-unlock" style="font-size: 15px; color: green; margin-right: 8px;" title="Online"></i>';
                        } else if (row.status === 'offline') {
                            icon = '<i class="fas fa-lock" style="font-size: 15px; color: red; margin-right: 8px;" title="Offline"></i>';
                             /* Show last_seen time if available*/
                            if (row.last_seen) {
                                last_seen = `<small style="color: gray; margin-left: 5px;">(${__time_ago(row.last_seen)})</small>`;
                            }
                        } else if (row.status === 'expired') {
                            icon = '<i class="fas fa-clock" style="font-size: 15px; color: orange; margin-right: 8px;" title="Expired"></i>';
                        } else if (row.status === 'blocked') {
                            icon = '<i class="fas fa-ban" style="font-size: 15px; color: darkred; margin-right: 8px;" title="Blocked"></i>';
                        } else if (row.status === 'disabled') {
                            icon = '<i class="fas fa-user-slash" style="font-size: 15px; color: gray; margin-right: 8px;" title="Disabled"></i>';
                        } else if (row.status === 'discontinue') {
                            icon = '<i class="fas fa-times-circle" style="font-size: 15px; color: #ff6600; margin-right: 8px;" title="Discontinue"></i>';
                        } else {
                            icon = '<i class="fa fa-question-circle" style="font-size: 18px; color: gray; margin-right: 8px;" title="Unknown"></i>';
                        }
                         return `<a href="${viewUrl}" style="display: flex; align-items: center; text-decoration: none; color: #333;">
                                    ${icon}
                                    <span style="font-size: 16px; font-weight: bold;">${row.fullname}</span>
                                    ${last_seen}
                                </a>`;
                    }
                },



                {
                    "data": "package.name"
                },
                {
                    "data": "amount"
                },


                {
                    "data": "expire_date",
                    "render": function(data, type, row) {
                        var expireDate = new Date(data);
                        var todayDate = new Date();
                        todayDate.setHours(0, 0, 0, 0);

                        if (todayDate > expireDate) {
                            return '<span class="badge bg-danger">Expire (' + data + ')</span>';
                        } else {
                            return data;
                        }
                    }
                },


                {
                    "data": "username"
                },
                {
                    "data": "phone",
                    "render": function(data, type, row) {
                        return '<i class="fas fa-phone-alt" style="color: #007bff; margin-right: 6px;"></i>' +
                            '<span>' + row.phone + '</span>';
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
                    "data": "created_at",
                    "render": function(data, type, row) {
                        var date = new Date(data);
                        var options = {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit'
                        };
                        return date.toLocaleDateString('en-GB', options);
                    }
                },

                {
                    data: null,
                    render: function(data, type, row) {
                        var viewUrl = '{{ route('admin.customer.view', ':id') }}'.replace(':id',
                            row.id);

                        return `
                            <button class="btn btn-primary btn-sm mr-3 customer_edit_btn" data-id="${row.id}">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-sm mr-3 delete-btn" data-id="${row.id}">
                                <i class="fa fa-trash"></i>
                            </button>

                            <a href="${viewUrl}" class="btn-sm btn btn-success mr-3">
                                <i class="fa fa-eye"></i>
                            </a> `;
                    }


                },
            ],
            order: [
                [1, "desc"]
            ],
            dom: 'Bfrtip',
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>' +
           'rt' +
           '<"row"<"col-md-6"i><"col-md-6"p>>' +
           '<"row"<"col-md-12"B>>',
           lengthMenu: [[10, 25, 50,100,150,200, -1], [10, 25, 50,100,150,200, "All"]],
            "pageLength": 10,
            "buttons": [
                { extend: 'copy', text: 'Copy', className: 'btn btn-secondary btn-sm ' },
                { extend: 'csv', text: 'CSV', className: 'btn btn-secondary btn-sm ml-1' },
                { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm ml-1' },
                { extend: 'pdf', text: 'PDF', className: 'btn btn-danger btn-sm ml-1' },
                { extend: 'print', text: 'Print', className: 'btn btn-info btn-sm ml-1',title: "Customer Report - {{ date('Y-m-d') }}"},
            ],
        });

        /** Handle Delete button click**/
        $('#customer_datatable1 tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            var deleteUrl = "{{ route('admin.customer.delete', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').find('input[name="id"]').val(id);
            $('#deleteModal').modal('show');
        });

        /** Handle form submission for delete **/
        $('#deleteModal form').submit(function(e) {
            e.preventDefault();
            /*Get the submit button*/
            var submitBtn = $('#deleteModal form').find('button[type="submit"]');

            /* Save the original button text*/
            var originalBtnText = submitBtn.html();

            /*Change button text to loading state*/
            submitBtn.html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>`
            );

            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();
            /** Use Ajax to send the delete request **/
            $.ajax({
                type: 'POST',
                'url': url,
                data: formData,
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    if (response.success) {
                        toastr.success(response.message);
                        $('#customer_datatable1').DataTable().ajax.reload(null, false);
                    }
                },

                error: function(xhr, status, error) {
                    /** Handle  errors **/
                    toastr.error(xhr.responseText);
                },
                complete: function() {
                    submitBtn.html(originalBtnText);
                }
            });
        });

        function __time_ago(datetime) {
            const now = new Date();
            const then = new Date(datetime);
            const diff = Math.floor((now - then) / 1000); // diff in seconds

            if (diff < 60) {
                return `${diff} sec${diff !== 1 ? 's' : ''} ago`;
            }

            const minutes = Math.floor(diff / 60);
            if (minutes < 60) {
                return `${minutes} min${minutes !== 1 ? 's' : ''} ago`;
            }

            const hours = Math.floor(diff / 3600);
            if (hours < 24) {
                return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
            }

            const days = Math.floor(diff / 86400);
            if (days < 7) {
                return `${days} day${days !== 1 ? 's' : ''} ago`;
            }

            return then.toLocaleString();
        }
    });
</script>
