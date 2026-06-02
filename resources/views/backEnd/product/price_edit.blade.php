@extends('backEnd.layouts.master')
@section('title','Product Price Manage')
@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                    <!-- Left: Title + Info -->
                    <div class="d-flex align-items-center gap-3">

                        <!-- Icon -->
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                            <i class="fas fa-shopping-bag fs-4"></i>
                        </div>

                        <!-- Text -->
                        <div>
                            <h4 class="mb-0 "> Product Price Edit</h4>

                            <small class="text-muted">Manage all your products efficiently</small>
                        </div>
                    </div>

                    <!-- Right: Count + Button -->
                    <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">



                        <!-- Divider -->
                        <div class="vr d-none d-sm-block"></div>

                        <!-- Button -->
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fe-shopping-cart me-1"></i> Add Product
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
   <div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{route('products.price_update')}}" method="POST">
                @csrf
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table nowrap w-100" id="price_edit_datatable">
                    <thead>
                        <tr>
                            <th style="width:5%">SL</th>
                                    </div></th>
                            <th style="width:50%">Name</th>
                            <th style="width:15%">Old Price</th>
                            <th style="width:15%">New Price</th>
                            <th style="width:15%">Stock</th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($products as $key=>$value)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <input type="hidden" value="{{$value->id}}" name="ids[]">
                            <td>{{$value->name}}</td>
                            <td><input value="{{$value->old_price}}" name="old_price[]" class="form-control"></td>
                            <td><input value="{{$value->new_price}}" name="new_price[]" class="form-control"></td>
                            <td><input value="{{$value->stock}}" name="stock[]" class="form-control"></td>
                        </tr>
                        @endforeach
                     </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end">
                                    <button type="button" onclick="history.back();" class="btn btn-danger me-2">
                                        Back
                                    </button>

                                    <button type="submit" class="btn btn-success">
                                        Update Price
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div> <!-- end card body-->
            </form>
        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
</div>


@endsection

@section('script')
<script type="text/javascript">
$('#price_edit_datatable').DataTable();
</script>
{!! Toastr::message() !!}
@endsection
