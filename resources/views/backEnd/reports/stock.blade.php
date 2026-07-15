@extends('backEnd.layouts.master')
@section('title', 'Stock Report')
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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                        style="width:60px;height:60px;">
                        <i class="mdi mdi-chart-box text-white fs-2"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-1">Stock Report</h3>
                        <p class="text-muted mb-0">View and analyze your current stock inventory.</p>
                    </div>
                </div>
                <div class="mt-2 mt-md-0">
                    <button onclick="window.print()" class="btn btn-success"><i class="mdi mdi-printer"></i> Print</button>
                    <button id="export-excel-button" class="btn btn-info"><i class="mdi mdi-file-excel"></i> Export</button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="mdi mdi-filter-outline"></i> Filter</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>Keyword</label>
                            <input type="text" class="form-control" placeholder="Enter Keyword" name="keyword" value="{{ request('keyword') }}">
                        </div>

                        <div class="col-md-3">
                            <label>Category</label>
                            <select class="form-control select2" name="category_id">
                                <option value="">Select</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="date" class="form-control flatdate" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="date" class="form-control flatdate" name="end_date"
                                value="{{ request('end_date') }}">
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary"><i class="mdi mdi-magnify"></i> Search</button>
                            <a href="{{ url()->current() }}" class="btn btn-danger">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3">

            {{-- Total Order --}}
            <div class="col-md-6 col-xl-4">
                <div class="stat-card stat-primary">
                    <div class="stat-card-icon">
                        <i class="mdi mdi-cart-outline"></i>
                    </div>

                    <div class="stat-card-body">
                        <span class="stat-label">Total Products</span>
                        <h3 class="stat-value mb-0">{{$products->count()}}</h3>
                    </div>
                </div>
            </div>

            {{-- Today Order --}}
            <div class="col-md-6 col-xl-4">
                <div class="stat-card stat-success">
                    <div class="stat-card-icon">
                        <i class="mdi mdi-shopping-outline"></i>
                    </div>

                    <div class="stat-card-body">
                        <span class="stat-label">Total Stock</span>
                        <h3 class="stat-value mb-0">{{$total_stock}}</h3>
                    </div>
                </div>
            </div>

            {{-- Products --}}
            <div class="col-md-6 col-xl-4">
                <div class="stat-card stat-info">
                    <div class="stat-card-icon">
                        <i class="mdi mdi-database-outline"></i>
                    </div>

                    <div class="stat-card-body">
                        <span class="stat-label">Total Value</span>
                        <h3 class="stat-value mb-0">{{number_format($total_price)}}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Stock List</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $stock = 0;
                            $total = 0;
                        @endphp
                        @foreach($products as $key=>$value)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->new_price}}</td>
                            <td>{{$value->stock}}</td>
                            <td>{{$value->stock*$value->new_price}}</td>
                        </tr>
                        @php
                            $stock += $value->stock;
                            $total += $value->stock * $value->new_price;
                        @endphp
                        @endforeach
                     </tbody>
                   <tfoot>
                             <tr>
                                 <td colspan="3" class="text-end"><strong>Total</strong></td>
                                 <td><strong>{{$stock}} Pcs</strong></td>
                                 <td><strong>{{$total}} Tk</strong></td>
                             </tr>
                             {{-- <tr>
                                 <td colspan="6" class="text-center">
                                     <h5><strong>Total Purchase = {{$total_purchase}}</strong></h5>
                                     <h5><strong>Total Stock = {{$total_stock}} Pcs</strong></h5>
                                     <h5><strong>Total Price = {{$total_price}} Tk</strong></h5>
                                 </td>
                             </tr> --}}
                         </tfoot>
                </table>
            </div>
        </div>

    </div>
@endsection

@section('script')

    <script>
        $('.select2').select2();
        flatpickr('.flatdate', {});
    </script>
    {!! Toastr::message() !!}
@endsection
