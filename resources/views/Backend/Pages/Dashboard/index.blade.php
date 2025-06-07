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
                    'url' => route('admin.customer.index', ['status' => 'online']),
                ],
                [
                    'id' => 2,
                    'title' => 'Offline',
                    'value' => $offline_customer,
                    'bg' => 'info',
                    'icon' => 'fa-user-times',
                    'url' => route('admin.customer.index', ['status' => 'Offline']),
                ],
                [
                    'id' => 3,
                    'title' => 'Active Customers',
                    'value' => $active_customer,
                    'bg' => 'primary',
                    'icon' => 'fa-users',
                    'url' => route('admin.customer.index', ['status' => 'active']),
                ],
                [
                    'id' => 4,
                    'title' => 'Expired',
                    'value' => $expire_customer,
                    'bg' => 'danger',
                    'icon' => 'fa-user-clock',
                    'url' => route('admin.customer.index', ['status' => 'expired']),
                ],
                [
                    'id' => 5,
                    'title' => 'Disabled',
                    'value' => $disable_customer,
                    'bg' => 'warning',
                    'icon' => 'fa-user-lock',
                    'url' => route('admin.customer.index', ['status' => 'disabled']),
                ],
                [
                    'id' => 6,
                    'title' => 'Requests',
                    'value' => 0,
                    'bg' => 'dark',
                    'icon' => 'fa-user-edit'
                ],

                [
                    'id' => 7,
                    'title' => 'Area',
                    'value' => $total_area,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-map-marker-alt',
                    'url' => route('admin.pop.area.index'),
                ],
                [
                    'id' => 8,
                    'title' => 'Total Tickets',
                    'value' => $tickets,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-ticket-alt',
                    'url' => route('admin.tickets.index'),
                ],
                [
                    'id' => 9,
                    'title' => 'Completed Tickets',
                    'value' => $ticket_completed,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-check-circle',
                     'url' => route('admin.tickets.index',['status' => 'completed']),
                ],
                [
                    'id' => 10,
                    'title' => 'Pending Tickets',
                    'value' => $ticket_pending,
                    'bg' => 'danger',
                    'icon' => 'fas fa-solid fa-exclamation-triangle',
                     'url' => route('admin.tickets.index',['status' => 'pending']),
                ],
            ];
        @endphp
        @foreach ($dashboardCards as $card)
            <div class="col-lg-3 col-6 card-item wow animate__animated animate__fadeInUp" data-id="{{ $card['id'] }}"
                data-wow-delay="0.{{ $card['id'] }}s">
                <a href="{{ $card['url'] ?? '#' }}" style="text-decoration: none;">
                    <div class="small-box bg-{{ $card['bg'] }}">
                        <div class="inner">
                            <h3>{{ $card['value'] }}</h3>
                            <p>{{ $card['title'] }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas {{ $card['icon'] }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </a>
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
        @include('Backend.Component.Chart.Customer_yearly_static')
        @include('Backend.Component.Chart.Online_offline_chart')
    </div>


    <div class="row mt-4">
        <!----- Ticket Chart------>
        @include('Backend.Component.Chart.Ticket.Yearly_chart')
        <!----- Cusomer Payment Chart------>
        @include('Backend.Component.Chart.Customer_payment_chart')
    </div>
    <!----- Google Map  Start ------>
      @include('Backend.Component.Customer.Google_map')
    <!----- Google Map  End ------>

    <div class="row mt-4">
        <!----- Recent Pop/Branch Transactions ------>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">Recent Pop/Branch Transactions</div>
                <div class="card-body">
                    @php
                        use App\Models\Branch_transaction;
                        use App\Models\User;

                        if (!empty($branch_user_id) ) {
                            $branch_recharge = Branch_transaction::with('pop')->where('pop_id', $branch_user_id)->get();
                            $showBranchColumn = false;
                        } else {
                            $branch_recharge = Branch_transaction::with('pop')->latest()->get();
                            $showBranchColumn = true;
                        }


                        $number = 1;
                    @endphp
                    @if (!empty($branch_recharge))
                        <div class="table-responsive">
                            <table id="branch_recharge_datatable" class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        @if(empty($branch_user_id) || $branch_user_id=NULL)
                                        <th>Pop/Branch Name</th>
                                        @endif
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Transaction</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @foreach ($branch_recharge as $item)
                                        <tr>
                                            <td>{{ $number++ }}</td>
                                            @if($showBranchColumn)
                                                <td>{{ $item->pop->name ?? 'N/A' }}</td>
                                            @endif
                                            <td>
                                                {{ date('d F Y', strtotime($item->created_at)) }}
                                            </td>
                                            <td>{{ $item->amount }}</td>
                                            <td>
                                                @php
                                                    $type = $item->transaction_type;
                                                    $badge = match ($type) {
                                                        'cash' => 'success',
                                                        'credit' => 'danger',
                                                        'bkash' => 'success',
                                                        'nagad' => 'primary',
                                                        'bank' => 'success',
                                                        'due_paid' => 'success',
                                                        'other' => 'success',
                                                        default => 'danger',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badge }}">{{ ucfirst($type ?? 'N/A') }}</span>
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
        <!----- New Customers by Month ------>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    @php
                        $selectedYear = request()->get('year', date('Y'));
                        $endYear = date('Y');
                        $startYear = 2000;
                    @endphp
                    <span>New Customers by Month ({{ $selectedYear }})</span>
                    <form method="GET" action="" id="yearForm" class="d-flex align-items-center" style="width: 50%;">
                        <select name="year" class="form-control ms-2"  onchange="document.getElementById('yearForm').submit();">

                            @for ($y = $endYear; $y >= $startYear; $y--)
                                <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>

                    </form>
                </div>
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
                            @php
                                use App\Models\Customer;
                                use Illuminate\Support\Carbon;

                                $monthlyData = [];

                                for ($month = 1; $month <= 12; $month++) {
                                    $monthName = Carbon::createFromDate($selectedYear, $month, 1)->format('F');

                                    $query = Customer::query();

                                    if (!empty($branch_user_id) && $branch_user_id > 0) {
                                        $query->where('pop_id', $branch_user_id);
                                    }

                                    $newCustomers = (clone $query)
                                        ->whereYear('created_at', $selectedYear)
                                        ->whereMonth('created_at', $month)
                                        ->count();

                                    $expiredCustomers = (clone $query)
                                        ->whereYear('expire_date', $selectedYear)
                                        ->whereMonth('expire_date', $month)
                                        ->count();

                                    $monthlyData[] = [
                                        'month' => $monthName,
                                        'new' => $newCustomers,
                                        'expired' => $expiredCustomers,
                                    ];
                                }
                            @endphp

                            @foreach ($monthlyData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $data['month'] }}</td>
                                    <td>
                                        <span class="badge bg-success text-dark">
                                            <a target="__blank" href="{{ route('admin.customer.index', ['year' => $selectedYear, 'month' => $data['month'] , 'type' => 'expired']) }}">
                                                {{ $data['new'] }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <a target="__blank" href="{{ route('admin.customer.index', ['year' => $selectedYear, 'month' => $data['month'], 'type' => 'expired']) }}">
                                                {{ $data['expired'] }}
                                            </a>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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

        /*Customer Recharge Details*/
        var total_recharged = <?php echo json_encode($total_recharged); ?>;
        var totalPaid = <?php echo json_encode($totalPaid); ?>;
        var totalDue = <?php echo json_encode($totalDue); ?>;
        var duePaid = <?php echo json_encode($duePaid); ?>;







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
