@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endsection
@section('content')
    <div class="row mb-3">
        <!-- Buttons -->
        <div class="col-md-12 d-flex flex-wrap gap-2">
            <button class="btn btn-success m-1" data-toggle="modal" data-target="#addCustomerModal" ><i class="fas fa-user-plus"></i> Add Customer</button>

            <button class="btn btn-secondary m-1" data-toggle="modal" data-target="#addBranchPackageModal">
                <i class="fas fa-box"></i> Add Package
            </button>

            <button class="btn btn-dark m-1" data-toggle="modal" data-target="#PopRechargeModal"><i
                    class="fas fa-hand-holding-usd"></i> Pop Recharge & Received</button>
            <button class="btn btn-primary m-1 edit-pop" data-id="{{ $pop->id }}"><i class="fas fa-edit"></i> Edit
                POP/Branch</button>


                @php
                $get_pop = App\Models\Pop_branch::find($pop->id);
            @endphp

            <button type="button" class="btn btn-{{ $get_pop && $get_pop->status == 1 ? 'danger' : 'success' }} m-1 change-status" data-id="{{ $pop->id }}"><i class="fas fa-user-lock"></i>
                {{ $get_pop && $get_pop->status == 1 ? 'Disable' : 'Enable' }} POP/Branch
            </button>





            <button id="resetOrderBtn" class="btn btn-warning m-1"><i class="fas fa-undo"></i> Reset Card</button>
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
                    'icon' => 'fas fa-solid fa-user-check',
                ],
                ['id' => 2, 'title' => 'Offline', 'value' => $offline_customer, 'bg' => 'info', 'icon' => 'fas fa-solid fa-user-times'],
                [
                    'id' => 3,
                    'title' => 'Active Customers',
                    'value' => $active_customer,
                    'bg' => 'primary',
                    'icon' => 'fas fa-solid fa-users',
                ],
                [
                    'id' => 4,
                    'title' => 'Expired',
                    'value' => $expire_customer,
                    'bg' => 'danger',
                    'icon' => 'fas fa-solid fa-user-clock',
                ],
                [
                    'id' => 5,
                    'title' => 'Disabled',
                    'value' => $disable_customer,
                    'bg' => 'warning',
                    'icon' => 'fas fa-solid fa-user-lock',
                ],
                [
                    'id' => 6,
                    'title' => 'Current Balance',
                    'value' => $current_balance,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-dollar-sign',
                ],
                [
                    'id' => 7,
                    'title' => 'Total Paid',
                    'value' => $total_paid,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-dollar-sign',
                ],
                [
                    'id' => 8,
                    'title' => 'Total Due',
                    'value' => $total_due,
                    'bg' => 'danger',
                    'icon' => 'fas fa-solid fa-dollar-sign',
                ],
                [
                    'id' => 9,
                    'title' => 'Due Paid',
                    'value' => $due_paid,
                    'bg' => 'success',
                    'icon' => 'fas fa-hand-holding-usd',
                ],
                [
                    'id' => 10,
                    'title' => 'Area',
                    'value' => $total_area,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-map-marker-alt',
                ],
                [
                    'id' => 11,
                    'title' => 'Total Tickets',
                    'value' => $tickets,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-ticket-alt',
                ],
                [
                    'id' => 12,
                    'title' => 'Completed Tickets',
                    'value' => $ticket_completed,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-check-circle',
                ],
                [
                    'id' => 13,
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
                        <i class="{{ $card['icon'] }} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#customers" data-toggle="tab">Total Customers (
                            @php
                            echo   $area_count=App\Models\Customer::where('pop_id',$pop->id)->count();
                          @endphp
                                )</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tickets" data-toggle="tab">Tickets</a></li>

                        <li class="nav-item"><a class="nav-link" href="#branch_package" data-toggle="tab">Package</a></li>

                        <li class="nav-item"><a class="nav-link" href="#transaction_statment" data-toggle="tab">Transaction Statment</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#area_list" data-toggle="tab">Branch Area</a>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Customer -->
                        <div class="active tab-pane" id="customers">
                            <div class="table-responsive">
                                @include('Backend.Component.Customer.Customer',['pop_id'=>$pop->id])
                            </div>
                        </div>
                        <!-- Tickets -->
                        <div class="tab-pane" id="tickets">
                            <div class="table table-responsive">
                                @include('Backend.Component.Tickets.Tickets',['pop_id'=>$pop->id])
                            </div>
                        </div>

                        <!-- Package -->
                        <div class="tab-pane" id="branch_package">
                            <div class="row">
                                @php
                                    $branch_pacakges = App\Models\Branch_package::where('pop_id', $pop->id)->get();
                                @endphp
                                @if (!empty($branch_pacakges))
                                    <div class="table-responsive">
                                        <table id="branch_package_datatable" class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>Package Name</th>
                                                    <th>Purchase Price</th>
                                                    <th>Sales Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="">
                                                @php
                                                    $branch_pacakges = App\Models\Branch_package::where(
                                                        'pop_id',
                                                        $pop->id,
                                                    )->get();
                                                    $number=1;
                                                @endphp
                                                @foreach ($branch_pacakges as $item)
                                                    <tr>
                                                        <td>{{ $number++ }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->purchase_price }}</td>
                                                        <td>{{ $item->sales_price }}</td>
                                                        <td>
                                                            <button class="btn btn-primary edit_branch_pacakge_btn" data-id="{{ $item->id }}"><i class="fas fa-edit"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <h4 class="text-center text-danger">Documents Not Found</h4>
                                @endif



                            </div>
                        </div>
                        <!--transaction Statment -->
                        <div class="tab-pane" id="transaction_statment">
                            <div class="row">
                                @php
                                    $branch_pacakges = App\Models\Branch_transaction::where('pop_id', $pop->id)->get();
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
                                                    <th>Transaction Type</th>
                                                    <th>Note</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="">
                                                @php
                                                    $branch_recharge = App\Models\Branch_transaction::where(
                                                        'pop_id',
                                                        $pop->id,
                                                    )->get();
                                                    $number=1;
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
                                                        <td>
                                                            <button type="button" class="btn btn-danger branch_recharge_undo_btn" data-id="{{ $item->id }}"><i class="fas fa-undo"></i></button>
                                                        </td>
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
                        <!-- Branch Area List -->
                        <div class="tab-pane" id="area_list">
                            <div class="row">
                                @php
                                    $branch_areas = App\Models\Pop_area::where('pop_id', $pop->id)->get();
                                @endphp
                                @if (!empty($branch_areas))
                                    <div class="table-responsive">
                                        <table id="branch_area_datatable" class="table table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Area Name </th>
                                                    <th>Billing Cycle</th>
                                                    {{-- <th>Action</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody id="">
                                                @php
                                                    $branch_areas = App\Models\Pop_area::where(
                                                        'pop_id',
                                                        $pop->id,
                                                    )->get();
                                                    $number=1;
                                                @endphp
                                                @foreach ($branch_areas as $item)
                                                    <tr>
                                                        <td>{{ $number++ }}</td>
                                                        <td>{{ $item->name }}</td>

                                                        <td>{{ $item->billing_cycle }}</td>
                                                        {{-- <td>
                                                            <button type="button" class=" btn btn-success branch_area_edit_btn" data-id="{{ $item->id }}"><i class="fas fa-edit"></i></button>
                                                        </td> --}}
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
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">Yearly Revenue Chart</div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Active vs Inactive Customers</div>
                <div class="card-body">
                    <canvas id="customerChart"></canvas>
                </div>
            </div>
        </div>
    </div>





   
    @include('Backend.Modal.Pop.pop_modal')
    @include('Backend.Modal.Customer.customer_modal',['pop_id'=>$pop->id])
    @include('Backend.Modal.Customer.Package.branch_package_modal')
    @include('Backend.Modal.delete_modal')


@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            /*****Branch Pop Update****/
            handleSubmit('#popForm', '#addModal');
            /*****Brnach Package Add****/
            handleSubmit('#BranchPackageForm', '#addBranchPackageModal');

            /*****Branch Recharge ****/
            handleSubmit('#popRechargeForm', '#PopRechargeModal');

            $("#recent_transaction").DataTable();
            $("#recent_tickets").DataTable();
            $("#branch_package_datatable").DataTable();
            $("#branch_recharge_datatable").DataTable();
            $("#branch_area_datatable").DataTable();
        });

        /** Handle Edit button click **/
        $(document).on('click', '.edit-pop', function() {
            var id = $(this).data('id');

            // AJAX call to fetch supplier data
            $.ajax({
                url: "{{ route('admin.pop.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#popForm').attr('action', "{{ route('admin.pop.update', ':id') }}".replace(
                            ':id', id));
                        $('#ModalLabel').html(
                            '<span class="mdi mdi-account-edit mdi-18px"></span> &nbsp;Edit POP/Branch'
                            );
                        $('#popForm input[name="name"]').val(response.data.name);
                        $('#popForm input[name="username"]').val(response.data.username);
                        $('#popForm input[name="password"]').val(response.data.password);
                        $('#popForm input[name="phone"]').val(response.data.phone);
                        $('#popForm input[name="email"]').val(response.data.email);
                        $('#popForm input[name="address"]').val(response.data.address);
                        $('#popForm select[name="status"]').val(response.data.status);

                        // Show the modal
                        $('#addModal').modal('show');
                    } else {
                        toastr.error('Failed to fetch Supplier data.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });
        /** Handle Branch Edit button click **/
        $(document).on('click', '.edit_branch_pacakge_btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.pop.branch.package.edit', ':id') }}".replace(':id', id),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#editBranchPackageForm').attr('action', "{{ route('admin.pop.branch.package.update', ':id') }}".replace(
                            ':id', id));
                        $('#editBranchPackageModal select[name="package_id"]').val(response.data.package_id).trigger('change');
                        $('#editBranchPackageModal input[name="purchase_price"]').val(response.data.purchase_price);
                        $('#editBranchPackageModal input[name="sales_price"]').val(response.data.sales_price);
                        // Show the modal
                        $('#editBranchPackageModal').modal('show');
                    } else {
                        toastr.error('Failed to fetch  data.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });
        /** Handle Branch Undo Recharge button click **/
        $(document).on('click', '.branch_recharge_undo_btn', function() {
            if(confirm('Are you sure you want to undo this action?')){
                var id = $(this).data('id');
                var button = $(this);
                var row = button.closest('tr');
                var originalContent = button.html();
                button.html('<i class="fas fa-spinner fa-spin"></i> Undoing...').prop('disabled', true);
                $.ajax({
                    url: "{{ route('admin.pop.brnach.recharge.undo', ':id') }}".replace(':id', id),
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            row.fadeOut(300, function() {
                                $(this).remove();
                                toastr.success('Successfully Undo!');
                            });

                        }
                    },
                    error: function() {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }
        });

        $(document).on("click", ".change-status", function () {
            let id = $(this).data("id");
            let btn = $(this);
            let originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop("disabled", true);
            $.ajax({
                url: "{{ route('admin.pop.change_status', '') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        let newStatus = response.new_status;
                        btn.toggleClass("btn-danger btn-success");
                        btn.html(
                            `<i class="fas fa-user-lock"></i> ` +
                            (newStatus == 1 ? "Disable" : "Enable") + " POP/Branch"
                        );
                    }
                },
                error: function () {
                    toastr.error("Something went wrong!");
                },
                complete: function () {
                    btn.prop("disabled", false);
                }
            });
        });

        /** Handle Delete button click**/
        $('#datatable1 tbody').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            var deleteUrl = "{{ route('admin.pop.delete', ':id') }}".replace(':id', id);

            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').find('input[name="id"]').val(id);
            $('#deleteModal').modal('show');
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
        $(document).on("click", "#resetOrderBtn", function () {
            let btn = $(this);
            let originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop("disabled", true);
            resetOrder();
        });
        /************************** Card Move Another Place*****************************************/
        /************************** Yearly Revenue Chart*****************************************/
        var ctx = document.getElementById('revenueChart').getContext('2d');
        var revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Augest', 'September', 'Octobar',
                    'November', 'December'
                ],
                datasets: [{
                    label: 'Revenue',
                    data: [1200, 1900, 3000, 5000, 2000, 3000, 1200, 1900, 3000, 5000, 2000, 3000],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: {
                responsive: true
            }
        });
        /************************** Yearly Revenue Chart*****************************************/
        /************************** Customer  Chart*****************************************/
        var ctx2 = document.getElementById('customerChart').getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [80, 20],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true
            }
        });
        /************************** Customer  Chart*****************************************/
    </script>
@endsection
