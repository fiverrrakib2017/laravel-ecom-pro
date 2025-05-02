@extends('Backend.Layout.App')
@section('title', 'Product Profile | Admin Panel')
@section('style')
    <style>
        #product_info>li {
            border-bottom: 1px dashed;
        }

        .section-header {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img class="img-fluid"
                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSkdGbj-QrUuNqhXP7DtY3-t8yD6H1Tk4uFg&s">
                </div>
                <div class="card mt-3">
                    <div class="card-title text-center mt-1">
                        <h5>About This Product</h5>
                    </div>
                    <div class="card-body">
                        <p>this is the nast timethis is the nast timethis is the nast timethis is the nast
                            timethis is the nast timethis is the nast timethis is the nast time</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Product Name:</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $data->name ?? '' }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Category</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $data->category->category_name ?? '' }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Brand</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $data->brand->brand_name ?? '' }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Purchase Price:</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $data->purchase_price ?? 00 }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Sale's Price:</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $data->sale_price ?? 00 }}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Store</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                {{ $data->store->name ?? '' }}
                            </div>
                        </div>
                        <hr>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-primary card-outline">
                                    <div class="card-header p-2">
                                        <ul class="nav nav-pills" id="history-tabs">
                                            <li class="nav-item"><a class="nav-link active" href="#purchase_history" data-toggle="tab">Purchase History</a></li>
                                            <li class="nav-item"><a class="nav-link" href="#sales_history" data-toggle="tab">Sales History</a></li>
                                        </ul>
                                    </div><!-- /.card-header -->

                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- Purchase History Tab -->
                                            <div class="active tab-pane" id="purchase_history">
                                                <div class="table-responsive">
                                                    <table id="purchase_table" class="table table-bordered table-striped table-hover">
                                                        <thead >
                                                            <tr>
                                                                <th>Invoice ID</th>
                                                                <th>Supplier Name</th>
                                                                <th>Quantity</th>
                                                                <th>Invoice Date</th>
                                                                <th>Create Date</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if (!empty($purchase_invoice_history))
                                                                @foreach ($purchase_invoice_history as $item)
                                                                    <tr>
                                                                        <td>{{ $item->invoice_id ?? '-' }}</td>
                                                                        <td>{{ $item->invoice->supplier->fullname ?? '-' }}</td>
                                                                        <td>{{ $item->qty ?? '-' }}</td>
                                                                        <td>{{ $item->invoice->invoice_date ?? '-' }}</td>
                                                                        <td>{{ $item->created_at ?? '-' }}</td>
                                                                        <td>
                                                                            <a href="{{ route('admin.supplier.invoice.view_invoice', $item->invoice_id) }}"
                                                                                class="btn btn-info btn-sm">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr><td colspan="6" class="text-center text-muted">No data found.</td></tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Sales History Tab -->
                                            <div class="tab-pane" id="sales_history">
                                                <div class="table-responsive">
                                                    <table id="sales_table" class="table table-bordered table-striped table-hover">
                                                        <thead >
                                                            <tr>
                                                                <th>Invoice ID</th>
                                                                <th>Customer Name</th>
                                                                <th>Quantity</th>
                                                                <th>Invoice Date</th>
                                                                <th>Create Date</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if (!empty($sales_invoice_history))
                                                                @foreach ($sales_invoice_history as $item)
                                                                    <tr>
                                                                        <td>{{ $item->invoice_id ?? '-' }}</td>
                                                                        <td>{{ $item->invoice->client->fullname ?? '-' }}</td>
                                                                        <td>{{ $item->qty ?? '-' }}</td>
                                                                        <td>{{ $item->invoice->invoice_date ?? '-' }}</td>
                                                                        <td>{{ $item->created_at ?? '-' }}</td>
                                                                        <td>
                                                                            <a href="{{ route('admin.customer.invoice.view_invoice', $item->invoice_id) }}"
                                                                                class="btn btn-info btn-sm">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr><td colspan="6" class="text-center text-muted">No data found.</td></tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div> <!-- /.tab-content -->
                                    </div><!-- /.card-body -->
                                </div><!-- /.card -->
                            </div>
                        </div>

                    </div>
                </div>




            </div>
        </div>
    </div>
@endsection
@section('script')

<script type="text/javascript">

    $(document).ready(function(){
        $("#purchase_table").DataTable();
        $("#sales_table").DataTable();
    })

</script>

@endsection
