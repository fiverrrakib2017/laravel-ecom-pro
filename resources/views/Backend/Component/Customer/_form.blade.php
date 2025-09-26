<!-- Personal Information -->
<fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
    <legend class="w-auto px-3 text-primary fw-bold">Personal Information</legend>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="fullname" class="form-control" placeholder="Enter Fullname" value="{{ $customer->fullname ?? old('fullname') }}" required>
        </div>
        <div class="col-lg-6 mb-3 position-relative">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" id="username_input" class="form-control" placeholder="Enter Username" value="{{ $customer->username ?? old('username') }}" required>
            <span id="username_status_icon" style="position:absolute; right:10px; top:38px;"></span>
            <small id="username_status_msg" class="form-text text-muted"></small>
            <script>
            $(document).ready(function () {
                $('#username_input').on('input', function () {
                    let username = $(this).val().trim();

                    if (username.length >= 3) {
                        $.ajax({
                            url: '{{ route("admin.customer.check.username") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                username: username
                            },
                            success: function (response) {
                                if (response.available) {
                                    $('#username_status_icon').html('<i class="fas fa-check-circle text-success"></i>');
                                    $('#username_status_msg').text('Username is available').css('color', 'green');
                                    $("#addCustomerModal form").find('button[type="submit"]').prop('disabled', false);
                                } else {
                                    $('#username_status_icon').html('<i class="fas fa-times-circle text-danger"></i>');
                                    $('#username_status_msg').text('Username already taken').css('color', 'red');
                                    $("#addCustomerModal form").find('button[type="submit"]').prop('disabled', true);
                                }
                            },
                            error: function () {
                                $('#username_status_icon').html('<i class="fas fa-times-circle text-danger"></i>');
                                $('#username_status_msg').text('Error occurred while checking username').css('color', 'red');
                                $("#addCustomerModal form").find('button[type="submit"]').prop('disabled', true);
                            }
                        });
                    } else {
                        $('#username_status_icon').html('');
                        $('#username_status_msg').text('');
                        $("#addCustomerModal form").find('button[type="submit"]').prop('disabled', true);
                    }
                });
            });
        </script>
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" placeholder="Enter Phone" value="{{ $customer->phone ?? old('phone') }}" required>
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="text" name="password" class="form-control" placeholder="Enter Password" value="{{ $customer->password ?? old('password') }}" required>
        </div>

        <div class="col-lg-6 mb-3">
            <label class="form-label">NID</label>
            <input type="text" name="nid" class="form-control" placeholder="Enter NID" value="{{ $customer->nid ?? old('nid') }}">
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Address</label>
            <input name="address" class="form-control" placeholder="Enter Address" value="{{ $customer->address ?? old('address') }}">
        </div>
    </div>
</fieldset>

<!-- Connection Details -->
<fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
    <legend class="w-auto px-3 text-primary fw-bold">Connection Details</legend>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <label class="form-label">POP Branch <span class="text-danger">*</span></label>
            <select name="pop_id" class="form-control" required>
                <option value="">Select POP Branch</option>
                @php
                    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                    if(empty($pop_id)){
                        $pop_id = $branch_user_id;
                    }
                    if ($branch_user_id != null) {
                        $pops = App\Models\Pop_branch::where('status','1')->where('id', $branch_user_id)->get();
                    } else {
                        $pops = App\Models\Pop_branch::where('status','1')->latest()->get();
                    }
                @endphp
                @foreach ($pops as $item)
                    <option value="{{ $item->id }}" >{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Area <span class="text-danger">*</span></label>
            <select name="area_id" class="form-control" required>
                <option value="">Select Area</option>
                @php
                    $areas = App\Models\Pop_area::when($pop_id, function ($query) use ($pop_id) {
                        return $query->where('pop_id', $pop_id);
                    })->latest()->get();
                @endphp
                @foreach ($areas as $item)
                    {{-- <option value="{{ $item->id }}">{{ $item->name }}</option> --}}
                @endforeach
            </select>
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Router <span class="text-danger">*</span></label>
            <select name="router_id" class="form-control" required>
                <option value="">Select Router</option>
                 @php
                    $routers = App\Models\Router::where('status', 'active')->latest()->get();
                @endphp
                @foreach ($routers as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Package <span class="text-danger">*</span></label>
            <select name="package_id" id="package_id" class="form-control" required>
                  <option>Select Package</option>
                 @php
                    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                    $packages = collect();

                    if(isset($pop_id) || $branch_user_id){
                        $search_pop_id = $pop_id ?? $branch_user_id;
                        $packages = App\Models\Branch_package::where('pop_id', $search_pop_id)->latest()->get();
                    }else{
                        $packages = App\Models\Branch_package::latest()->get();
                    }

                @endphp
                @foreach ($packages as $item)
                    {{-- <option value="{{ $item->id }}">{{ $item->name }}</option> --}}
                @endforeach
            </select>
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Connection Type <span class="text-danger">*</span></label>
            <select name="connection_type" class="form-control" required>
                <option>---Select---</option>
                <option value="pppoe" {{ isset($customer) && $customer->connection_type == 'pppoe' ? 'selected' : '' }}>PPPOE</option>
                <option value="radius" {{ isset($customer) && $customer->connection_type == 'radius' ? 'selected' : '' }}>Radius</option>
                <option value="hotspot" {{ isset($customer) && $customer->connection_type == 'hotspot' ? 'selected' : '' }}>Hotspot</option>
            </select>
        </div>
        <div class="col-lg-6 mb-3">
            <label class="form-label">Expire Date <span class="text-danger">*</span></label>
            <input type="date" name="expire_date" class="form-control">
        </div>
    </div>
</fieldset>

<!-- Additional Information -->
<fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
    <legend class="w-auto px-3 text-primary fw-bold">Additional Information</legend>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="form-group">
                <label class="form-label">Liabilities</label>
                <select name="liabilities" class="form-control" required>
                    <option>---Select---</option>
                    <option value="YES" {{ isset($customer) && $customer->liabilities == 'YES' ? 'selected' : '' }}>YES</option>
                    <option value="NO" {{ isset($customer) && $customer->liabilities == 'NO' ? 'selected' : '' }}>NO</option>
                </select>
            </div>
            <div class="form-group">
                 <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ isset($customer) && $customer->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="online" {{ isset($customer) && $customer->status == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ isset($customer) && $customer->status == 'offline' ? 'selected' : '' }}>Offline</option>
                </select>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks"  placeholder="কাস্টমার এর সম্পর্কে যদি কোণ নোট রাখতে হয় তাহলে এইখানে লিখে রাখুন , পরবর্তীতে আপনি সেটা কাস্টমার এর প্রোফাইল এ দেখতে পারবেন" class="form-control" style="height: 123px;">{{ $customer->remarks ?? old('remarks') }}</textarea>
        </div>
    </div>
</fieldset>
