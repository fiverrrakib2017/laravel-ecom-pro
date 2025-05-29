@extends('Backend.Layout.App')
@section('title', 'ONT Device List | Dashboard | Admin Panel')

@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ONT Device List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Serial Number</th>
                                    <th>MAC Address</th>
                                    <th>PON Port</th>
                                    <th>Status</th>
                                    <th>RX Power (dBm)</th>
                                    <th>Distance (m)</th>
                                    <th>Last Online</th>
                                    <th>Offline Time</th>
                                    <th>Offline Reason</th>
                                    <th>Location</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
    <tr>
        <td>1</td>
        <td>Rahim</td>
        <td>SN123456789</td>
        <td>00:1A:C2:7B:00:47</td>
        <td>0/1/1</td>
        <td><span class="badge badge-success">Online</span></td>
        <td>-18.20</td>
        <td>250</td>
        <td>2025-05-28 14:32</td>
        <td>N/A</td>
        <td>-</td>
        <td>Dhaka Office</td>
        <td>2025-05-28 15:00</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Karim</td>
        <td>SN987654321</td>
        <td>00:1A:C2:7B:00:48</td>
        <td>0/1/2</td>
        <td><span class="badge badge-danger">Offline</span></td>
        <td>-20.10</td>
        <td>300</td>
        <td>2025-05-27 10:00</td>
        <td>2025-05-28 09:00</td>
        <td>Power Failure</td>
        <td>Chittagong Office</td>
        <td>2025-05-28 09:05</td>
    </tr>
    <tr>
        <td>3</td>
        <td>Sumon</td>
        <td>SN112233445</td>
        <td>00:1A:C2:7B:00:49</td>
        <td>0/1/3</td>
        <td><span class="badge badge-success">Online</span></td>
        <td>-17.50</td>
        <td>150</td>
        <td>2025-05-28 13:20</td>
        <td>N/A</td>
        <td>-</td>
        <td>Barishal Branch</td>
        <td>2025-05-28 14:00</td>
    </tr>
    <tr>
        <td>4</td>
        <td>Fatema</td>
        <td>SN556677889</td>
        <td>00:1A:C2:7B:00:50</td>
        <td>0/1/4</td>
        <td><span class="badge badge-danger">Offline</span></td>
        <td>-22.30</td>
        <td>280</td>
        <td>2025-05-26 16:45</td>
        <td>2025-05-27 08:30</td>
        <td>Fiber Cut</td>
        <td>Rajshahi Branch</td>
        <td>2025-05-27 09:00</td>
    </tr>
    <tr>
        <td>5</td>
        <td>Jamil</td>
        <td>SN998877665</td>
        <td>00:1A:C2:7B:00:51</td>
        <td>0/2/1</td>
        <td><span class="badge badge-success">Online</span></td>
        <td>-19.00</td>
        <td>200</td>
        <td>2025-05-28 15:15</td>
        <td>N/A</td>
        <td>-</td>
        <td>Sylhet Office</td>
        <td>2025-05-28 15:20</td>
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
        $(document).ready(function() {
            $(document).on('change','#employee_select_all',function(){
                if ($(this).is(':checked')) {
                    $(".employee-checkbox").prop('checked', true);
                } else {
                    $(".employee-checkbox").prop('checked', false);
                }
            });
            $(document).on('click', '#printBtn', function() {
            /*keep reference*/
            let print_button = $(this);

            /* Show spinner & disable button*/
            print_button.html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...`
            );
            print_button.prop('disabled', true);

            /* Delay spinner for 1s*/
            setTimeout(() => {
                var selectedIds = [];
                $(".employee-checkbox:checked").each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    var url = "{{ route('admin.hr.employee.card.print', [ 'employee_ids' => ':employee_ids']) }}";
                    var employee_ids = selectedIds.join(',');
                    url = url.replace(':employee_ids', employee_ids);

                    window.open(url, '_blank');
                } else {
                    toastr.error("Please select at least one Employee.");
                }

                /* Restore button after task done*/
                print_button.html(`<i class="fas fa-print"></i> Generate ID Card`);
                /* Enable button*/
                print_button.prop('disabled', false);

            }, 1000);
        });
            var table = $("#datatable1").DataTable();
            /* General form submission handler*/
            function handleFormSubmit(modalId, form) {
                $(modalId + ' form').submit(function(e) {
                    e.preventDefault();
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalBtnText = submitBtn.html();
                    submitBtn.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                        );
                    submitBtn.prop('disabled', true);

                    var formData = new FormData(this);
                    $.ajax({
                        type: $(this).attr('method'),
                        url: $(this).attr('action'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                table.ajax.reload(null, false);
                                $(modalId).modal('hide');
                                form[0].reset();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(field, messages) {
                                    $.each(messages, function(index, message) {
                                        toastr.error(message);
                                    });
                                });
                            } else {
                                toastr.error('An error occurred. Please try again.');
                            }
                        },
                        complete: function() {
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                        }
                    });
                });
            }



            /* Handle Delete button click and form submission*/
            $('#datatable1 tbody').on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                $('#deleteModal').modal('show');
                $("input[name*='id']").val(id);
            });

            $('#deleteModal form').submit(function(e) {
                e.preventDefault();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalBtnText = submitBtn.html();
                submitBtn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    );
                var form = $(this);
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#deleteModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseText);
                    },
                    complete: function() {
                        submitBtn.html(originalBtnText);
                    }
                });
            });
        });
    </script>


@endsection
