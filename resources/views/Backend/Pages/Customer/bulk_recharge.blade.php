@extends('Backend.Layout.App')
@section('title', 'Dashboard | Bulk Recharge | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-cogs"></i> Bulk Recharge
                    </h4>

                </div>
                <div class="card-body ">
                    <form class="row g-3 align-items-end" id="search_box">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="pop_id" class="form-label">POP/Branch Name <span
                                        class="text-danger">*</span></label>
                                <select name="pop_id" id="pop_id" class="form-control" required>
                                    <option value="">Select POP Branch</option>
                                    @php
                                        $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                        if (empty($pop_id)) {
                                            $pop_id = $branch_user_id;
                                        }
                                        if ($branch_user_id != null) {
                                            $pops = App\Models\Pop_branch::where('status','1')->where('id', $branch_user_id)->get();
                                        } else {
                                            $pops = App\Models\Pop_branch::where('status','1')->latest()->get();
                                        }
                                    @endphp
                                    @foreach ($pops as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $pop_id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="area" class="form-label">Area <span class="text-danger">*</span></label>
                                <select name="area_id" id="area_id" class="form-control" required>
                                    <option value="">Select Area</option>
                                    @php
                                        $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                        if ($branch_user_id != null || $branch_user_id != 0) {
                                            $areas = App\Models\Pop_area::where('pop_id', $branch_user_id)
                                                ->latest()
                                                ->get();
                                        } else {
                                            $areas = \App\Models\Pop_area::latest()->get();
                                        }
                                    @endphp
                                    @foreach ($areas as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="customer_status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select name="customer_status" id="customer_status" class="form-select" style="width: 100%;"
                                    required>
                                    <option value="">---Select---</option>
                                    <option value="active">Active</option>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                    <option value="blocked">Blocked</option>
                                    <option value="expired">Expired</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 d-grid">
                            <div class="form-group">
                                <button type="button" name="search_btn" class="btn btn-success">
                                    <i class="fas fa-search me-1"></i> Search Now
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body d-none" id="print_area">

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" id="bulk_recharge_btn" class="btn btn-primary mb-2">
                                <i class="fas fa-cogs"></i> Process
                            </button>

                        </div>
                    </div>

                    <div class="table-responsive responsive-table">

                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>

                                    <th class=""><input type="checkbox" id="selectAll" class=" customer-checkbox">
                                    </th>
                                    <th class="">ID.</th>
                                    <th class="">Username</th>
                                    <th class="">Package </th>
                                    <th class="">Price </th>
                                    <th class="">Expire Date </th>
                                    <th class="">POP/Branch</th>
                                    <th class="">Area</th>
                                    <th class="">Phone Number</th>
                                    <th class="">Address</th>
                                </tr>
                            </thead>
                            <tbody id="_data">
                                <tr id="no-data">
                                    <td colspan="10" class="text-center">No data available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>





    <!-- Modal for Bulk Recharge -->
    <div class="modal fade bs-example-modal-lg" id="bulk_rechargeModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span class="mdi mdi-account-check mdi-18px"></span> &nbsp;Bulk Recharge</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success" id="selectedCustomerCount"></div>
                    <form  action="{{ route('admin.customer.bulk.recharge.store') }}" id="bulk_rechargeForm" method="POST">
                        @csrf
                    @php
                        $months = [
                            1 => 'January',
                            2 => 'February',
                            3 => 'March',
                            4 => 'April',
                            5 => 'May',
                            6 => 'June',
                            7 => 'July',
                            8 => 'August',
                            9 => 'September',
                            10 => 'October',
                            11 => 'November',
                            12 => 'December',
                        ];

                        /*Current Month*/
                        $currentMonth = date('n');
                    @endphp

                        @php
                            $currentYear = date('Y');
                            $years = range($currentYear, $currentYear + 5);
                        @endphp

                        <div class="form-group mb-2">
                            <label>Recharge Month & Year</label>
                            <select  name="recharge_month[]" class="form-control" multiple required>
                                @foreach ($years as $year)
                                    @foreach ($months as $num => $name)
                                        @php
                                            $value = $year . '-' . str_pad($num, 2, '0', STR_PAD_LEFT); // ex: 2025-05
                                            $label = $name . ' ' . $year; // ex: May 2025
                                        @endphp
                                        <option value="{{ $value }}" {{ ($num == $currentMonth && $year == $currentYear) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-2">
                        <label for="">Transaction Type</label>
                        <select type="text" class="form-select" name="transaction_type" style="width: 100%;"
                            required>
                            <option value="">---Select---</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                            <option value="bkash">Bkash</option>
                            <option value="nagad">Nagad</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Remarks</label>
                        <input name="note" placeholder="Enter Remarks" class="form-control" type="text">
                    </div>
                        <div class="modal-footer ">
                            <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                            <button type="submit"  class="btn btn-success">Confirm Recharge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        /** Handle pop branch button click **/
        $(document).on('change', 'select[name="pop_id"]', function() {
            var pop_id = $(this).val();
            if (pop_id) {
                var $area_url = "{{ route('admin.pop.area.get_pop_wise_area', ':id') }}".replace(':id', pop_id);
                var $package_url = "{{ route('admin.pop.branch.get_pop_wise_package', ':id') }}".replace(':id',
                    pop_id);
                load_dropdown($area_url, 'select[name="area_id"]');
            } else {
                $(' select[name="area_id"]').html('<option value="">Select Area</option>');
            }

        });
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
          /***Load Customer **/
          $("button[name='search_btn']").click(function() {
                var button = $(this);

                button.html(`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`);
                button.attr('disabled', true);
                var pop_id = $("#pop_id").val();
                var area_id = $("#area_id").val();
                var customer_status = $("#customer_status").val();
                if ( $.fn.DataTable.isDataTable("#datatable1") ) {
                    $("#datatable1").DataTable().destroy();
                }
                $.ajax({
                    url: "{{ route('admin.customer.get_customer_info') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {  _token: "{{ csrf_token() }}",pop_id: pop_id, area_id: area_id, status: customer_status},
                    success: function(response) {
                        if(response.success==true){

                            $("#print_area").removeClass('d-none');
                            $("#_data").html(response.html);
                            $("#datatable1").DataTable({
                                "paging": true,
                                "searching": true,
                                "ordering": true,
                                "info": true
                            });

                            $('#selectAll').on('click', function() {
                                $('.customer-checkbox').prop('checked', this.checked);
                            });

                            $('.customer-checkbox').on('click', function() {
                                if ($('.customer-checkbox:checked').length == $('.customer-checkbox').length) {
                                    $('#selectAll').prop('checked', true);
                                } else {
                                    $('#selectAll').prop('checked', false);
                                }
                            });
                        }

                        if(response.success==false) {
                            toastr.error(response.message);
                            $("#_data").html('<tr id="no-data"><td colspan="10" class="text-center">No data available</td></tr>');
                        }
                    },
                    complete: function() {
                        button.html('<i class="fas fa-search me-1"></i> Search Now');
                        button.attr('disabled', false);
                    }
                });
            });
            $(document).on('click', '#bulk_recharge_btn', function(event) {
                event.preventDefault();
                var selectedCustomers = [];
                $(".checkSingle:checked").each(function() {
                    selectedCustomers.push($(this).val());
                });
                if(selectedCustomers.length === 0) {
                    toastr.error('Please select at least one customer.');
                    return;
                }
                var countText = "You have selected " + selectedCustomers.length + " customers.";
                $("#selectedCustomerCount").text(countText);
                $('#bulk_rechargeModal').modal('show');
            });
            $("#bulk_rechargeForm").submit(function(e) {
                e.preventDefault();

                /* Get the submit button */
                var submitBtn = $(this).find('button[type="submit"]');
                var originalBtnText = submitBtn.html();

                submitBtn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden"></span>'
                    );
                submitBtn.prop('disabled', true);

                var form = $(this);
                var formData = new FormData(this);
                var customer_ids = [];
                $(".checkSingle:checked").each(function() {
                    customer_ids.push($(this).val());
                });
                customer_ids.forEach(function(customerId) {
                    formData.append('customer_ids[]', customerId);
                });
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        form.find(':input').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success == true) {
                            toastr.success(response.message);
                            form[0].reset();
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                            form.find(':input').prop('disabled', false);
                        } else if (response.success == false) {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            /* Validation error*/
                            var errors = xhr.responseJSON.errors;

                            /* Loop through the errors and show them using toastr*/
                            $.each(errors, function(field, messages) {
                                $.each(messages, function(index, message) {
                                    /* Display each error message*/
                                    toastr.error(message);
                                });
                            });
                        } else {
                            /*General error message*/
                            toastr.error('An error occurred. Please try again.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        form.find(':input').prop('disabled', false);
                    }
                });
            });
    </script>

@endsection
