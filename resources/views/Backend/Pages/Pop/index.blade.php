@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    <button data-toggle="modal" data-target="#addModal" type="button" class=" btn btn-success mb-2"><i
                            class="mdi mdi-account-plus"></i>
                        Add New POP/Branch</button>

                    <div class="table-responsive" id="tableStyle">
                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Mobile</th>
                                    <th>Active Customer</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Expired</th>
                                    <th>status</th>
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
    @include('Backend.Modal.Pop.pop_modal')
    @include('Backend.Modal.delete_modal')


@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            handleSubmit('#popForm', '#addModal');
            handleSubmit('#popEditForm', '#editPopBranchModal');
            var table = $("#datatable1").DataTable({
                "processing": true,
                "responsive": true,
                "serverSide": true,
                beforeSend: function() {},
                complete: function() {},
                ajax: "{{ route('admin.pop.get_all_data') }}",
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page',
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "name",
                        render: function(data, type, row) {
                            return `<a href="{{ route('admin.pop.view', '') }}/${row.id}">${row.name}</a>`;
                        }
                    },

                    {
                        "data": "username"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": "active_customer",
                        render: function(data, type, row) {
                            return `<span class="badge badge-primary">${row.active_customer}</span>`;
                        }
                    },
                    {
                        "data": "online",
                        render: function(data, type, row) {
                            return `<span class="badge badge-success">${row.online}</span>`;
                        }
                    },
                    {
                        "data": "offline",
                        render: function(data, type, row) {
                            return `<span class="badge badge-warning">${row.offline}</span>`;
                        }
                    },
                    {
                        "data": "expired",
                        render: function(data, type, row) {
                            return `<span class="badge badge-danger">${row.expired}</span>`;
                        }
                    },
                    {
                        "data": "status",
                        render: function(data, type, row) {
                            if (row.status == 1) {
                                return '<span class="badge badge-success">Active</span>';
                            } else {
                                return '<span class="badge badge-danger">Disable</span>';
                            }
                        }
                    },
                    {
                        "data": null,
                        render: function(data, type, row) {
                            var viewUrl = "{{ route('admin.pop.view', ':id') }}".replace(':id', row
                                .id);
//  <button type="button" name="pop_login_button" data-id="${row.id}" class="btn btn-warning btn-sm">
//                                     <i class="fa fa-sign-in-alt"></i>
//                                 </button>

                            return `
                                <button  class="btn btn-primary btn-sm mr-3 edit-btn" data-id="${row.id}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <a href="${viewUrl}" class="btn btn-success btn-sm mr-3 ">
                                    <i class="fa fa-eye"></i>
                                </a>

                                `;
                        }
                    }
                ],

                order: [
                    [0, "desc"]
                ],

            });

        });








        /** Handle Edit button click **/
        $('#datatable1 tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.pop.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {


                        $('#popEditForm input[name="id"]').val(response.data.id);
                        $('#popEditForm input[name="name"]').val(response.data.name);
                        $('#popEditForm input[name="username"]').val(response.data.username);

                        $('#popEditForm input[name="password"]')
                        .val(response.data.password)
                        .prop('readonly', true);

                        $('#popEditForm input[name="phone"]').val(response.data.phone);
                        $('#popEditForm input[name="email"]').val(response.data.email);
                        $('#popEditForm input[name="address"]').val(response.data.address);
                        $('#popEditForm select[name="status"]').val(response.data.status).trigger('change');

                        // Show the modal
                        $('#editPopBranchModal').modal('show');
                    } else {
                        toastr.error('Failed to fetch Supplier data.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });

        /** Handle Delete button click**/
        $('#datatable1 tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            var deleteUrl = "{{ route('admin.pop.delete', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').find('input[name="id"]').val(id);
            $('#deleteModal').modal('show');
        });
        /*Handle Pop Branch Login */
        $("#datatable1 tbody").on('click', 'button[name="pop_login_button"]',function(){
            var $button = $(this);
            var id = $button.data('id');

            /* Show spinner*/
            $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...');
            $button.prop('disabled', true);

            var login_url = "{{ route('admin.pop.branch.auto_login', ':id') }}".replace(':id', id);
            setTimeout(function () {
                window.location.href = login_url;
            }, 500);
        });
    </script>
@endsection
