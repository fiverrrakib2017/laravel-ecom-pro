@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endsection
@section('header_title')
    <li class="breadcrumb-item active">POP Area/{{ $data->name }} </li>
@endsection
@section('content')
    <div class="row mb-3">
        <!-- Buttons -->
        <div class="col-md-12 d-flex flex-wrap gap-2">
            <button class="btn btn-success m-1" data-toggle="modal" data-target="#addCustomerModal"><i
                    class="fas fa-user-plus"></i> Add Customer</button>

            {{-- <button class="btn btn-primary m-1 edit-area" data-id="{{ $data->id }}"><i class="fas fa-edit"></i> Edit POP/Area</button> --}}





            @php
                $get_area = App\Models\Pop_area::find($data->id);
            @endphp

            <button type="button"
                class="btn btn-{{ $get_area && $get_area->status == 'active' ? 'danger' : 'success' }} m-1 change-status"
                data-id="{{ $data->id }}"
                data-status="{{ $get_area && $get_area->status == 'active' ? 'active' : 'inactive' }}">
                <i class="fas fa-user-lock"></i>
                {{ $get_area && $get_area->status == 'active' ? 'Disable' : 'Enable' }} This Area
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
                [
                    'id' => 2,
                    'title' => 'Offline',
                    'value' => $offline_customer,
                    'bg' => 'info',
                    'icon' => 'fas fa-solid fa-user-times',
                ],
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
                              echo   $area_count=App\Models\Customer::where('area_id',$data->id)->count();
                            @endphp
                            )</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tickets" data-toggle="tab">Tickets</a></li>


                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Customer -->
                        <div class="active tab-pane" id="customers">
                            <div class="table-responsive">
                                @include('Backend.Component.Customer.Customer',['area_id' => $data->id])
                            </div>
                        </div>
                        <!-- Tickets -->
                        <div class="tab-pane" id="tickets">
                            <div class="table table-responsive">
                                @include('Backend.Component.Tickets.Tickets',['area_id' => $data->id])
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
        @include('Backend.Component.Chart.Customer_yearly_static',['area_id'=>$data->id])
        @include('Backend.Component.Chart.Online_offline_chart',['area_id'=>$data->id])
    </div>





    @include('Backend.Modal.Customer.customer_modal', ['area_id' => $data->id])
    @include('Backend.Modal.delete_modal')


@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/delete_data.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on("click", ".change-status", function() {
            let id = $(this).data("id");
            let btn = $(this);
            let originalHtml = btn.html();

            btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop("disabled", true);

            $.ajax({
                url: "{{ route('admin.pop.area.change_status', '') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);

                        let newStatus = response.new_status;

                        // Update button class & text based on status
                        btn.removeClass("btn-danger btn-success").addClass(newStatus === 'active' ?
                            "btn-danger" : "btn-success");
                        btn.html(
                            `<i class="fas fa-user-lock"></i> ` + (newStatus === 'active' ?
                                "Disable" : "Enable") + " This Area"
                        );

                        // Update data-status attribute
                        btn.attr("data-status", newStatus);
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

        });



    </script>
@endsection
