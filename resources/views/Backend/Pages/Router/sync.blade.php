@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <select id="router_id" class="form-control" style="width: 300px">
                            <option value="">Select MikroTik</option>
                            @php
                                $datas = App\Models\Router::where('status', 'active')->latest()->get();
                            @endphp
                            @foreach ($datas as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="table-responsive" id="tableStyle">
                        <table id="mikrotik_data_table" class="table table-hover text-nowrap table-bordered table-striped">
                            <thead class="">
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col"> User name </th>
                                    <th scope="col"> Password </th>
                                    <th scope="col"> Profile Name </th>
                                    <th scope="col"> Comment </th>
                                    <th scope="col"> POP/Branch</th>
                                    <th scope="col"> Area Name </th>
                                    <th scope="col"> Package Name </th>
                                    <th scope="col"> Billing Cycle </th>
                                    <th scope="col" style="width: 200px"> Action </th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Add Modal -->


    @include('Backend.Modal.delete_modal')


@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            handleSubmit('#routerForm', '#addModal');


        });

        /** Handle Edit button click **/
        $('#datatable1 tbody').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.router.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#routerForm').attr('action', "{{ route('admin.router.update', ':id') }}"
                            .replace(':id', id));
                        $('#ModalLabel').html(
                            '<span class="mdi mdi-account-edit mdi-18px"></span> &nbsp;Edit Router');
                        $('#routerForm input[name="name"]').val(response.data.name);
                        $('#routerForm input[name="name"]').val(response.data.name);
                        $('#routerForm input[name="ip_address"]').val(response.data.ip_address);
                        $('#routerForm input[name="api_version"]').val(response.data.api_version);
                        $('#routerForm input[name="username"]').val(response.data.username);
                        $('#routerForm input[name="password"]').val(response.data.password);
                        $('#routerForm input[name="port"]').val(response.data.port);
                        $('#routerForm select[name="status"]').val(response.data.status);
                        $('#routerForm input[name="location"]').val(response.data.location);
                        $('#routerForm textarea[name="remarks"]').val(response.data.remarks);
                        // Show the modal
                        $('#addModal').modal('show');
                    } else {
                        toastr.error('Failed to fetch data.');
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
            var deleteUrl = "{{ route('admin.router.delete', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').find('input[name="id"]').val(id);
            $('#deleteModal').modal('show');
        });
        /** Handle Mikrotik Select**/
        $(document).on('change', '#router_id',function(){
            let mikrotik_id = $(this).val();
            if (!mikrotik_id) return;
            var url = "{{ route('admin.mikrotik.get_user', ':id') }}".replace(':id', mikrotik_id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    let tbody = '';
                    $.each(response.users, function (i, user) {
                        tbody += `<tr>
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.password}</td>
                            <td>${user.profile}</td>
                            <td>${user.comment}</td>
                            <td>${user.pop}</td>
                            <td>${user.area}</td>
                            <td>${user.package}</td>
                            <td>${user.billing_cycle}</td>
                            <td><button class="btn btn-sm btn-success save-user" data-user='${JSON.stringify(user)}'>Save</button></td>
                        </tr>`;
                    });
                    $('#mikrotik_data_table tbody').html(tbody);
                    $("#mikrotik_data_table").dataTable();
                }
            });
        });

    </script>
@endsection
