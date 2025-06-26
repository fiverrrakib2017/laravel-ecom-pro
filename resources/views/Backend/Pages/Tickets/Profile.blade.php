@extends('Backend.Layout.App')
@section('title', 'Dashboard | Ticket Profile | Admin Panel')
@section('style')

@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header ">
                    <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Ticket Profile - #TID-{{ $data->id }}</h3>

                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h5><strong>Customer Information</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $data->customer->fullname ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $data->customer->phone ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>POP/Branch</th>
                                    <td>{{ $data->pop->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Area</th>
                                    <td>{{ $data->area->name ?? '' }}</td>
                                </tr>
                            </table>

                            <h5 class="mt-4"><strong>Ticket Details</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Complain Type</th>
                                    <td>{{ $data->complain_type->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Assigned To</th>
                                    <td>Technician - {{ $data->assign->name ?? '' }}</td>
                                </tr>

                                <tr>
                                    <th>Priority</th>
                                    <td>
                                        @php
                                            $priorityLabel = '';
                                            $badgeColor = '';

                                            switch ($data->priority_id) {
                                                case 1:
                                                    $priorityLabel = 'Low';
                                                    $badgeColor = 'badge-secondary';
                                                    break;
                                                case 2:
                                                    $priorityLabel = 'Normal';
                                                    $badgeColor = 'badge-info';
                                                    break;
                                                case 3:
                                                    $priorityLabel = 'Standard';
                                                    $badgeColor = 'badge-primary';
                                                    break;
                                                case 4:
                                                    $priorityLabel = 'Medium';
                                                    $badgeColor = 'badge-warning';
                                                    break;
                                                case 5:
                                                    $priorityLabel = 'High';
                                                    $badgeColor = 'badge-danger';
                                                    break;
                                                case 6:
                                                    $priorityLabel = 'Very High';
                                                    $badgeColor = 'badge-dark';
                                                    break;
                                                default:
                                                    $priorityLabel = 'Unknown';
                                                    $badgeColor = 'badge-light';
                                                    break;
                                            }
                                        @endphp

                                        <span class="badge {{ $badgeColor }}">{{ $priorityLabel }}</span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($data->status == 0)
                                            <span class="badge badge-warning">Open</span>
                                        @else
                                            <span class="badge badge-success">Completed</span>
                                        @endif

                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h5><strong>Problem Description</strong></h5>
                            <div class="callout callout-danger">
                                {{ $data->note ?? 'N/A' }}
                            </div>

                            <h5 class="mt-4"><strong>Activity Timeline</strong></h5>

                            <div class="timeline">
                                <!-- timeline time label -->
                                <div class="time-label">
                                    <span
                                        class="bg-red">{{ \Carbon\Carbon::parse($data->created_at)->format('d M Y') }}</span>

                                </div>
                                <!-- /.timeline-label -->

                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-ticket-alt bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                        <h3 class="timeline-header">Ticket Created</h3>
                                        <div class="timeline-body">
                                            Customer submitted a ticket for {{ $data->complain_type->name ?? '' }}.
                                        </div>
                                    </div>
                                </div>

                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-user-check bg-warning"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                        <h3 class="timeline-header">Assigned to {{ $data->assign->name ?? '' }}</h3>
                                        <div class="timeline-body">
                                            Ticket assigned to technician <b>{{ $data->assign->name ?? '' }}</b> for
                                            investigation.
                                        </div>
                                    </div>
                                </div>

                                <!-- timeline item -->
                                @foreach ($ticekts_details as $item)
                                    @if ($item->status == 'visit')
                                        <div>
                                            <i class="fas fa-map-marker-alt bg-info"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="far fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                                <h3 class="timeline-header">Visited Customer Location</h3>
                                                <div class="timeline-body">
                                                    Technician <b>{{ $data->assign->name ?? '' }}</b> visited the customer
                                                    premises for issue inspection.
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($item->status == 'process')
                                        <div>
                                            <i class="fas fa-tools bg-primary"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="far fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                                <h3 class="timeline-header">Work in Progress</h3>
                                                <div class="timeline-body">
                                                    Technician <b>{{ $data->assign->name ?? '' }}</b> is currently working
                                                    on resolving the issue.
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($item->status == 'completed')
                                        <div>
                                            <i class="fas fa-check-circle bg-success"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="far fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                                <h3 class="timeline-header">Ticket Completed</h3>
                                                <div class="timeline-body">
                                                    The ticket has been successfully resolved and closed.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                            </div>


                            <div class="mt-4">
                                {{-- <button type="button" class="btn btn-warning"><i class="fas fa-check-circle"></i> Close Ticket</button> --}}
                                
                                <button type="button"  data-toggle="modal" data-target="#addModal" class="btn btn-success"> <i class="fas fa-plus-circle"></i> Add Activity</button>

                                <button type="button" onclick="history.back()" class="btn btn-danger"><i class="fas fa-arrow-left"></i> Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-lg" id="addModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <h5 class="modal-title" id="complainModalLabel"><span class="mdi mdi-account-check mdi-18px"></span>
                        &nbsp;New Activity </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.tickets.add_ticekts_activity')}}" id="activityForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" id="statusSelect" required>
                                <option value="">---Select---</option>
                                <option value="visit">Visit</option>
                                <option value="process">Process</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label>Title Name</label>
                            <input type="text" name="ticket_id" class="d-none" value="{{$data->id}}">
                            <input type="text" name="customer_id" class="d-none" value="{{$data->customer_id}}">
                            <input name="title" id="titleField" placeholder="Enter Title" class="form-control" type="text" required>
                        </div>
                        <div class="form-group mb-2">
                            <label>Description</label>
                            <textarea name="description"  id="descriptionField" placeholder="Enter Description" class="form-control" required></textarea>
                        </div>

                        <div class="form-group clearfix mb-3">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="sendMessageCheckbox" name="send_message" value="1">
                                <label for="sendMessageCheckbox">
                                    Send message to the Customer
                                </label>
                            </div>
                        </div>
                         <div class="form-group mb-2 message_area d-none">
                            <label>Message</label>
                            <textarea name="message" id="messageBox" placeholder="Enter Message" class="form-control" value="প্রিয় {{$data->customer->fullname?? ''}}, আপনার টিকিট (#{{$data->id ?? ''}}) গ্রহণ করা হয়েছে। আমরা খুব দ্রুত আপনার সাথে যোগাযোগ করব।"></textarea>
                        </div>

                        <div class="modal-footer ">
                            <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        __handleSubmit('#activityForm', '#addModal');

        function __handleSubmit(formSelector, modalSelector) {
            $(formSelector).submit(function(e) {
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
                            /* Hide the modal */
                            $(modalSelector).modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                            form.find(':input').prop('disabled', false);
                        }else if(response.success == false){
                            toastr.error(response.message || response.error);
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
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        form.find(':input').prop('disabled', false);
                    }
                });
            });
        }
        $('#sendMessageCheckbox').on('change', function () {
        if ($(this).is(':checked')) {
            $('.message_area').removeClass('d-none');
        } else {
            $('.message_area').addClass('d-none');
            $('#messageBox').val('');
        }
    });

    // Auto fill based on status
    $('#statusSelect').on('change', function () {
        const status = $(this).val();
        let customerName = @json($data->customer->fullname ?? '');
        let ticketId = @json($data->id ?? '');

        let title = '';
        let description = '';
        let message = '';

        switch (status) {
            case 'visit':
                title = "Technician Assigned for Visit";
                description = "Technician has been assigned and is on the way to customer's location.";
                message = `প্রিয় ${customerName}, আপনার টিকিট (#${ticketId}) এর জন্য একজন টেকনিশিয়ান প্রেরণ করা হয়েছে।`;
                break;

            case 'process':
                title = "Ticket in Process";
                description = "Work is ongoing to resolve the reported issue.";
                message = `প্রিয় ${customerName}, আপনার টিকিট (#${ticketId}) প্রক্রিয়াধীন রয়েছে। আমরা খুব দ্রুত সমস্যাটি সমাধান করব।`;
                break;

            case 'completed':
                title = "Ticket Resolved";
                description = "The reported issue has been resolved and ticket is marked completed.";
                message = `প্রিয় ${customerName}, আপনার টিকিট (#${ticketId}) সফলভাবে সমাধান করা হয়েছে। ধন্যবাদ আমাদের সাথে থাকার জন্য।`;
                break;
        }

        // Autofill the fields
        $('#titleField').val(title);
        $('#descriptionField').val(description);
        $('#sendMessageCheckbox').prop('checked', true);
        $('.message_area').removeClass('d-none');
        $('#messageBox').val(message);
    });

    });

</script>
@endsection
