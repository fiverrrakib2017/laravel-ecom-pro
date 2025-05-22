@php
   //$branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
   if(!empty($branch_user_id) && $branch_user_id > 0){
        $pop_branches = \App\Models\Pop_branch::where('id',$branch_user_id)->first();
        $areas=\App\Models\Pop_area::where('status','active')->where('pop_id',$branch_user_id)->get();
   }else{
    $pop_branches=\App\Models\Pop_branch::where('status',1)->get();
    $areas=\App\Models\Pop_area::where('status','active')->get();
   }


@endphp


<div class="col-6 nav justify-content-end" id="export_buttonscc"></div>
<table id="customer_datatable1" class="table table-bordered dt-responsive nowrap"
    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th>
                <input type="checkbox" class="custom-control-input" id="selectAllCheckbox" name="selectAll">
            </th>

            <th>ID</th>
            <th>Name</th>
            <th>Package</th>
            <th>Amount</th>
            <th>Create Date</th>
            <th>Expired Date</th>
            <th>User Name</th>
            <th>Mobile no.</th>
            <th>POP/Branch</th>
            <th>Area/Location</th>
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

        var pop_id = @json($pop_id ?? '');
        var area_id = @json($area_id ?? '');
        var status = @json($status ?? '');

        /*GET POP-Branch */
        var pop_branches    = @json($pop_branches);
        var pop_filter      = '<label style="margin-left: 20px;">';
        pop_filter          += '<select id="search_pop_id" name="search_pop_id" class="form-control">';
        pop_filter          += '<option value="">--Select POP/Branch--</option>';
        pop_branches.forEach(function(item) {
            pop_filter += '<option value="' + item.id + '">' + item.name + '</option>';
        });
        pop_filter += '</select></label>';

        /*Get Areas*/
        var areas = @json($areas);
        var area_filter = '<label style="margin-left: 20px;">';
        area_filter     += '<select id="search_area_id" name="search_area_id" class="form-control">';
        area_filter     += '<option value="">--Select Area--</option>';
        areas.forEach(function(item) {
           // area_filter += '<option value="' + item.id + '">' + item.name + '</option>';
        });
        area_filter += '</select></label>';

        /*Status Filter*/
       var  status_filter = '<label style="margin-left: 20px;"> ';
            status_filter += '<select class="status_filter form-control">';
            status_filter += '<option value="">--Status--</option>';
            status_filter += '<option value="online" >Online</option>';
            status_filter += '<option value="offline">Offline</option>';
            status_filter += '<option value="expired">Expired</option>';
            status_filter += ' <option value="unpaid">Unpaid</option>';
            status_filter += ' <option value="due">Due</option>';
            status_filter += ' <option value="free">Free</option>';
            status_filter += ' <option value="active">Active</option>';
            status_filter += ' <option value="disabled">Disabled</option>';
            status_filter += '</select></label>';

        setTimeout(() => {
            $('.dataTables_length').append(pop_filter);
            $('.dataTables_length').append(area_filter);
            $('.dataTables_length').parent().removeClass('col-sm-12 col-md-6');
            $('.dataTables_filter').parent().removeClass('col-sm-12 col-md-6');
            $('.dataTables_length').append(status_filter);

            $('#search_pop_id').select2();
            $('#search_area_id').select2();
            $('.status_filter').select2();
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

            $('select[name="search_area_id"]').html(areasOptions);
        });
        /*Handle POP/Branch filter change*/
        $('select[name="search_pop_id"]').on('change', function() {
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
        });
        /*Handle Area filter change*/
        $('select[name="search_area_id"]').on('change', function() {
            alert('okkk');
            $('#customer_datatable1').DataTable().ajax.reload(null, false);
        });

        var customer_table = $("#customer_datatable1").DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            beforeSend: function() {},
            complete: function() {},
            "ajax": {
                url: "{{ route('admin.customer.get_all_data') }}",
                type: "GET",
                data: function(d) {
                    d.pop_id = pop_id;
                    d.area_id = area_id;
                    d.status = status;
                }
            },
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_ items/page',
            },
            "columns": [{
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return '<div class="custom-control custom-checkbox">' +
                            '<input type="checkbox" class="custom-control-input" id="checkbox_' +
                            meta.row + '" name="checkbox_' + meta.row + '">' +
                            '<label class="custom-control-label" for="checkbox_' + meta.row +
                            '"></label>' +
                            '</div>';
                    }
                },
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

                        if (row.status === 'online') {
                            icon =
                                '<i class="fas fa-unlock" style="font-size: 15px; color: green; margin-right: 8px;"></i>';
                        } else if (row.status === 'offline') {
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
                            .fullname + '</span>' +
                            '</a>';
                    }
                },



                {
                    "data": "package.name"
                },
                {
                    "data": "amount"
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
                    "data": "expire_date",
                    "render": function(data, type, row) {
                        var expireDate = new Date(data);
                        var todayDate = new Date();
                        todayDate.setHours(0, 0, 0, 0);

                        if (todayDate > expireDate) {
                            return '<span class="badge bg-danger">Expire</span>';
                        } else {
                            return data;
                        }
                    }
                },


                {
                    "data": "username"
                },
                {
                    "data": "phone"
                },
                {
                    "data": "pop.name"
                },
                {
                    "data": "area.name"
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


    });
</script>
