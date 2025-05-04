@extends('Backend.Layout.App')
@section('title','Dashboard | Admin Panel')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card  ">
            <form action="{{ route('admin.customer.store') }}" method="post" id="addStudentForm" enctype="multipart/form-data">
                @csrf
            <div class="card-body ">


                    <!-- Personal Information -->
                    <fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
                        <legend class="w-auto px-3 text-primary fw-bold">Personal Information</legend>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" placeholder="Enter Fullname" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter Phone" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">NID</label>
                                <input type="text" name="nid" class="form-control" placeholder="Enter NID">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Address</label>
                                <input name="address" class="form-control" placeholder="Enter Address">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Connection Details -->
                    <fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
                        <legend class="w-auto px-3 text-primary fw-bold">Connection Details</legend>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">POP Branch</label>
                                <select name="pop_id" class="form-control" required>
                                    <option value="">Select POP Branch</option>
                                    @php
                                        $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                        if($branch_user_id != null || $branch_user_id != 0){
                                            $pops = App\Models\Pop_branch::where('id', $branch_user_id)->latest()->get();
                                        }else{
                                            $pops = \App\Models\Pop_branch::latest()->get();
                                        }
                                    @endphp
                                    @foreach ($pops as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Area</label>
                                <select name="area_id" class="form-control" required>
                                    <option value="">Select Area</option>
                                    @php
                                    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                    if($branch_user_id != null || $branch_user_id != 0){
                                        $areas = App\Models\Pop_area::where('pop_id', $branch_user_id)->latest()->get();
                                    }else{
                                        $areas = \App\Models\Pop_area::latest()->get();
                                    }
                                @endphp
                                    @foreach ($areas as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Router</label>
                                <select name="router_id" class="form-control" required>
                                    <option value="">Select Router</option>
                                    @foreach (App\Models\Router::latest()->get() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Package</label>
                                <select name="package_id" id="package_id" class="form-control" required>
                                    <option value="">Select Package</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Connection Charge</label>
                                <input type="number" name="con_charge" class="form-control" value="500" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Additional Information -->
                    <fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
                        <legend class="w-auto px-3 text-primary fw-bold">Additional Information</legend>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Liabilities</label>
                                <select name="liabilities" class="form-control" required>
                                    <option>---Select---</option>
                                    <option value="YES">YES</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Status</label>
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
                            <div class="col-lg-12 mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control" placeholder="কাস্টমার এর সম্পর্কে যদি কোণ নোট রাখতে হয় তাহলে এইখানে লিখে রাখুন , পরবর্তীতে আপনি সেটা কাস্টমার এর প্রোফাইল এ দেখতে পারবেন" style="height: 123px;"></textarea>
                            </div>
                        </div>
                    </fieldset>
            </div>
            <div class="card-footer">
                <button type="button" onclick="history.back();" class="btn btn-danger">Back</button>
                <button type="submit" class="btn btn-success">Add Customer</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
    $(document).ready(function(){
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
        /** Handle pop branch button click **/
        $(document).on('change', 'select[name="pop_id"]', function () {
            var pop_id = $(this).val();
            if(pop_id){
                var $area_url="{{ route('admin.pop.area.get_pop_wise_area', ':id') }}".replace(':id', pop_id);
                var $package_url="{{ route('admin.pop.branch.get_pop_wise_package', ':id') }}".replace(':id', pop_id);
                load_dropdown($area_url,'select[name="area_id"]');
                load_dropdown($package_url,'select[name="package_id"]');
            }else{
                $(' select[name="area_id"]').html('<option value="">Select Area</option>');
                $(' select[name="package_id"]').html('<option value="">Select Package</option>');
            }

        });
        /** Handle Amount when package button click **/
        $(document).on('change', ' select[name="package_id"]', function () {
            var package_id = $(this).val();
            var $amount_url = "{{ route('admin.pop.branch.get_pop_wise_package_price', ':id') }}".replace(':id', package_id);
            if(package_id){
                $.ajax({
                    url: $amount_url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('input[name="amount"]').val(response.data.purchase_price);
                    }
                });
            }else{
                $('input[name="amount"]').val('0');
            }

        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){

        $('#addStudentForm').submit(function(e) {
            e.preventDefault();

            /* Get the submit button */
            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();

            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>');
            submitBtn.prop('disabled', true);

            var form = $(this);
            var url = form.attr('action');
            /*Change to FormData to handle file uploads*/
            var formData = new FormData(this);

            /* Use Ajax to send the request */
            $.ajax({
                type: 'POST',
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    /* Disable the Form input */
                    form.find(':input').prop('disabled', true);
                    submitBtn.prop('disabled', true);
                },
                success: function(response) {

                    if (response.success) {
                        toastr.success(response.message);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                    if(response.success == false){
                        form.find(':input').prop('disabled', false);
                        toastr.error(response.message);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    /* Handle errors */
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        for (var error in errors) {
                            toastr.error(errors[error][0]);
                        }
                    } else {
                        toastr.error('An error occurred while processing the request.');
                    }
                },
                complete: function() {
                    /* Reset button text and enable the button */
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                }
            });
        });


    });
  </script>


@endsection
