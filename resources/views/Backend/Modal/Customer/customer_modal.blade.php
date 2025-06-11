
@php
    $pop_id = $pop_id ?? null;
    $area_id = $area_id ?? null;
@endphp
<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm" action="{{ route('admin.customer.store') }}" method="POST">
                    @csrf

                    <!-- Personal Information -->
                    <fieldset class="border p-3 mb-4">
                        <legend class="w-auto px-2 text-primary">Personal Information</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="fullname" class="form-control"
                                        placeholder="Enter Fullname" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" placeholder="Enter Phone"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>NID</label>
                                    <input type="text" name="nid" class="form-control" placeholder="Enter NID">
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="Enter Username" required>
                                </div>
                                <div class="form-group">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Enter Password" required>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input name="address" class="form-control" placeholder="Enter Address">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Connection Details -->
                    <fieldset class="border p-3 mb-4">
                        <legend class="w-auto px-2 text-primary">Connection Details</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>POP Branch</label>
                                    <select name="pop_id" id="pop_id" class="form-control" required>
                                        <option value="">Select POP Branch</option>
                                        @php
                                            $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                            if(empty($pop_id)){
                                                $pop_id = $branch_user_id;
                                            }
                                            if ($branch_user_id != null) {
                                                $pops = App\Models\Pop_branch::where('id', $branch_user_id)->get();
                                            } else {
                                                $pops = App\Models\Pop_branch::latest()->get();
                                            }
                                        @endphp
                                        @foreach ($pops as $item)
                                            <option value="{{ $item->id }}"  @if($item->id == $pop_id) selected @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Area</label>
                                    <select name="area_id" id="area_id" class="form-control" required>
                                        <option value="">Select Area</option>
                                        @php
                                            $datas = App\Models\Pop_area::when($pop_id, function ($query) use ($pop_id) {
                                                return $query->where('pop_id', $pop_id);
                                            })->latest()->get();
                                        @endphp
                                        @foreach ($datas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="form-group">
                                    <label>Package</label>
                                    <select name="package_id" id="package_id" class="form-control" required>
                                        <option value="">Select Package</option>
                                        @php
                                            $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                            $datas = collect();

                                            if(isset($pop_id) || $branch_user_id){
                                                $search_pop_id = $pop_id ?? $branch_user_id;
                                                $datas = App\Models\Branch_package::where('pop_id', $search_pop_id)->latest()->get();
                                            }else{
                                                $datas = App\Models\Branch_package::latest()->get();
                                            }

                                        @endphp
                                        @foreach ($datas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>


                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Router</label>
                                    <select name="router_id" class="form-control" required>
                                        <option value="">Select Router</option>
                                        @php
                                            $datas = App\Models\Router::latest()->get();
                                        @endphp
                                        @foreach ($datas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Connection Charge</label>
                                    <input type="number" name="con_charge" class="form-control" value="500"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" name="amount" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!--Additional Information -->
                    <fieldset class="border p-3 mb-4">
                        <legend class="w-auto px-2 text-primary">Additional Information</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Liabilities</label>
                                    <select name="liabilities" class="form-control" required>
                                        <option>---Select---</option>
                                        <option value="YES">YES</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option>---Select---</option>
                                        <option value="active">Active</option>
                                        <option value="online">Online</option>
                                        <option value="offline">Offline</option>
                                        <option value="blocked">Blocked</option>
                                        <option value="expired">Expired</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remarks" class="form-control"
                                        placeholder="কাস্টমার এর সম্পর্কে যদি কোণ নোট রাখতে হয় তাহলে এইখানে লিখে রাখুন , পরবর্তীতে আপনি সেটা কাস্টমার এর প্রোফাইল এ দেখতে পারবেন"
                                        style="height: 123px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <!--Device  Information -->
                        <div class="row">
                            <div class="col-12">
                                <div id="liability_device_table" class="mt-3" style="display: none;">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Device Information</h3>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover mb-0" id="device_table">
                                                    <thead class="">
                                                        <tr>
                                                            <th style="min-width: 120px;">Device Type</th>
                                                            <th style="min-width: 140px;">Name</th>
                                                            <th style="min-width: 140px;">Serial No</th>
                                                            <th style="min-width: 140px;">Assign Date</th>
                                                            <th style="min-width: 80px;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select class="form-control" name="device_type[]">
                                                                    <option>---Select---</option>
                                                                    <option value="router">Router</option>
                                                                    <option value="onu">Onu</option>
                                                                    <option value="fiber">Fiber</option>
                                                                    <option value="other">Others</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control" placeholder="Enter Device Name" name="device_name[]"></td>
                                                            <td><input type="text" class="form-control" placeholder="Example: K5453110" name="serial_no[]"></td>
                                                            <td><input type="date" class="form-control" name="assign_date[]"></td>
                                                            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash-alt"></i></button></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer text-left">
                                            <button type="button" class="btn btn-sm btn-primary" id="add_row"><i class="fas fa-plus"></i> Add Row</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Customer</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- Update Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Edit Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm" action="{{ route('admin.customer.store') }}" method="POST">
                    @csrf

                    <!-- Personal Information -->
                    <fieldset class="border p-3 mb-4">
                        <legend class="w-auto px-2 text-primary">Personal Information</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="fullname" class="form-control"
                                        placeholder="Enter Fullname" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control"
                                        placeholder="Enter Phone" required>
                                </div>
                                <div class="form-group">
                                    <label>NID</label>
                                    <input type="text" name="nid" class="form-control"
                                        placeholder="Enter NID">
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="Enter Username" required>
                                </div>
                                <div class="form-group">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Enter Password" required>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input name="address" class="form-control" placeholder="Enter Address">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Connection Details -->
                    <fieldset class="border p-3 mb-4">
                        <legend class="w-auto px-2 text-primary">Connection Details</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>POP Branch</label>
                                    <select name="pop_id" id="pop_id" class="form-control" required>
                                        <option value="">Select POP Branch</option>
                                        @php
                                            $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                            if(empty($pop_id)){
                                                $pop_id = $branch_user_id;
                                            }
                                            if ($branch_user_id != null) {
                                                $pops = App\Models\Pop_branch::where('id', $branch_user_id)->get();
                                            } else {
                                                $pops = App\Models\Pop_branch::latest()->get();
                                            }
                                        @endphp
                                        @foreach ($pops as $item)
                                            <option value="{{ $item->id }}" >{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Area</label>
                                    <select name="area_id" id="area_id" class="form-control" required>
                                        <option value="">Select Area</option>
                                        @php
                                            $datas = App\Models\Pop_area::when($pop_id, function ($query) use ($pop_id) {
                                                return $query->where('pop_id', $pop_id);
                                            })->latest()->get();
                                        @endphp
                                        @foreach ($datas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Package</label>
                                    <select name="package_id" id="package_id" class="form-control" required>
                                        <option value="">Select Package</option>
                                        @php
                                            $datas = App\Models\Branch_package::latest()->get();
                                        @endphp
                                        @foreach ($datas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Router</label>
                                    <select name="router_id" class="form-control" required>
                                        <option value="">Select Router</option>
                                        @php
                                            $datas = App\Models\Router::latest()->get();
                                        @endphp
                                        @foreach ($datas as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Connection Charge</label>
                                    <input type="number" name="con_charge" class="form-control" value="500"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" name="amount" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!--Additional Information -->
                    <fieldset class="border p-3 mb-4">
                        <legend class="w-auto px-2 text-primary">Additional Information</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Liabilities</label>
                                    <select name="liabilities" class="form-control" required>
                                        <option>---Select---</option>
                                        <option value="YES">YES</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option>---Select---</option>
                                        <option value="active">Active</option>
                                        <option value="online">Online</option>
                                        <option value="offline">Offline</option>
                                        <option value="blocked">Blocked</option>
                                        <option value="expired">Expired</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remarks" class="form-control"
                                        placeholder="কাস্টমার এর সম্পর্কে যদি কোণ নোট রাখতে হয় তাহলে এইখানে লিখে রাখুন , পরবর্তীতে আপনি সেটা কাস্টমার এর প্রোফাইল এ দেখতে পারবেন"
                                        style="height: 123px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Customer</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .border {

        border: 2px #c9c9c9 dotted !important;
    }


</style>
<script  src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
<script type="text/javascript">
    /*Add Modal Submit*/
    handleSubmit('#addCustomerForm','#addCustomerModal');
    /*update Modal Submit*/
    handleSubmit('#editCustomerForm','#editCustomerModal');
    $(document).ready(function() {
        function load_dropdown(url, target_url) {
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $(target_url).empty().append('<option value="">---Select---</option>');
                    $.each(data.data, function(key, value) {
                        $(target_url).append('<option value="' + value.id + '">' + value
                            .name + '</option>');
                    });
                }
            });
        }
        /** Handle pop branch button click **/
        $(document).on('change', '#addCustomerModal select[name="pop_id"]', function() {
            var pop_id = $(this).val();
            if (pop_id) {
                var $area_url = "{{ route('admin.pop.area.get_pop_wise_area', ':id') }}".replace(':id',
                    pop_id);
                var $package_url = "{{ route('admin.pop.branch.get_pop_wise_package', ':id') }}"
                    .replace(':id', pop_id);
                load_dropdown($area_url, '#addCustomerModal select[name="area_id"]');
                load_dropdown($package_url, '#addCustomerModal select[name="package_id"]');
            } else {
                $('#addCustomerModal select[name="area_id"]').html(
                    '<option value="">Select Area</option>');
                $('#addCustomerModal select[name="package_id"]').html(
                    '<option value="">Select Package</option>');
            }

        });
        /** Handle Liablities button click **/
        $(document).on('change', '#addCustomerModal select[name="liabilities"]', function() {
            if ($(this).val() === 'YES') {
                $('#liability_device_table').show();
            } else {
                $('#liability_device_table').hide();
            }
        });
        /* Add new row For Include Device*/
        $(document).on('click', '#addCustomerModal #add_row', function() {
            var newRow = `
                <tr>
                    <td>
                        <select class="form-control" name="device_type[]">
                            <option>---Select---</option>
                            <option value="router">Router</option>
                            <option value="onu">Onu</option>
                            <option value="fiber">Fiber</option>
                            <option value="other">Others</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control" placeholder="Enter Device Name" name="device_name[]"></td>
                    <td><input type="text" class="form-control" placeholder="Example: K5453110" name="serial_no[]"></td>
                    <td><input type="date" class="form-control" name="assign_date[]"></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash-alt"></i></button></td>
                </tr>
            `;
            $('#device_table tbody').append(newRow);
        });
        $(document).find('.device-type-select').last().select2({
            width: '100%'
        });
        /* Remove row*/
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        });
        /** Handle Amount when package button click **/
        $(document).on('change', '#addCustomerModal select[name="package_id"]', function() {
            var package_id = $(this).val();
            var $amount_url = "{{ route('admin.pop.branch.get_pop_wise_package_price', ':id') }}"
                .replace(':id', package_id);
            if (package_id) {
                $.ajax({
                    url: $amount_url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#addCustomerModal input[name="amount"]').val(response.data
                            .purchase_price);
                    }
                });
            } else {
                $('#addCustomerModal input[name="amount"]').val('0');
            }

        });




        /*****************************Edit Customer Script************************************/
        /** Handle pop branch button click **/
        $(document).on('change', '#editCustomerModal select[name="pop_id"]', function() {
            var pop_id = $(this).val();
            if (pop_id) {
                var $area_url = "{{ route('admin.pop.area.get_pop_wise_area', ':id') }}".replace(':id',
                    pop_id);
                var $package_url = "{{ route('admin.pop.branch.get_pop_wise_package', ':id') }}"
                    .replace(':id', pop_id);
                load_dropdown($area_url, '#editCustomerModal select[name="area_id"]');
                load_dropdown($package_url, '#editCustomerModal select[name="package_id"]');
            } else {
                $('#editCustomerModal select[name="area_id"]').html(
                    '<option value="">Select Area</option>');
                $('#editCustomerModal select[name="package_id"]').html(
                    '<option value="">Select Package</option>');
            }

        });
        /** Handle Amount when package button click **/
        $(document).on('change', '#editCustomerModal select[name="package_id"]', function() {
            var package_id = $(this).val();
            var $amount_url = "{{ route('admin.pop.branch.get_pop_wise_package_price', ':id') }}"
                .replace(':id', package_id);
            if (package_id) {
                $.ajax({
                    url: $amount_url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#editCustomerModal input[name="amount"]').val(response.data
                            .purchase_price);
                    }
                });
            } else {
                $('#editCustomerModal input[name="amount"]').val('0');
            }

        });
        /** Handle Customer Edit button click **/
        $(document).on('click', '.customer_edit_btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.customer.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#editCustomerForm').attr('action',
                            "{{ route('admin.customer.update', ':id') }}".replace(
                                ':id', id));
                        $('#editCustomerModal input[name="fullname"]').val(response.data
                            .fullname);
                        $('#editCustomerModal input[name="username"]').val(response.data
                            .username);
                        $('#editCustomerModal input[name="password"]').val(response.data
                            .password);
                        $('#editCustomerModal input[name="phone"]').val(response.data
                        .phone);
                        $('#editCustomerModal input[name="nid"]').val(response.data.nid);
                        $('#editCustomerModal input[name="con_charge"]').val(response.data
                            .con_charge);
                        $('#editCustomerModal input[name="amount"]').val(response.data
                            .amount);
                        $('#editCustomerModal input[name="address"]').val(response.data
                            .address);
                        $('#editCustomerModal input[name="remarks"]').val(response.data
                            .remarks);

                        $('#editCustomerModal select[name="pop_id"]').val(response.data
                            .pop_id).select2();
                        $('#editCustomerModal select[name="router_id"]').val(response.data
                            .router_id).select2();
                        $('#editCustomerModal select[name="area_id"]').val(response.data
                            .area_id).select2();
                        $('#editCustomerModal select[name="package_id"]').val(response.data
                            .package_id).select2();
                        $('#editCustomerModal select[name="liabilities"]').val(response.data
                            .liabilities).select2();
                        $('#editCustomerModal select[name="status"]').val(response.data
                            .status).trigger('change');



                        // Show the modal
                        $('#editCustomerModal').modal('show');
                    } else {
                        toastr.error('Failed to fetch data.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });



    });
</script>
