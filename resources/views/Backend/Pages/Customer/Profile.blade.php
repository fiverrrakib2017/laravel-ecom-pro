@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>

    </style>
@endsection

@section('content')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <!-- Buttons -->
                <div class="col-md-12 d-flex flex-wrap gap-2">
                    <button class="btn btn-success m-1" data-toggle="modal" data-target="#CustomerRechargeModal"><i
                            class="fas fa-bolt"></i> Recharge Now</button>
                    <button class="btn btn-dark m-1" data-toggle="modal" data-target="#ticketModal"><i
                            class="fas fa-ticket-alt"></i> Add Ticket</button>
                    <button type="submit" name="customer_re_connect_btn" class="btn btn-warning m-1"
                        data-id="{{ $data->id }}"><i class="fas fa-undo-alt"></i> Ree-Connect</button>

                    <!--------Customer Disable And Enable Button--------->
                    @if (in_array($data->status, ['disabled', 'offline', 'online']))
                        <button type="button"
                            class="btn btn-{{ in_array($data->status, ['disabled', 'offline']) ? 'success' : 'danger' }} m-1 change-status"
                            data-id="{{ $data->id }}" data-username="{{ $data->username }}">
                            <i class="fas fa-user-lock"></i>
                            {{ in_array($data->status, ['disabled', 'offline']) ? 'Enable' : 'Disable' }} This User
                        </button>
                    @endif



                    <button type="button" class="btn btn-sm btn-primary m-1 customer_edit_btn"
                        data-id="{{ $data->id }}"><i class="fas fa-edit"></i> Edit Profile</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-danger card-outline shadow-sm">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img src="{{ asset($data->photo ?? 'Backend/images/avatar.png') }}" alt="Profile Picture"
                                    class="profile-user-img img-fluid img-circle border border-primary">
                            </div>

                            <h3 class="profile-username text-center mt-2">{{ $data->fullname ?? 'N/A' }}</h3>
                            <p class="text-muted text-center">
                                <i class="fas fa-user-tag"></i> User ID: {{ $data->id ?? 'N/A' }}
                            </p>
                            @php
                                $expireDate = $data->expire_date;
                                $today_date = date('Y-m-d');

                                $isExpired = $expireDate && strtotime($today_date) > strtotime($expireDate);

                                $formattedDate = $expireDate ? date('d M Y', strtotime($expireDate)) : 'N/A';
                            @endphp

                            <p class="text-muted text-center">
                                <i class="fas fa-calendar-alt"></i>
                                <strong>Expire Date:</strong>
                                <span class="{{ $isExpired ? 'text-danger' : 'text-success' }}">
                                    {{ $isExpired ? 'Expired' : $formattedDate }}
                                </span>
                            </p>




                            <p class="text-muted text-center">
                                @php
                                    $icon = '';
                                    $statusText = $data->status ?? 'N/A';
                                    $badgeColor = 'secondary';

                                    switch ($data->status) {
                                        case 'online':
                                            $icon = 'fas fa-unlock text-success';
                                            $badgeColor = 'success';
                                            break;
                                        case 'offline':
                                            $icon = 'fas fa-times-circle text-danger';
                                            $badgeColor = 'danger';
                                            break;
                                        case 'active':
                                            $icon = 'fas fa-user-circle text-primary';
                                            $badgeColor = 'primary';
                                            break;
                                        case 'blocked':
                                            $icon = 'fas fa-ban text-warning';
                                            $badgeColor = 'warning';
                                            break;
                                        case 'expired':
                                            $icon = 'fas fa-clock text-secondary';
                                            $badgeColor = 'secondary';
                                            break;
                                        case 'disabled':
                                            $icon = 'fas fa-lock text-danger';
                                            $badgeColor = 'danger';
                                            break;
                                        default:
                                            $icon = 'fas fa-question-circle text-muted';
                                            $badgeColor = 'secondary';
                                            break;
                                    }
                                @endphp
                                <i class="{{ $icon }}"></i> <span
                                    class="badge badge-{{ $badgeColor }}">{{ ucfirst($statusText) }}</span>
                            </p>
                            <hr>
                            <!-- Additional Information -->
                            <div class="card card-primary card-outline shadow-sm">

                                <div class="card-body">

                                    <div class="row mb-3">
                                        <div class="col-md-6 text-center border-end">
                                            <p class="mb-1"><i class="fas fa-clock text-warning fa-lg"></i></p>
                                            <strong>Up Time</strong>
                                            <p class="text-dark"><span id="customer_uptime">0.00</span></p>
                                        </div>


                                        <div class="col-md-6 text-center">
                                            <p class="mb-1"><i class="fas fa-chart-line text-success"></i></p>
                                            <strong>Monthly Usage</strong>
                                            <p class="text-primary">{{ $mikrotik_data['monthly_usage'] ?? 'N/A' }} MB</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 text-center border-right">
                                            <p class="mb-1"><i class="fas fa-arrow-up text-success"></i></p>
                                            <strong>Upload</strong>
                                            <p class="text-danger"><span id="customer_upload_speed"></span> Mbps</p>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <p class="mb-1"><i class="fas fa-arrow-down text-danger"></i></p>
                                            <strong>Download</strong>
                                            <p class="text-success"><span id="customer_download_speed"></span> Mbps
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 text-center border-right">
                                            <p class="mb-1"><i class="fas fa-plug text-info"></i></p>
                                            <strong>Interface</strong>
                                            <p class="text-muted"><span id="customer_interface">N/A</span></p>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <p class="mb-1"><i class="fas fa-address-card text-warning"></i></p>
                                            <strong>MAC Address</strong>
                                            <p class="text-muted">{{ $mikrotik_data['mac'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 text-center border-right">
                                            <p class="mb-1"><i class="fas fa-laptop-code text-secondary"></i></p>
                                            <strong>IP Address</strong>
                                            <p class="text-muted">{{ $mikrotik_data['ip'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <p class="mb-1"><i class="fas fa-route text-success"></i></p>
                                            <strong>Router Used</strong>
                                            <p class="text-muted">{{ $data->router->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <div class="card  shadow-sm">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-user"></i> Customer Information</h5>
                                </div>
                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-user-alt text-primary mr-2"></i> <strong>Username:</strong>
                                        </div>
                                        <span class="badge badge-primary badge-pill">{{ $data->username ?? 'N/A' }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-phone-alt text-success mr-2"></i> <strong>Phone:</strong>
                                        </div>
                                        <span class="badge badge-success badge-pill">{{ $data->phone ?? 'N/A' }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-map-marker-alt text-info mr-2"></i> <strong>Address:</strong>
                                        </div>
                                        <span class="badge badge-info badge-pill">{{ $data->address ?? 'N/A' }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-building text-warning mr-2"></i> <strong>POP Branch:</strong>
                                        </div>
                                        <span
                                            class="badge badge-warning badge-pill">{{ $data->pop->name ?? 'N/A' }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-map text-danger mr-2"></i> <strong>Area:</strong>
                                        </div>
                                        <span
                                            class="badge badge-danger badge-pill">{{ $data->area->name ?? 'N/A' }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-network-wired text-secondary mr-2"></i>
                                            <strong>Package:</strong>
                                        </div>
                                        <span
                                            class="badge badge-secondary badge-pill">{{ $data->package->name ?? 'N/A' }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-dollar-sign text-primary mr-2"></i> <strong>Monthly
                                                Charge:</strong>
                                        </div>
                                        <span class="badge badge-primary badge-pill">{{ number_format($data->amount, 2) }}
                                            ৳</span>
                                    </li>



                                </ul>
                            </div>

                        </div>
                    </div>
                </div>









                <div class="col-md-8">

                    <div class="row">
                        @php
                            $dashboardCards = [
                                [
                                    'id' => 1,
                                    'title' => 'Recharged',
                                    'value' => $total_recharged,
                                    'bg' => 'success',
                                    'icon' => 'fa-arrow-up',
                                ],
                                [
                                    'id' => 2,
                                    'title' => 'Total Paid',
                                    'value' => $totalPaid,
                                    'bg' => 'info',
                                    'icon' => 'fa-credit-card',
                                ],
                                [
                                    'id' => 3,
                                    'title' => 'Total Due',
                                    'value' => $totalDue,
                                    'bg' => 'danger',
                                    'icon' => 'fa-hand-holding-usd',
                                ],
                                // [
                                //     'id' => 4,
                                //     'title' => 'Due Paid',
                                //     'value' => $duePaid,
                                //     'bg' => 'warning',
                                //     'icon' => 'fa-check-circle',
                                // ],
                            ];
                        @endphp
                        @foreach ($dashboardCards as $card)
                            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                                <div class="small-box bg-{{ $card['bg'] }} shadow-lg rounded">
                                    <div class="inner">
                                        <h3>{{ $card['value'] }}</h3>
                                        <p>{{ $card['title'] }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas {{ $card['icon'] }} fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#tickets"
                                        data-toggle="tab">Tickets</a></li>
                                <li class="nav-item"><a class="nav-link" href="#recharge" data-toggle="tab">Recharge
                                        History</a></li>
                                <li class="nav-item"><a class="nav-link" href="#onu_details" data-toggle="tab">Onu
                                        Information</a></li>

                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Tickets -->
                                <div class="active tab-pane" id="tickets">
                                    <div class="table-responsive">
                                        @include('Backend.Component.Tickets.Tickets', [
                                            'customer_id' => $data->id,
                                        ])
                                    </div>
                                </div>
                                <!-- Customer Recharge Section  -->
                                <div class="tab-pane" id="recharge">
                                    <div class="table table-responsive">
                                        <table id="recharge_datatable"  class="table table-bordered dt-responsive nowrap"style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Months</th>
                                                    <th>Type</th>
                                                    <th>Remarks</th>
                                                    <th>Paid until</th>
                                                    <th>Amount</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total_recharge_data = App\Models\Customer_recharge::where(
                                                        'customer_id',
                                                        $data->id,
                                                    )
                                                        ->latest()
                                                        ->get();

                                                    // echo '<pre>';
                                                    //  print_r($total_recharge_data->toArray());
                                                    // echo '</pre>';
                                                @endphp
                                                @foreach ($total_recharge_data as $item)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                                        </td>
                                                        <td>{{ $item->recharge_month }}</td>
                                                        <td>
                                                            @if ($item->transaction_type == 'cash')
                                                                <span
                                                                    class="badge bg-success">{{ ucfirst($item->transaction_type) }}</span>
                                                            @elseif($item->transaction_type == 'credit')
                                                                <span
                                                                    class="badge bg-danger">{{ ucfirst($item->transaction_type) }}</span>
                                                            @elseif($item->transaction_type == 'due_paid')
                                                                <span
                                                                    class="badge bg-success">{{ ucfirst($item->transaction_type) }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-danger">{{ ucfirst($item->transaction_type) }}</span>
                                                            @endif
                                                        </td>

                                                        <td>{{ ucfirst($item->note) }}</td>
                                                        <td>
                                                            @if ($item->paid_until)
                                                                {{ \Carbon\Carbon::parse($item->paid_until)->format('d M Y') }}
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>


                                                        <td>{{ number_format($item->amount, 2) }} BDT</td>
                                                        <td>
                                                            <button
                                                                class="btn btn-danger btn-sm customer_recharge_undo_btn"
                                                                data-id="{{ $item->id }}">
                                                                <i class="fas fa-undo"></i></button>
                                                            {{-- @if ($item->transaction_type == 'credit')
                                                                <button class="btn btn-info btn-sm credit_recharge_btn" data-id="{{ $item->id }}"> <i class="fas fa-check-circle"></i> </button>
                                                            @endif --}}


                                                            <button
                                                                class="btn btn-success btn-sm customer_recharge_print_btn"
                                                                data-id="{{ $item->id }}"><i
                                                                    class="fas fa-print"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Customer ONU Section -->
                                <div class="tab-pane fade show " id="onu_details" role="tabpanel">
                                    <div class="container px-0">
                                        <div class=" ">

                                            <div class="card-body ">
                                                <div class="row g-4">
                                                    <!-- Single Card -->
                                                    <div class="col-md-4">
                                                        <div class="card h-100 shadow-sm border-0">
                                                            <div class="card-body">
                                                                <h6 class="card-title text-muted">OLT Name</h6>
                                                                <p class="card-text fw-bold">---</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="card h-100 shadow-sm border-0">
                                                            <div class="card-body">
                                                                <h6 class="card-title text-muted">MAC Address</h6>
                                                                <p class="card-text fw-bold">---</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="card h-100 shadow-sm border-0">
                                                            <div class="card-body">
                                                                <h6 class="card-title text-muted">PON ID / VLAN</h6>
                                                                <p class="card-text fw-bold">---</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="card h-100 shadow-sm border-0">
                                                            <div class="card-body">
                                                                <h6 class="card-title text-muted">Last Update</h6>
                                                                <p class="card-text fw-bold">---</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="card h-100 shadow-sm border-0">
                                                            <div class="card-body">
                                                                <h6 class="card-title text-muted">Power (dBm)</h6>
                                                                <p class="card-text fw-bold">---</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="card h-100 shadow-sm border-0">
                                                            <div class="card-body">
                                                                <h6 class="card-title text-muted">Distance (Km)</h6>
                                                                <p class="card-text fw-bold">---</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>
                    <div class="card mt-4 card-dark">
                        <div class="card-header">
                            Bandwidth Usage (Current Session)
                        </div>
                        <div class="card-body">
                            <canvas id="liveBandwidthChart" height="100"></canvas>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
    @include('Backend.Modal.Customer.Recharge.Recharge_modal')
    @include('Backend.Modal.Tickets.ticket_modal', [
        'customer_id' => $data->id,
        'pop_id' => $data->pop_id,
        'area_id' => $data->area_id,
    ])
    @include('Backend.Modal.Customer.customer_modal')
    @include('Backend.Modal.delete_modal')
@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#recharge_datatable").DataTable({
                "responsive": true,
                "autoWidth": false,
                "lengthMenu": [10, 25, 50, 100],
                "language": {
                    "emptyTable": "No recharge data available",
                    "zeroRecords": "No matching records found"
                },
                "order": [[0, 'desc']],
            });
            /************** Customer Enable And Disabled Start**************************/
            $(document).on("click", ".change-status", function() {
                let id = $(this).data('id');
                let username = $(this).data('username');
                let btn = $(this);
                let originalHtml = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop("disabled", true);
                $.ajax({
                    url: "{{ route('admin.customer.change_status') }}",
                    type: "POST",
                    data: {
                        username: username,
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success == true) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                        if (response.success == false) {
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
            /** Handle Customer Undo Recharge button click **/
            $(document).on('click', '.customer_recharge_undo_btn', function() {
                if (confirm('Are you sure you want to undo this action?')) {
                    var id = $(this).data('id');
                    var button = $(this);
                    var row = button.closest('tr');
                    var originalContent = button.html();
                    button.html('<i class="fas fa-spinner fa-spin"></i> Undoing...').prop('disabled', true);
                    $.ajax({
                        url: "{{ route('admin.customer.recharge.undo', ':id') }}".replace(':id',
                            id),
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                    toastr.success('Successfully Undo!');
                                });
                            }
                            if (response.success == false) {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('An error occurred. Please try again.');
                        },
                        complete: function() {
                            button.html('Recharge Now').prop('disabled', false);
                        }
                    });
                }
            });
            /** Handle Customer Recharge Print click **/
            $(document).on('click', '.customer_recharge_print_btn', function() {
                var id = $(this).data('id');
                var button = $(this);
                var row = button.closest('tr');
                var originalContent = button.html();

                /* Show loading*/
                button.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

                /* Wait for 1 second before making the Ajax call*/
                setTimeout(() => {
                    var url = "{{ route('admin.customer.recharge.print', ':id') }}".replace(':id',
                        id);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            if (response.success == false) {
                                toastr.error(response.message);
                                return;
                            }
                            if (response.success == true) {
                                var myWindow = window.open('', 'PrintWindow',
                                    'height=500,width=400');
                                myWindow.document.write(
                                    '<html><head><title>Print Slip</title>');
                                myWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">');
                                myWindow.document.write('<style>body { font-family: "Poppins", sans-serif; font-size: 12px; text-align: center; }</style>');
                                myWindow.document.write('</head><body>');
                                myWindow.document.write(response.html);
                                myWindow.document.write('</body></html>');
                                myWindow.document.close();
                                myWindow.focus();
                                myWindow.print();
                                myWindow.close();
                            }
                        },
                        error: function() {
                            toastr.error('Could not load print slip.');
                        },
                        complete: function() {
                            button.html(originalContent).prop('disabled', false);
                        }
                    });
                }, 1000);
            });

            /** Customer Re-connect button click **/
            $(document).on('click', 'button[name="customer_re_connect_btn"]', function() {
                if (confirm('Are you sure you want to undo this action?')) {
                    var id = $(this).data('id');
                    let button = $(this);
                    button.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Reconnecting...');
                    $.ajax({
                        url: "{{ route('admin.customer.mikrotik.reconnect', ':id') }}".replace(
                            ':id', id),
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        },
                        error: function() {
                            toastr.error('An error occurred. Please try again.');
                        },
                        complete: function() {
                            button.html('<i class="fas fa-undo-alt"> Ree-Connect').prop(
                                'disabled', false);
                        }
                    });
                }
            });
        });
        /************** Customer Bandwidth Graph Start **************************/
        const ctx = document.getElementById('liveBandwidthChart').getContext('2d');

        const labels = Array.from({
            length: 30
        }, () => '');
        const downloadData = Array(30).fill(0);
        const uploadData = Array(30).fill(0);

        const bandwidthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Download (kbps)',
                        data: downloadData,
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'Upload (kbps)',
                        data: uploadData,
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.4,
                    }
                ]
            },
            options: {
                responsive: true,
                animation: false,
                scales: {
                    x: {
                        display: false,
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: 1000,
                    }
                }
            }
        });

        function fetch_live_bandwidth_data() {
            $.ajax({
                url: "{{ route('admin.customer.live_bandwith_update', ':id') }}".replace(':id',
                    "{{ $data->id }}"),
                method: 'GET',
                success: function(response) {
                    // console.log(response);
                    if (response.success) {
                        const downloadSpeed = response.rx_mb;
                        const uploadSpeed = response.tx_mb;
                        const user_uptime = response.uptime;
                        const user_interface_name = response.interface_name;

                        // Update graph data with new point (slide effect)
                        downloadData.push(downloadSpeed);
                        downloadData.shift();

                        uploadData.push(uploadSpeed);
                        uploadData.shift();

                        bandwidthChart.update();

                        // Update Client Data
                        $("#customer_upload_speed").html(uploadSpeed);
                        $("#customer_download_speed").html(downloadSpeed);
                        $("#customer_uptime").html(user_uptime);
                        $("#customer_interface").html($('<div>').text(user_interface_name).html());
                    }
                }
            });
        }

        fetch_live_bandwidth_data();
        setInterval(fetch_live_bandwidth_data, 1000);

        /************** Customer Bandwidth Graph End **************************/
        $.ajax({
            url: "{{ route('admin.customer.get_onu_info') }}",
            type: "POST",
            data: {
                mac_address: "{{ $mikrotik_data['mac'] ?? 'N/A' }}",
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);
            },
            error: function() {
                console.error("Something went wrong!");
            },
        });
    </script>

@endsection
