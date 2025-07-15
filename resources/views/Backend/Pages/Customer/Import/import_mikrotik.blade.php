@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
<style>
    .select2-container--default .select2-selection--single {
        height: 32px !important;
        padding: 3px 8px;
        font-size: 14px;
    }

    .select2-container {
        width: 100% !important;
    }

    table td select {
        min-width: 100px;
    }
</style>
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
                                   <th scope="col" style="width: 100px;">User Name</th>
                                    <th scope="col" style="width: 100px;">Password</th>
                                    <th scope="col" style="width: 120px;">Profile Name</th>
                                    <th scope="col" style="width: 150px;">POP/Branch</th>
                                    <th scope="col" style="width: 150px;">Area Name</th>
                                    <th scope="col" style="width: 150px;">Package Name</th>
                                    <th scope="col" style="width: 100px;">Billing Cycle</th>
                                    <th scope="col" style="width: 200px;">Action</th>
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
            // handleSubmit('#routerForm', '#addModal');


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
                            <td>${user.username}</td>
                            <td>${user.password}</td>
                            <td>${user.profile}</td>
                            <td>${user.pop}</td>
                            <td>${user.area}</td>
                            <td>${user.package}</td>
                            <td>${user.billing_cycle}</td>
                            <td><button class="btn btn-sm btn-success save-user" data-user='${JSON.stringify(user)}'>Save</button></td>
                        </tr>`;
                    });
                    $('#mikrotik_data_table tbody').html(tbody);
                    $("#mikrotik_data_table").dataTable();
                    $('.pop-select').select2();
                    $('.area-select').select2();
                }
            });
        });

        /*GET POP/Branch Area Dependent*/
        $(document).on('change','.pop-select',function(){
            var pop_id = $(this).val();
            if(pop_id){
                var $area_url="{{ route('admin.pop.area.get_pop_wise_area', ':id') }}".replace(':id', pop_id);
                var $package_url="{{ route('admin.pop.branch.get_pop_wise_package', ':id') }}".replace(':id', pop_id);
                //var $router_url="{{ route('admin.router.get_router_with_pop', ':id') }}".replace(':id', pop_id);
                load_dropdown($area_url,'select[name="area_id"]');
                load_dropdown($package_url,'select[name="package_id"]');
                //load_dropdown($router_url,'select[name="router_id"]');
            }else{
                $(' select[name="area_id"]').html('<option value="">Select Area</option>');
                $(' select[name="package_id"]').html('<option value="">Select Package</option>');
                //$(' select[name="router_id"]').html('<option value="">Select Router</option>');
            }
        });
        /*Create A function for Load DropDown*/
        function load_dropdown(url,target_url){
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $(target_url).empty().append('<option value="">---Select---</option>');
                    $.each(data.data, function (key, value) {
                        $(target_url).append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        }
    </script>
@endsection
