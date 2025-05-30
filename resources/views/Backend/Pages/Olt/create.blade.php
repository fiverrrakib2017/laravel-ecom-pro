@extends('Backend.Layout.App')
@section('title', 'Create OLT Device | Dashboard | Admin Panel')
@section('content')
    <div class="container-fluid">
        <div class="card ">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-server"></i>&nbsp; Add OLT Device
                </h3>

            </div>
            <form action="{{ route('admin.olt.store') }}" method="POST" id="addOltForm">
                @csrf
                <div class="card">
                    <div class="card-body row">

                        <div class="form-group col-md-4">
                            <label for="name">OLT Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. OLT-Basundhara"
                                required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="brand">Brand <span class="text-danger">*</span></label>
                            <select name="brand" class="form-control" required>
                                <option value="">-- Select Brand --</option>
                                @foreach (['Huawei', 'ZTE', 'Fiberhome', 'VSOL', 'BDCOM', 'CDATA', 'Opton', 'Tenda', 'TP-Link', 'Nokia', 'DZS', 'Zhone', 'Edgecore', 'Netlink', 'Corelink', 'ECOM', 'TBS', 'Alcatel', 'Cisco', 'Raisecom', 'Skyworth', 'Planet', 'Visiontek', 'Other'] as $brand)
                                    <option value="{{ $brand }}">{{ $brand }}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="mode">Mode</label>
                            <select name="mode" class="form-control" required>
                                <option value="">--- Select Mode ---</option>
                                @foreach (['GPON', 'XG-PON', 'EPON', 'XGS-PON', 'NG-PON2'] as $mode)
                                    <option value="{{ $mode }}">{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="ip_address">IP Address <span class="text-danger">*</span></label>
                            <input type="text" name="ip_address" class="form-control" placeholder="e.g. 192.168.0.1"
                                required>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="port">Port</label>
                            <input type="text" name="port" class="form-control" value="22">
                        </div>

                        <div class="form-group col-md-2">
                            <label for="protocol">Protocol</label>
                            <select name="protocol" class="form-control">
                                <option value="SSH" selected>SSH</option>
                                <option value="Telnet">Telnet</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="snmp_community">SNMP Community</label>
                            <input type="text" name="snmp_community" class="form-control" placeholder="e.g. public">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="snmp_version">SNMP Version</label>
                            <select name="snmp_version" class="form-control">
                                <option value="">-- Select Version --</option>
                                <option value="v1">v1</option>
                                <option value="v2c" selected>v2c</option>
                                <option value="v3">v3</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="username">Login Username</label>
                            <input type="text" name="username" class="form-control" placeholder="e.g. username" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="password">Login Password</label>
                            <input type="password" name="password" placeholder="e.g. password" class="form-control"
                                required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="vendor">Vendor</label>
                            <input type="text" name="vendor" placeholder="e.g. Huawei" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="model">Model</label>
                            <input type="text" name="model" placeholder="e.g. ZTE" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="serial_number">Serial Number</label>
                            <input type="text" name="serial_number" placeholder="e.g. ZTE" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="firmware_version">Firmware Version</label>
                            <input type="text" name="firmware_version" placeholder="e.g. ZTE" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="location">Location</label>
                            <input type="text" name="location" placeholder="e.g. Dhanmondi" class="form-control"
                                placeholder="e.g. Dhanmondi POP Room">
                        </div>

                        <div class="form-group col-md-2">
                            <label for="status">Status</label>
                            <select name="status" class="form-control">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="description">Description</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Optional remarks about this OLT"></textarea>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('admin.olt.index') }}" class="btn btn-danger">Back to OLT List</a>
                        <button type="submit" class="btn btn-primary">Create New OLT</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            /* Handle form submission */
            $('#addOltForm').submit(function(e) {
                e.preventDefault();

                /* Get the submit button */
                var submitBtn = $(this).find('button[type="submit"]');
                var originalBtnText = submitBtn.html();

                submitBtn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>'
                    );
                submitBtn.prop('disabled', true);

                var form = $(this);
                var url = form.attr('action');
                /*Change to FormData to handle file uploads*/
                var formData = new FormData(this);

                /* Use Ajax to send the request */
                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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
                        if (response.success == false) {
                            form.find(':input').prop('disabled', false);
                            toastr.error(response.message);
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        form.find(':input').prop('disabled', false);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            for (var field in errors) {
                                toastr.error(errors[field][0]);
                            }
                        } else {
                            toastr.error("Something went wrong! Please try again.");
                        }
                    },
                    complete: function() {
                        /* Reset button text and enable the button */
                        form.find(':input').prop('disabled', false);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
