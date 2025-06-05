@php
    $customer_id = $customer_id ?? null;
    $pop_id = $pop_id ?? null;
    $area_id = $area_id ?? null;
@endphp

<div class="modal fade bs-example-modal-lg" id="ticketModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content col-md-12">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">
                    <span class="mdi mdi-account-check mdi-18px"></span> &nbsp;Create Ticket
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tickets.store') }}" method="POST" id="ticketForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Customer Name</label>
                            <select name="customer_id" class="form-select" type="text" style="width: 100%;" required>
                                <option value="">---Select---</option>
                                @php
                                    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                    if ($branch_user_id != null) {
                                        $customers = \App\Models\Customer::where('pop_id', $branch_user_id)->latest()->get();
                                    } else {
                                        $customers = \App\Models\Customer::latest()->get();
                                    }
                                @endphp
                                @if ($customers->isNotEmpty())
                                    @foreach ($customers as $item)
                                        @php
                                            $status_icon = $item->status == 'online' ? '🟢' : '🔴';
                                        @endphp
                                        <option value="{{ $item->id }}" {{ (isset($customer_id) && $item->id == $customer_id) ? 'selected' : '' }} > {!! $status_icon !!} [{{ $item->id }}] -
                                            {{ $item->username }} || ({{ $item->phone }})</option>
                                    @endforeach

                                @endif
                            </select>

                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Ticket For</label>
                            <select name="ticket_for" class="form-select" type="text" style="width: 100%;" required>
                                <option value="">---Select---</option>
                                <option value="1">Default </option>
                            </select>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Ticket Assign</label>
                            <select name="ticket_assign_id" class="form-select" type="text" style="width: 100%;"
                                required>
                                <option value="">---Select---</option>
                                @php
                                $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                if ($branch_user_id != null) {
                                    $tickets_assign = \App\Models\Ticket_assign::where('pop_id', $branch_user_id)->latest()->get();
                                } else {
                                    $tickets_assign = \App\Models\Ticket_assign::latest()->get();
                                }
                                @endphp
                                @if ($tickets_assign->isNotEmpty())
                                    @foreach ($tickets_assign as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach

                                @endif
                            </select>

                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Complain Type</label>
                            <select name="ticket_complain_id" class="form-select" type="text" style="width: 100%;"
                                required>
                                <option value="">---Select---</option>
                                @php
                                    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                                    if ($branch_user_id != null) {
                                        $tickets_complain = \App\Models\Ticket_complain_type::where('pop_id', $branch_user_id)->latest()->get();
                                    } else {
                                        $tickets_complain = \App\Models\Ticket_complain_type::latest()->get();
                                    }
                                @endphp
                                @if ($tickets_complain->isNotEmpty())
                                    @foreach ($tickets_complain as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach

                                @endif
                            </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Priority</label>
                            <select name="priority_id" class="form-select" type="text" style="width: 100%;" required>
                                <option value="">---Select---</option>
                                <option value="1">Low</option>
                                <option value="2">Normal</option>
                                <option value="3">Standard</option>
                                <option value="4">Medium</option>
                                <option value="5">High</option>
                                <option value="6">Very High</option>
                            </select>

                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Ticket Status</label>
                            <select name="status_id" class="form-select" type="text" style="width: 100%;" required>
                                <option value="0" selected>Active</option>
                                <option value="1">Completed</option>
                            </select>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Percentage</label>
                            <select name="percentage" class="form-select" type="text" style="width: 100%;" required>
                                <option value="0%">0%</option>
                                <option value="15%">15%</option>
                                <option value="25%">25%</option>
                                <option value="35%">35%</option>
                                <option value="45%">45%</option>
                                <option value="55%">55%</option>
                                <option value="65%">65%</option>
                                <option value="75%">75%</option>
                                <option value="85%">85%</option>
                                <option value="95%">95%</option>
                                <option value="100%">100%</option>
                            </select>

                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Note</label>
                            <input name="note" class="form-control" type="text" placeholder="Enter Note" />

                        </div>
                        <div class="col-md-6 mb-2">

                        </div>
                        <!-- Previous Tickets Table -->
                    <div class="mt-3 col-md-12 mb-2 d-none" id="previous_tickets">
                        <h6>Previous Tickets</h6>
                        <table class="table table-bordered"  id="customer_tickets_table">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Issue</th>
                                    <th>Priority</th>
                                    <th>Percentage</th>
                                    <th>Acctual Work</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="customer_tickets">
                                <tr >
                                    <td colspan="6" class="text-center">No Tickets Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
<script src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>
<script src="{{ asset('Backend/assets/js/custom_select.js') }}"></script>
<script type="text/javascript">
    __handleSubmit('#ticketForm', '#ticketModal');

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
                        $('#datatable1').DataTable().ajax.reload(null, false);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        form.find(':input').prop('disabled', false);
                    }else if(response.success == false){
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
    $(document).ready(function() {
        /** Handle Edit button click **/
        $(document).on('click', '.tickets_edit_btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.tickets.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#ticketForm').attr('action',
                            "{{ route('admin.tickets.update', ':id') }}".replace(':id',
                                id));
                        $('#ticketModalLabel').html(
                            '<span class="mdi mdi-account-edit mdi-18px"></span> &nbsp;Edit Ticket'
                        );
                        $('#ticketForm select[name="customer_id"]').val(response.data
                            .customer_id).trigger('change');
                        $('#ticketForm select[name="ticket_for"]').val(response.data
                            .ticket_for).trigger('change');
                        $('#ticketForm select[name="ticket_assign_id"]').val(response.data
                            .ticket_assign_id).trigger('change');
                        $('#ticketForm select[name="ticket_complain_id"]').val(response.data
                            .ticket_complain_id).trigger('change');
                        $('#ticketForm select[name="priority_id"]').val(response.data
                            .priority_id).trigger('change');

                        $('#ticketForm input[name="note"]').val(response.data.note);
                        $('#ticketForm select[name="status_id"]').val(response.data.status)
                            .trigger('change');
                        $('#ticketForm select[name="percentage"]').val(response.data
                            .percentage).trigger('change');

                        // Show the modal
                        $('#ticketModal').modal('show');
                    } else {
                        toastr.error('Failed to fetch  data.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });

        /** Handle Delete button click**/
        $(document).on('click', '.tickets_delete_btn', function() {
            var id = $(this).data('id');
            var deleteUrl = "{{ route('admin.tickets.delete', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').find('input[name="id"]').val(id);
            $('#deleteModal').modal('show');
        });
        /** Handle Completed button click**/
        $(document).on("click", ".tickets_completed_btn", function() {
            let id = $(this).data("id");
            let btn = $(this);
            let originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop("disabled", true);
            $.ajax({
                url: "{{ route('admin.tickets.change_status', '') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        btn.html(originalHtml).prop("disabled", false);
                        toastr.success(response.message);
                        $('#datatable1').DataTable().ajax.reload(null, false);
                    } else if (response.success == false) {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error("Something went wrong!");
                },
                complete: function() {
                    btn.prop("disabled", false);
                }
            });
        });
        /*GET Customer Ticket*/
        $(document).on('change','#ticketForm select[name="customer_id"]',function(){
            var customer_id = $(this).val();
            if (customer_id) {
                $.ajax({
                    url: "{{ route('admin.tickets.get_customer_tickets', '') }}/" + customer_id,
                    type: "GET",
                    data: { get_customer_tickets: true, customer_id: customer_id },
                    dataType:'json',
                    success: function (response) {
                        if (response.success == true) {
                            let tickets = response.data;
                            let tableBody = $("#customer_tickets_table tbody");
                            tableBody.empty();
                            if (tickets.length > 0) {
                                $("#previous_tickets").removeClass('d-none');
                                tickets.forEach((ticket, index) => {
                                    switch (ticket.priority_id) {
                                        case 1:
                                            priorityLabel = 'Low';
                                            badgeColor = 'badge-secondary';
                                            break;
                                        case 2:
                                            priorityLabel = 'Normal';
                                            badgeColor = 'badge-info';
                                            break;
                                        case 3:
                                            priorityLabel = 'Standard';
                                            badgeColor = 'badge-primary';
                                            break;
                                        case 4:
                                            priorityLabel = 'Medium';
                                            badgeColor = 'badge-warning';
                                            break;
                                        case 5:
                                            priorityLabel = 'High';
                                            badgeColor = 'badge-danger';
                                            break;
                                        case 6:
                                            priorityLabel = 'Very High';
                                            badgeColor = 'badge-dark';
                                            break;
                                        default:
                                            priorityLabel = 'Unknown';
                                            badgeColor = 'badge-light';
                                            break;
                                    }

                                    /*get Acctual Work*/
                                    var acctual_work='';
                                    if (ticket.updated_at == ticket.created_at) {
                                        acctual_work= 'N/A';
                                    }
                                    if (ticket.updated_at && ticket.created_at) {
                                        let start = moment(ticket.created_at);
                                        let end = moment(ticket.updated_at);

                                        acctual_work= end.from(start);
                                    } else {
                                        acctual_work= 'N/A';
                                    }
                                    /*GET Ticket Status*/
                                    var ticket_status       ='';
                                    if (ticket.status       == 0) {
                                        ticket_status       = '<span class="badge bg-danger">Active</span>';
                                    } else if (ticket.status   == 2) {
                                        ticket_status       ='<span class="badge bg-warning">Pending</span>';
                                    } else if (ticket.status   == 1) {
                                        ticket_status       ='<span class="badge bg-success">Completed</span>';
                                    }
                                    let row = `
                                        <tr class="wow animate__animated animate__fadeInUp animate__delay-${index  * 0.1}s ">
                                            <td>${ticket.id}</td>
                                            <td>${ticket.complain_type.name}</td>
                                            <td><span class="badge ${badgeColor}" style="font-size: 80%;">${priorityLabel}</span></td>
                                            <td>${ticket.percentage}</td>
                                            <td>${acctual_work} </td>
                                            <td>${ticket_status}</td>
                                        </tr>`;
                                    tableBody.append(row);
                                });
                            } else {
                                tableBody.append(`<tr><td colspan="6" class="text-center text-danger">No tickets found</td></tr>`);
                            }
                        }
                    }
                });
            }
        });
    });
</script>
