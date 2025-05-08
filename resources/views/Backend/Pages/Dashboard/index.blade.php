@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .marquee-container {
            background: linear-gradient(90deg, #163b62, #015a29);
            color: #ffffff;
            font-size: 18px;
            font-weight: 600;
            padding: 15px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .marquee-container p {
            margin: 0;
            padding-left: 20px;
            white-space: nowrap;
        }

        .marquee-container p span {
            padding-right: 30px;
        }

        .marquee-container p i {
            margin-right: 10px;
        }

        /* Smooth animation */
        .marquee-container marquee {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 18px;
            color: #ffffff;
        }

        /*Drop Down BUtton css*/
        .custom-dropdown-menu {
            min-width: 220px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            border: none;
        }

        .custom-dropdown-menu .dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            font-weight: 500;
            transition: background 0.1s ease-in-out;
            border-bottom: 3px dotted rgb(195 195 195);
        }

        .custom-dropdown-menu .dropdown-item i {
            margin-right: 10px;
            font-size: 16px;

        }

        .custom-dropdown-menu .dropdown-item:hover {
            background: #d1d2d3;
            color: rgb(0, 0, 0);
        }
    </style>
@endsection
@section('content')
    <div class="row mb-3">
        <!-- Marquee above the buttons -->
        <div class="col-md-12">
            <div class="marquee-container">
                <marquee behavior="scroll" direction="left" scrollamount="8">
                    <span><i class="fas fa-broadcast-tower"></i> স্বাগতম, Admin Panel এ! <i class="fas fa-cogs"></i> আপনার ISP
                        বিলিং সিস্টেম পরিচালনা করুন, সহায়তা দরকার হলে আমাদের সাপোর্ট টিমের সাথে যোগাযোগ করুন | নতুন ফিচার
                        আসছে!</span>
                </marquee>
            </div>
        </div>
        <!-- Buttons -->
        <div class="col-md-12 d-flex flex-wrap gap-2">
            <button class="btn btn-success m-1" data-toggle="modal" data-target="#addCustomerModal" type="button"><i
                    class="fas fa-user-plus"></i> Add Customer</button>
            <button type="button" data-toggle="modal" data-target="#ticketModal" class="btn btn-info m-1"><i
                    class="fas fa-ticket-alt"></i> Add Ticket</button>
            <button type="button" data-toggle="modal" data-target="#addSendMessageModal"
                class="btn btn-primary text-white m-1"><i class="fas fa-envelope"></i> SMS Notification</button>
            <button class="btn btn-dark m-1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"><i class="fas fa-chart-line"></i> Reports</button>
            <div class="dropdown-menu custom-dropdown-menu">
                <a class="dropdown-item" href="{{ route('admin.customer.payment.history') }}">
                    <i class="fas fa-file-invoice-dollar text-success"></i> Payment History
                </a>
                <a class="dropdown-item" href="{{ route('admin.customer.customer_credit_recharge_list') }}">
                    <i class="fas fa-users text-primary"></i> Credit Recharge Report
                </a>
                <a class="dropdown-item" href="{{ route('admin.customer.log.index') }}">
                    <i class="fas fa-file-alt text-danger"></i> Customer Logs Report
                </a>


            </div>

            <button id="resetOrderBtn" class="btn btn-danger m-1"><i class="fas fa-undo"></i> Reset Card</button>
            @php
                $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
            @endphp

            @if (!empty($branch_user_id))
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Top Up Button -->
                    <button type="button" data-toggle="modal" data-target="#smsTopUpModal" class="btn btn-success m-1">
                        <i class="fas fa-plus-circle"></i> Top Up
                    </button>

                    <!-- Available SMS -->
                    <div class="d-flex align-items-center bg-light p-2 rounded m-1">
                        <i class="fas fa-comment-dots text-info me-2"></i>
                        <span class="text-dark">
                            <strong>Available SMS:</strong>
                            <strong class="text-danger fw-bold">
                                {{-- Replace 520 with dynamic SMS count --}}
                                {{ $available_sms ?? 520 }}
                            </strong>
                        </span>
                    </div>

                    <!-- Remaining Account Balance -->
                    <div class="d-flex align-items-center bg-light p-2 rounded m-1">
                        <i class="fas fa-money text-success me-2"></i>
                        <span class="text-dark">
                            <strong>Remaining Balance TK:</strong>
                            <strong class="text-danger fw-bold">
                                @php
                                    /*Branch Transaction Current Balance*/
                                    $customer_recharge_total = App\Models\Customer_recharge::where(
                                        'pop_id',
                                        $branch_user_id,
                                    )
                                        ->where('transaction_type', '!=', 'due_paid')
                                        ->sum('amount');

                                    $branch_transaction_total = App\Models\Branch_transaction::where(
                                        'pop_id',
                                        $branch_user_id,
                                    )
                                        ->where('transaction_type', '!=', 'due_paid')
                                        ->sum('amount');

                                    $current_balance = $branch_transaction_total - $customer_recharge_total;
                                @endphp
                                {{ $current_balance ?? 00 }}
                            </strong>
                        </span>
                    </div>

                </div>
            @endif



        </div>
    </div>



    <div class="row" id="dashboardCards">
        @php
            $dashboardCards = [
                [
                    'id' => 1,
                    'title' => 'Online',
                    'value' => $online_customer,
                    'bg' => 'success',
                    'icon' => 'fa-user-check',
                ],
                [
                    'id' => 2,
                    'title' => 'Offline',
                    'value' => $offline_customer,
                    'bg' => 'info',
                    'icon' => 'fa-user-times',
                ],
                [
                    'id' => 3,
                    'title' => 'Active Customers',
                    'value' => $active_customer,
                    'bg' => 'primary',
                    'icon' => 'fa-users',
                ],
                [
                    'id' => 4,
                    'title' => 'Expired',
                    'value' => $expire_customer,
                    'bg' => 'danger',
                    'icon' => 'fa-user-clock',
                ],
                [
                    'id' => 5,
                    'title' => 'Disabled',
                    'value' => $disable_customer,
                    'bg' => 'warning',
                    'icon' => 'fa-user-lock',
                ],
                ['id' => 6, 'title' => 'Requests', 'value' => 0, 'bg' => 'dark', 'icon' => 'fa-user-edit'],

                [
                    'id' => 7,
                    'title' => 'Area',
                    'value' => $total_area,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-map-marker-alt',
                ],
                [
                    'id' => 8,
                    'title' => 'Total Tickets',
                    'value' => $tickets,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-ticket-alt',
                ],
                [
                    'id' => 9,
                    'title' => 'Completed Tickets',
                    'value' => $ticket_completed,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-check-circle',
                ],
                [
                    'id' => 10,
                    'title' => 'Pending Tickets',
                    'value' => $ticket_pending,
                    'bg' => 'danger',
                    'icon' => 'fas fa-solid fa-exclamation-triangle',
                ],
            ];
        @endphp
        @foreach ($dashboardCards as $card)
            <div class="col-lg-3 col-6 card-item wow animate__animated animate__fadeInUp" data-id="{{ $card['id'] }}"
                data-wow-delay="0.{{ $card['id'] }}s">
                <div class="small-box bg-{{ $card['bg'] }}">
                    <div class="inner">
                        <h3>{{ $card['value'] }}</h3>
                        <p>{{ $card['title'] }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas {{ $card['icon'] }} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($branch_user_id == null)
    <div class="row">
        <div class="col-lg-3 col-12">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-memory"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">RAM Usage</span>
                    <span class="info-box-number" id="ram-usage">Loading...</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-12">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-microchip"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">CPU Usage</span>
                    <span class="info-box-number" id="cpu-usage">Loading...</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-12">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-hdd"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Disk Usage</span>
                    <span class="info-box-number" id="disk-usage">Loading...</span>
                </div>
            </div>
        </div>
    </div>
    @endif


    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">Yearly Customers Chart</div>
                <div class="card-body">
                    <canvas id="customer_chart"></canvas>
                </div>
            </div>
        </div>
        @include('Backend.Component.Chart.Online_offline_chart')
    </div>


    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">Bandwidth Usage Chart</div>
                <div class="card-body">
                    <canvas id="bandwidthChart"></canvas>
                </div>
            </div>
        </div>
        @include('Backend.Component.Chart.Customer_payment_chart')
    </div>


    @if ($branch_user_id != null)
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">Recent Transactions</div>
                    <div class="card-body">
                        @php
                            $branch_pacakges = App\Models\Branch_transaction::where('pop_id', $branch_user_id)->get();
                        @endphp
                        @if (!empty($branch_pacakges))
                            <div class="table-responsive">
                                <table id="branch_recharge_datatable" class="table table-bordered dt-responsive nowrap"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Transaction</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody id="">
                                        @php
                                            $branch_recharge = App\Models\Branch_transaction::where(
                                                'pop_id',
                                                $branch_user_id,
                                            )->get();
                                            $number = 1;
                                        @endphp
                                        @foreach ($branch_recharge as $item)
                                            <tr>
                                                <td>{{ $number++ }}</td>
                                                <td>
                                                    {{ date('d F Y', strtotime($item->created_at)) }}
                                                </td>
                                                <td>{{ $item->amount }}</td>
                                                <td>
                                                    @if ($item->transaction_type == 'cash')
                                                        <span class="badge bg-success">Cash</span>
                                                    @elseif($item->transaction_type == 'credit')
                                                        <span class="badge bg-danger">Credit</span>
                                                    @elseif($item->transaction_type == 'bkash')
                                                        <span class="badge bg-success">Bkash</span>
                                                    @elseif($item->transaction_type == 'nagad')
                                                        <span class="badge bg-primary">Nagad</span>
                                                    @elseif($item->transaction_type == 'bank')
                                                        <span class="badge bg-success">Bank</span>
                                                    @elseif($item->transaction_type == 'due_paid')
                                                        <span class="badge bg-success">Due Paid</span>
                                                    @elseif($item->transaction_type == 'other')
                                                        <span class="badge bg-success">Other</span>
                                                    @else
                                                        <span class="badge bg-danger">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->note }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <h4 class="text-center text-danger">Not Found</h4>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">New Customers by months</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>NO.</th>
                                    <th>Months</th>
                                    <th>New Conn.</th>
                                    <th>Expired Conn.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>January</td>
                                    <td><span class="badge bg-success">23</span></td>
                                    <td><span class="badge bg-danger">52</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('Backend.Modal.Customer.customer_modal')
    @include('Backend.Modal.Tickets.ticket_modal')
    @include('Backend.Modal.Sms.send_modal')
    @include('Backend.Modal.Sms.topup_modal')
@endsection

@section('script')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>

    <script type="text/javascript">
        $("#branch_recharge_datatable").DataTable();
        /*GET Recevied Controller Data with json formate for script file*/
        var onlineCustomer = <?php echo json_encode($online_customer); ?>;
        var offlineCustomer = <?php echo json_encode($offline_customer); ?>;
        /*Customer Recharge Details*/
        var total_recharged = <?php echo json_encode($total_recharged); ?>;
        var totalPaid = <?php echo json_encode($totalPaid); ?>;
        var totalDue = <?php echo json_encode($totalDue); ?>;
        var duePaid = <?php echo json_encode($duePaid); ?>;

        /*************************** Yearly Customers Chat***************************************/
        var ctx = document.getElementById('customer_chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Customer',
                    data: [100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1100, 1200],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: {
                responsive: true
            }
        });


        /*************************** Online vs Offline Customers ***************************************/
        var ctx2 = document.getElementById('customer_online_offline_chart').getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Online', 'Offline'],
                datasets: [{
                    data: [onlineCustomer, offlineCustomer],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true
            }
        });
        /*************************** Bandwidth Usage Chart ***************************************/
        var ctx3 = document.getElementById('bandwidthChart').getContext('2d');
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Bandwidth Usage (GB)',
                    data: [100, 150, 200, 250, 300, 350],
                    backgroundColor: 'rgba(255, 159, 64, 0.6)'
                }]
            },
            options: {
                responsive: true
            }
        });

        /*************************** Customer Payment Status Chart ***************************************/

        var ctx4 = document.getElementById('paymentChart').getContext('2d');
        new Chart(ctx4, {
            type: 'doughnut',
            data: {
                labels: ['Recharged', 'Total Paid', 'Total Due', 'Due Paid'],
                datasets: [{
                    data: [total_recharged, totalPaid, totalDue, duePaid],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
                }]
            },
            options: {
                responsive: true
            }
        });

        /************************** Card Move Another Place*****************************************/
        function saveOrder() {
            let order = [];
            $(".card-item").each(function() {
                order.push($(this).data("id"));
            });
            localStorage.setItem("dashboardOrder", JSON.stringify(order));
        }

        function loadOrder() {
            let savedOrder = localStorage.getItem("dashboardOrder");
            if (savedOrder) {
                let order = JSON.parse(savedOrder);
                let container = $("#dashboardCards");
                let elements = {};

                $(".card-item").each(function() {
                    let id = $(this).data("id");
                    elements[id] = $(this);
                });

                container.empty();

                order.forEach(id => {
                    if (elements[id]) {
                        container.append(elements[id]);
                        delete elements[id];
                    }
                });
                Object.values(elements).forEach(el => container.append(el));
            }
        }


        $("#dashboardCards").sortable({
            update: function(event, ui) {
                saveOrder();
            }
        });

        loadOrder();

        function resetOrder() {
            localStorage.removeItem("dashboardOrder");
            location.reload();
        }
        $(document).on("click", "#resetOrderBtn", function() {
            let btn = $(this);
            let originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop("disabled", true);
            resetOrder();
        });
        /************************** Card Move Another Place*****************************************/
        /************************** Server Information Start*****************************************/
        function __load_server_stats() {
            fetch('/server-information')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('ram-usage').textContent = data.ram;
                    document.getElementById('cpu-usage').textContent = data.cpu;
                    document.getElementById('disk-usage').textContent = data.disk;
                })
                .catch(error => console.error('Error fetching server stats:', error));
        }

        setInterval(__load_server_stats, 1000);
        __load_server_stats();
        /************************** Server Information End*****************************************/
    </script>
@endsection
