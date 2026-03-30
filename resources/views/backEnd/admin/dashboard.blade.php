@extends('backEnd.layouts.master')
@section('title','Dashboard')
@section('css')
<style>
   


.stat-card {
    position: relative;
    overflow: hidden;
    background: #fff;
    padding: 22px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(226, 232, 240, 0.9);
    transition: all 0.25s ease;
    min-height: 115px;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.10);
}

.stat-card::after {
    content: "";
    position: absolute;
    right: -30px;
    top: -30px;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    opacity: 0.08;
    background: currentColor;
}

.stat-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.08);
}

.stat-card-body {
    text-align: right;
    z-index: 1;
}

.stat-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
}

.stat-value {
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    color: #0f172a;
}

/* Color themes */
.stat-primary {
    color: #2563eb;
}
.stat-primary .stat-card-icon {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
}

.stat-success {
    color: #16a34a;
}
.stat-success .stat-card-icon {
    background: linear-gradient(135deg, #22c55e, #15803d);
    color: #fff;
}

.stat-info {
    color: #0891b2;
}
.stat-info .stat-card-icon {
    background: linear-gradient(135deg, #06b6d4, #0e7490);
    color: #fff;
}

.stat-warning {
    color: #d97706;
}
.stat-warning .stat-card-icon {
    background: linear-gradient(135deg, #f59e0b, #b45309);
    color: #fff;
}




.custom-card {
    border: none;
   
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    overflow: hidden;
}

.custom-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 18px 20px;
    border-bottom: 1px solid #f1f5f9;
}

.custom-header h5 {
    margin: 0;
    font-size: 16px;
}

.view-btn {
    font-size: 13px;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.view-btn:hover {
    text-decoration: underline;
}

.custom-table thead {
    background: #f8fafc;
}

.custom-table th {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    padding: 12px 16px;
}

.custom-table td {
    padding: 14px 16px;
    vertical-align: middle;
}

.custom-table tbody tr:hover {
    background: #f9fafb;
}

/* Avatar */
.avatar-sm {
    width: 34px;
    height: 34px;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

/* Status badge */
.status-badge {
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 20px;
    font-weight: 500;
}

.status-badge.success {
    background: rgba(34,197,94,0.1);
    color: #16a34a;
}

.status-badge.info {
    background: rgba(6,182,212,0.1);
    color: #0891b2;
}
</style>
@endsection
@section('content')

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <h4 class="fw-muted">Dashboard Overview 👋</h4>
            <p class="text-muted">Welcome back! Here’s what’s happening today.</p>
        </div>
    </div>

    {{-- 🔥 STATS CARDS --}}
    <div class="row g-3">

        {{-- Total Order --}}
        <div class="col-md-6 col-xl-3">
            <div class="stat-card stat-primary">
                <div class="stat-card-icon">
                    <i class="mdi mdi-cart-outline"></i>
                </div>

                <div class="stat-card-body">
                    <span class="stat-label">Total Orders</span>
                    <h3 class="stat-value mb-0">{{ $total_order }}</h3>
                </div>
            </div>
        </div>

        {{-- Today Order --}}
        <div class="col-md-6 col-xl-3">
            <div class="stat-card stat-success">
                <div class="stat-card-icon">
                    <i class="mdi mdi-shopping-outline"></i>
                </div>

                <div class="stat-card-body">
                    <span class="stat-label">Today's Orders</span>
                    <h3 class="stat-value mb-0">{{ $today_order }}</h3>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-md-6 col-xl-3">
            <div class="stat-card stat-info">
                <div class="stat-card-icon">
                    <i class="mdi mdi-database-outline"></i>
                </div>

                <div class="stat-card-body">
                    <span class="stat-label">Products</span>
                    <h3 class="stat-value mb-0">{{ $total_product }}</h3>
                </div>
            </div>
        </div>

        {{-- Customers --}}
        <div class="col-md-6 col-xl-3">
            <div class="stat-card stat-warning">
                <div class="stat-card-icon">
                    <i class="mdi mdi-account-group-outline"></i>
                </div>

                <div class="stat-card-body">
                    <span class="stat-label">Customers</span>
                    <h3 class="stat-value mb-0">{{ $total_customer }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">

        {{-- Latest Orders --}}
        <div class="col-xl-6">
            <div class="card custom-card">
                
                <div class="card-header custom-header">
                    <h5><i class="mdi mdi-cart-outline me-2 text-primary"></i> Latest Orders</h5>
                    <a href="{{route('admin.orders',['slug'=>'all'])}}" class="view-btn">
                        View All →
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle custom-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($latest_order as $order)
                            <tr>
                                <td>{{$loop->iteration}}</td>

                                <td>
                                    <span class="fw-semibold text-dark">
                                        {{$order->invoice_id}}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                {{ strtoupper(substr($order->customer->name ?? 'U',0,1)) }}
                                            </span>
                                        </div>
                                        <span>{{ $order->customer->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>

                                <td class="fw-semibold text-success">
                                    ৳ {{$order->amount}}
                                </td>

                                <td>
                                    <span class="status-badge success">
                                        {{$order->order_status}}
                                    </span>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        {{-- Latest Customers --}}
        <div class="col-xl-6">
            <div class="card custom-card">

                <div class="card-header custom-header">
                    <h5><i class="mdi mdi-account-group-outline me-2 text-info"></i> Latest Customers</h5>
                    <a href="{{route('customers.index')}}" class="view-btn">
                        View All →
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle custom-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($latest_customer as $customer)
                            <tr>

                                <td>{{$loop->iteration}}</td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <span class="avatar-title bg-soft-info text-info rounded-circle">
                                                {{ strtoupper(substr($customer->name,0,1)) }}
                                            </span>
                                        </div>
                                        <span class="fw-semibold">{{$customer->name}}</span>
                                    </div>
                                </td>

                                <td>{{$customer->phone}}</td>

                                <td class="text-muted">
                                    {{$customer->created_at->format('d M Y')}}
                                </td>

                                <td>
                                    <span class="status-badge info">
                                        {{$customer->status}}
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

</div>

@endsection
@section('script')
 <!-- Plugins js-->
        <script src="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.js"></script>
        <script src="{{asset('public/backEnd/')}}/assets/libs/apexcharts/apexcharts.min.js"></script>
        <script src="{{asset('public/backEnd/')}}/assets/libs/selectize/js/standalone/selectize.min.js"></script>

    <script>

    var colors = ["#f1556c"],
    dataColors = $("#total-revenue").data("colors");
    dataColors && (colors = dataColors.split(","));
    var options = {
          
          chart: {
             height: 242,
             type: "radialBar"
          },
          plotOptions: {
             radialBar: {
                hollow: {
                   size: "65%"
                }
             }
          },
          colors: colors,
          labels: ["Delivery"]
       },
        chart = new ApexCharts(document.querySelector("#total-revenue"), options);
        chart.render();
        colors = ["#1abc9c", "#4a81d4"];
        (dataColors = $("#sales-analytics").data("colors")) && (colors = dataColors.split(","));
        options = {
           series: [{
              name: "Revenue",
              type: "column",
              data: [@foreach($monthly_sale as $sale) {{$sale->amount}}, @endforeach]
           }, {
              name: "Sales",
              type: "line",
              data: [@foreach($monthly_sale as $sale) {{$sale->amount}}, @endforeach]
           }],
           chart: {
              height: 378,
              type: "line",
           },
           stroke: {
              width: [2, 3]
           },
           plotOptions: {
              bar: {
                 columnWidth: "50%"
              }
           },
           colors: colors,
           dataLabels: {
              enabled: !0,
              enabledOnSeries: [1]
           },
           labels: [@foreach($monthly_sale as $sale) {{date('d', strtotime($sale->date))}} + '-' + {{date('m', strtotime($sale->date))}}+ '-' + {{date('Y', strtotime($sale->date))}}, @endforeach],
           legend: {
              offsetY: 7
           },
           grid: {
              padding: {
                 bottom: 20
              }
           },
           fill: {
              type: "gradient",
              gradient: {
                 shade: "light",
                 type: "horizontal",
                 shadeIntensity: .25,
                 gradientToColors: void 0,
                 inverseColors: !0,
                 opacityFrom: .75,
                 opacityTo: .75,
                 stops: [0, 0, 0]
              }
           },
           yaxis: [{
              title: {
                 text: "Net Revenue"
              }
           }]
        };
        (chart = new ApexCharts(document.querySelector("#sales-analytics"), options)).render(), $("#dash-daterange").flatpickr({
           altInput: !0,
           mode: "range",
        });
    </script>
@endsection