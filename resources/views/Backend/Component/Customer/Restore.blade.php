<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<table id="customer_datatable1" class="table table-bordered dt-responsive nowrap"
    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>

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


<div id="restoreModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <form method="post" enctype="multipart/form-data" id="restoreForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h4 class="modal-title w-100">Are you sure?</h4>
                    <input type="hidden" name="id" value="">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to Restore these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-undo"></i>&nbsp Restore</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var customer_table = $("#customer_datatable1").DataTable({
            "processing": true,
            "responsive": true,
            "serverSide": true,
            beforeSend: function() {},
            complete: function() {},
            ajax: "{{ route('admin.customer.restore.get_all_data') }}",
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_ items/page',
            },
            "columns": [{
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
                        var viewUrl = '{{ route("admin.customer.view", ":id") }}'.replace(':id', row.id);

                        return `
                            <button class="btn btn-primary btn-sm mr-3 restore-btn" data-id="${row.id}">
                                <i class="fa fa-undo"></i>
                            </button>

                            <button class="btn btn-danger btn-sm mr-3 delete-btn" data-id="${row.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        `;
                    }
                },
            ],
            order: [
                [0, "desc"]
            ],

        });

        /** Handle Delete button click**/
        $('#customer_datatable1 tbody').on('click', '.restore-btn', function() {
            var id = $(this).data('id');
            var deleteUrl = "{{ route('admin.customer.restore.back', ':id') }}".replace(':id', id);

            $('#restoreForm').attr('action', deleteUrl);
            $('#restoreModal').find('input[name="id"]').val(id);
            $('#restoreModal').modal('show');
        });
        /** Handle form submission for restore **/
        $('#restoreModal form').submit(function(e) {
            e.preventDefault();
            /*Get the submit button*/
            var submitBtn = $('#restoreModal form').find('button[type="submit"]');

            /* Save the original button text*/
            var originalBtnText = submitBtn.html();

            /*Change button text to loading state*/
            submitBtn.html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>`
                );

            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();
            /** Use Ajax to send the restore request **/
            $.ajax({
                type: 'POST',
                'url': url,
                data: formData,
                success: function(response) {
                    $('#restoreModal').modal('hide');
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

        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            let url= "{{route('admin.customer.forge_delete')}}";
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id    :id,
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'The item has been deleted.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $(`.delete-btn[data-id="${id}"]`).closest('tr').remove();
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Something went wrong.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });


    });
</script>
