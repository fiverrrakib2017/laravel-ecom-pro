@extends('backEnd.layouts.master')
@section('title','Product Manage')
@section('css')
    <style>
        .backend-image{
            height: 50px;
            width: 50px;
            border-radius: 50px;
        }
        .status-badge {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
        }

        /* Active */
        .status-badge.active {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        /* Inactive */
        .status-badge.inactive {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
    </style>
@endsection
@section('content')
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
                        <h4 class="mb-0 ">
                            Product Manage
                        </h4>

                        <small class="text-muted">Manage all your products efficiently</small>
                    </div>
                </div>

                <!-- Right: Count + Button -->
                <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">

                    <!-- Count Badge -->
                    <div class="text-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            {{ $data->count() }}
                        </h5>
                        <small class="text-muted">Total Product</small>
                    </div>

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
<div class="row order_page">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <!-- Top Actions -->
                <div class="d-flex justify-content-between flex-wrap mb-3">
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Deal On -->
                        <a href="{{route('products.update_deals',['status'=>1])}}"
                        class="btn btn-success hotdeal_update">
                            <i class="fas fa-fire me-1"></i> Deal On
                        </a>

                        <!-- Deal Off -->
                        <a href="{{route('products.update_deals',['status'=>0])}}"
                        class="btn btn-danger hotdeal_update">
                            <i class="fas fa-ban me-1"></i> Deal Off
                        </a>

                        <!-- Active -->
                        <a href="{{route('products.update_status',['status'=>1])}}"
                        class="btn btn-primary update_status">
                            <i class="fas fa-check-circle me-1"></i> Active
                        </a>

                        <!-- Inactive -->
                        <a href="{{route('products.update_status',['status'=>0])}}"
                        class="btn btn-warning update_status">
                            <i class="fas fa-times-circle me-1"></i> Inactive
                        </a>

                    </div>

                    <!-- Search -->
                    <form class="d-flex custom_form">
                        <input type="text" name="keyword" class="form-control me-2" placeholder="Search">
                        <button class="btn btn-primary">Search</button>
                    </form>

                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table  class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th style="width:2%"><div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input checkall" value=""></label>
                            <th style="width:2%">SL</th>
                                    </div></th>
                            <th style="width:10%">Action</th>
                            <th style="width:20%">Name</th>
                            <th style="width:10%">Category</th>
                            <th style="width:10%">Image</th>
                            <th style="width:10%">Price</th>
                            <th style="width:8%">Stock</th>
                            <th style="width:14%">Deal & Feature</th>
                            <th style="width:8%">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($data as $key=>$value)
                        <tr>
                            <td><input type="checkbox" class="checkbox" value="{{$value->id}}"></td>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2 justify-content-center">
                                    {{-- Status Toggle --}}
                                    @if($value->status == 1)
                                    <form method="post" action="{{route('products.inactive')}}">
                                        @csrf
                                        <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                        <button type="submit" class="btn btn-sm btn-warning" title="Deactivate">
                                            <i class="fas fa-thumbs-down"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form method="post" action="{{route('products.active')}}">
                                        @csrf
                                        <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                        <button type="submit" class="btn btn-sm btn-success" title="Activate">
                                            <i class="fas fa-thumbs-up"></i>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Edit --}}
                                    <a href="{{route('products.edit',$value->id)}}"
                                    class="btn btn-sm btn-primary"
                                    title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form method="post" action="{{route('products.destroy')}}">
                                        @csrf
                                        <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                        <button type="submit"
                                                class="btn btn-sm btn-danger delete-confirm"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->category?$value->category->name:''}}</td>
                            <td><img src="{{asset($value->image?$value->image->image:'')}}" class="backend-image" alt=""></td>
                            <td>{{$value->new_price}}</td>
                            <td>{{$value->stock}}</td>
                            <td><p class="m-0">Hot Deals : {{$value->topsale==1?'Yes':'No'}}</p>
                                <p class="m-0">Top Feature : {{$value->feature_product==1?'Yes':'No'}}</p></td>
                            <td>
                                @if($value->status==1)
                                    <span class="badge status-badge active">
                                        <i class="fas fa-check-circle me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge status-badge inactive">
                                        <i class="fas fa-times-circle me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                     </tbody>


                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{$data->links('pagination::bootstrap-4')}}
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Assign User End-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".checkall").on('change',function(){
      $(".checkbox").prop('checked',$(this).is(":checked"));
    });
    $(document).on('click', '.hotdeal_update', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        console.log('url',url);
        var product = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var product_ids=product.get();
        if(product_ids.length ==0){
            toastr.error('Please Select A Product First !');
            return ;
        }
        $.ajax({
           type:'GET',
           url:url,
           data:{product_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var product = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var product_ids=product.get();
        if(product_ids.length ==0){
            toastr.error('Please Select A Product First !');
            return ;
        }
        $.ajax({
           type:'GET',
           url:url,
           data:{product_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                window.location.reload();
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
});
</script>
@endsection

