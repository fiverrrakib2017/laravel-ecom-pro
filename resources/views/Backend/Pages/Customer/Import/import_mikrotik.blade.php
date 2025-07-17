@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
<style>
    /* .select2-container--default .select2-selection--single {
        height: 32px !important;
        padding: 3px 8px;
        font-size: 14px;
    } */

    /* .select2-container {
        width: 100% !important;
    } */

    /* table td select {
        min-width: 100px;
    } */
</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <h5 class="card-title d-flex align-items-center gap-2 text-primary">
                            <i class="fas fa-sync fa-spin text-info me-2"></i>&nbsp;&nbsp;
                            <span>Import Data From MikroTik Router</span>
                        </h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-header">
                        <select id="router_id" class="form-control mb-2" style="width: 300px">
                            <option value="">Select MikroTik</option>
                            @php
                                $datas = App\Models\Router::where('status', 'active')->latest()->get();
                            @endphp
                            @foreach ($datas as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mt-3" id="tableStyle">
                       <div class="table-responsive">
                            <table id="mikrotik_data_table" class="table table-bordered table-striped align-middle text-nowrap w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="min-width: 120px;">User Name</th>
                                        <th style="min-width: 120px;">Password</th>
                                        <th style="min-width: 120px;">Profile Name</th>
                                        <th style="min-width: 150px;">POP/Branch</th>
                                        <th style="min-width: 150px;">Area Name</th>
                                        <th style="min-width: 150px;">Package Name</th>
                                        <th style="min-width: 100px;">Amount</th>
                                        <th style="min-width: 130px;">Billing Cycle</th>
                                        <th style="min-width: 150px;">Create Date</th>
                                        <th style="min-width: 150px;">Expire Date</th>
                                        <th style="min-width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="11" class="text-center">No Data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Add Modal -->


    @include('Backend.Modal.delete_modal')


@endsection

@section('script')
    <script type="text/javascript">
        /** Handle Mikrotik Select**/
        $(document).on('change', '#router_id',function(){
            let mikrotik_id = $(this).val();
            if (!mikrotik_id) return;
            var url = "{{ route('admin.mikrotik.get_user', ':id') }}".replace(':id', mikrotik_id);
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function () {

                    $('#mikrotik_data_table tbody').html(`
                        <tr>
                            <td colspan="11" class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                                <span class="ms-2">Loading data, please wait...</span>
                            </td>
                        </tr>
                    `);
                },
                success: function (response) {
                    let tbody = '';
                    $.each(response.users, function (i, user) {
                        tbody += `<tr>
                            <td class="username">${user.username}</td>
                            <td class="password">${user.password}</td>
                            <td class="profile">${user.profile}</td>
                            <td>${user.pop}</td>
                            <td>${user.area}</td>
                            <td>${user.package}</td>
                            <td>${user.amount}</td>
                            <td>${user.billing_cycle}</td>
                            <td>${user.create_date}</td>
                            <td>${user.expire_date}</td>
                            <td>${user.add_button}</td>
                        </tr>`;
                    });
                    $('#mikrotik_data_table tbody').html(tbody);
                    $("#mikrotik_data_table").dataTable();
                    // $('.pop-select').select2();
                    // $('.area-select').select2();
                }
            });
        });

        /*GET POP/Branch Area Dependent*/
        $(document).on('change', '.pop-select', function () {
            var pop_id = $(this).val();
            var $row = $(this).closest('tr');

            var $areaSelect = $row.find('.area-select');
            var $packageSelect = $row.find('.package-select');

            if (pop_id) {
                var area_url = "{{ route('admin.pop.area.get_pop_wise_area', ':id') }}".replace(':id', pop_id);
                var package_url = "{{ route('admin.pop.branch.get_pop_wise_package', ':id') }}".replace(':id', pop_id);

                load_dropdown(area_url, $areaSelect);
                load_dropdown(package_url, $packageSelect);
            } else {
                $areaSelect.html('<option value="">Select Area</option>');
                $packageSelect.html('<option value="">Select Package</option>');
            }
        });
        /*GET Package Price*/
        $(document).on('change', '.package-select', function () {
            var package_id = $(this).val();
            var $row = $(this).closest('tr');
            var $amountField = $row.find('.amount-field');
            if (package_id) {
                var $amount_url = "{{ route('admin.pop.branch.get_pop_wise_package_price', ':id') }}".replace(':id', package_id);

                $.ajax({
                    url: $amount_url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        $amountField.val(response.data.purchase_price ?? 0);
                    },
                    error: function () {
                        $amountField.val('0');
                    }
                });
            } else {
                $amountField.val('');
            }
        });
        $(document).on('click', '.add-user-btn', function () {
            let $btn = $(this);
            $btn.html('<i class="fas fa-spinner fa-spin me-1"></i>');
            $btn.prop('disabled', true);
            let $row = $(this).closest('tr');

            let userData = {
                _token: '{{ csrf_token() }}',
                username: $row.find('td:eq(0)').text().trim(),
                password: $row.find('td:eq(1)').text().trim(),
                profile: $row.find('td:eq(2)').text().trim(),
                pop_id: $row.find('select[name="pop_id"]').val(),
                area_id: $row.find('select[name="area_id"]').val(),
                package_id: $row.find('select[name="package_id"]').val(),
                amount: $row.find('input[name="amount"]').val(),
                billing_cycle: $row.find('input[name="billing_cycle"]').val(),
                create_date: $row.find('input[name="create_date"]').val(),
                expire_date: $row.find('input[name="expire_date"]').val(),
                router_id : $("#router_id").val(),
            };

            $.ajax({
                url : "{{ route('admin.customer.import.mikrotik.store') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    data: userData
                },
                success: function (response) {
                    if(response.success){
                        toastr.success(response.message);
                        let $btnCell = $row.find('td').last();
                        $btnCell.html('<span class="badge bg-success"><i class="fas fa-check me-1"></i></span>');
                    }
                    if(response.success==false){
                        $btn.html('<i class="fas fa-user-plus me-1"></i>');
                        toastr.error(response.message || 'Failed to add user');
                        $btn.prop('disabled', false);
                    }

                },
                error: function () {
                    $btn.html('<i class="fas fa-user-plus me-1"></i>');
                    toastr.error('Something went wrong!');
                    $btn.prop('disabled', false);
                }
            });
        });

        /*Create A function for Load DropDown*/
        function load_dropdown(url, $targetSelect) {
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $targetSelect.empty().append('<option value="">---Select---</option>');
                    $.each(data.data, function (key, value) {
                        $targetSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    // $targetSelect.select2({
                    //     width: 'resolve',
                    //     dropdownParent: $targetSelect.closest('td')
                    // });
                }
            });
        }

    </script>
@endsection
