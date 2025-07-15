@extends('Backend.Layout.App')
@section('title', 'Dashboard | SMS Template | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title d-flex align-items-center gap-2 text-primary">
                        <i class="fas fa-sync fa-spin text-info me-2"></i>&nbsp;&nbsp;
                        <span>Sync Data to MikroTik Router</span>
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

                    <div class="row mb-3">
                        <div class="col-md-12 text-right">
                            <button type="button" id="sync_btn" class="btn btn-primary">
                                <i class="fas fa-cloud-upload-alt me-2"></i> Sync to MikroTik
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
@endsection

@section('script')
    <script type="text/javascript">
        var selectedCustomers = [];
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
            $(document).on('click', '#sync_btn', function(event) {
                event.preventDefault();

                var button = $('#sync_btn');
                button.html(`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`);
                button.attr('disabled', true);
                $(".checkSingle:checked").each(function() {
                    selectedCustomers.push($(this).val());
                });
                if(selectedCustomers.length==0){
                    toastr.error('Please Selete Customer');
                    button.html('Send Message');
                    button.attr('disabled', false);
                    return false;
                }
                $.ajax({
                    url: "{{ route('admin.sms.send_message_store') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {  _token: "{{ csrf_token() }}", message: message, customer_ids:selectedCustomers },
                    success: function(response) {
                        if(response.success==true){
                            toastr.success(response.message);
                            $('#sendMessageModal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }

                        if(response.success==false) {
                            toastr.error(response.message);
                        }
                    },
                    complete: function() {
                        button.html('Send Message');
                        button.attr('disabled', false);
                    }
                });
            });

    </script>

@endsection
