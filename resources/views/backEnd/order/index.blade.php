@extends('backEnd.layouts.master')
@section('title',$order_status->name.' Order')
@section('content')
 <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex justify-content-between align-items-center flex-wrap">

            <!-- Title -->
            <h4 class="page-title mb-0">
                {{$order_status->name}} Order
                <span class="badge bg-primary ms-2">
                    {{$order_status->orders_count}}
                </span>
            </h4>

            <!-- Button -->
            <a href="{{route('admin.order.create')}}" class="btn btn-primary mt-2 mt-sm-0">
                <i class="fe-shopping-cart me-1"></i> Add New
            </a>

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
                        <a data-bs-toggle="modal" data-bs-target="#asignUser" class="btn btn-success">
                            <i class="fas fa-plus"></i> Assign User
                        </a>

                        <a data-bs-toggle="modal" data-bs-target="#changeStatus" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Change Status
                        </a>

                        <a href="{{route('admin.order.bulk_destroy')}}" class="btn btn-danger order_delete">
                           <i class="fas fa-trash"></i> Delete All
                        </a>

                        <a href="{{route('admin.order.order_print')}}" class="btn btn-info multi_order_print">
                            <i class="fas fa-print"></i> Print
                        </a>

                        @if($steadfast)
                        <a href="{{route('admin.bulk_courier', 'steadfast')}}" class="btn btn-warning multi_order_courier">
                            <i class="fas fa-truck"></i> Steadfast
                        </a>
                        @endif

                        <a data-bs-toggle="modal" data-bs-target="#pathao" class="btn btn-secondary">
                            <i class="fas fa-truck"></i> Pathao
                        </a>
                    </div>

                    <!-- Search -->
                    <form class="d-flex">
                        <input type="text" name="keyword" class="form-control me-2" placeholder="Search">
                        <button class="btn btn-primary">Search</button>
                    </form>

                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table  class="table table-bordered table-hover">

                        <thead class="table-light">
                            <tr>
                                <th width="2%">
                                    <input type="checkbox" class="checkall">
                                </th>
                                <th width="3%">SL</th>
                                <th>Action</th>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Assign</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($show_data as $key=>$value)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox" value="{{$value->id}}">
                                </td>

                                <td>{{$loop->iteration}}</td>

                                <td>
                                    <div class="d-flex gap-2">

                                        <a href="{{route('admin.order.invoice',['invoice_id'=>$value->invoice_id])}}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{route('admin.order.process',['invoice_id'=>$value->invoice_id])}}" class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-cog-outline"></i>
                                        </a>

                                        <a href="{{route('admin.order.edit',['invoice_id'=>$value->invoice_id])}}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="post" action="{{route('admin.order.destroy')}}">
                                            @csrf
                                            <input type="hidden" value="{{$value->id}}" name="id">
                                            <button type="submit" class="btn btn-sm btn-danger delete-confirm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                                <td>{{$value->invoice_id}}</td>

                                <td>
                                    {{date('d-m-Y', strtotime($value->updated_at))}} <br>
                                    <small>{{date('h:i:s a', strtotime($value->updated_at))}}</small>
                                </td>

                                <td>
                                    <strong>{{$value->shipping->name ?? ''}}</strong>
                                    <p class="mb-0">{{$value->shipping->address ?? ''}}</p>
                                </td>

                                <td>{{$value->shipping->phone ?? ''}}</td>
                                <td>{{$value->user->name ?? ''}}</td>
                                <td>৳{{$value->amount}}</td>
                                <td>{{$value->status->name ?? ''}}</td>

                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{$show_data->links('pagination::bootstrap-4')}}
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Assign User End -->
<div class="modal fade" id="asignUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.assign')}}" id="order_assign">
      <div class="modal-body">
        <div class="form-group">
            <select name="user_id" id="user_id" class="form-select">
                <option value="">---Select---</option>
                @foreach($users as $key=>$value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Assign User End-->

<div class="modal fade" id="changeStatus" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Order Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.status')}}" id="order_status_form">
      <div class="modal-body">
        <div class="form-group">
            <select name="order_status" id="order_status" class="form-select">
                <option value="">---Select---</option>
                @foreach($orderstatus as $key=>$value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- pathao coureir start -->
<div class="modal fade" id="pathao" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pathao Courier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('admin.order.pathao')}}" id="order_sendto_pathao">

      <div class="modal-body">
        <div class="form-group">
            <label for="pathaostore" class="form-label">Store</label>
           <select name="pathaostore" id="pathaostore" class="pathaostore form-select" >
             <option value="">Select Store...</option>
             @if(isset($pathaostore['data']['data']))
                @foreach($pathaostore['data']['data'] as $key=>$store)
                <option value="{{$store['store_id']}}">{{$store['store_name']}}</option>
                @endforeach
                @else
             @endif
           </select>
            @if ($errors->has('pathaostore'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaostore') }}</strong>
              </span>
              @endif
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="pathaocity" class="form-label">City</label>
           <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-select" style="width:100%" >
             <option value="">Select City...</option>
             @if(isset($pathaocities['data']['data']))
             @foreach($pathaocities['data']['data'] as $key=>$city)
             <option value="{{$city['city_id']}}">{{$city['city_name']}}</option>
             @endforeach
             @else
             @endif
           </select>
            @if ($errors->has('pathaocity'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaocity') }}</strong>
              </span>
              @endif
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="" class="form-label">Zone</label>
             <select name="pathaozone" id="pathaozone" class="pathaozone chosen-select form-control  {{ $errors->has('pathaozone') ? ' is-invalid' : '' }}" value="{{ old('pathaozone') }}"  style="width:100%">
            </select>
             @if ($errors->has('pathaozone'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaozone') }}</strong>
              </span>
              @endif
        </div>
        <!-- form group end -->
        <div class="form-group mt-3">
          <label for="" class="form-label">Area</label>
             <select name="pathaoarea" id="pathaoarea" class="pathaoarea chosen-select form-control  {{ $errors->has('pathaoarea') ? ' is-invalid' : '' }}" value="{{ old('pathaoarea') }}"  style="width:100%">
            </select>
             @if ($errors->has('pathaoarea'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('pathaoarea') }}</strong>
              </span>
              @endif
        </div>
        <!-- form group end -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<!-- pathao courier  End-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".checkall").on('change',function(){
      $(".checkbox").prop('checked',$(this).is(":checked"));
    });

    // order assign
    $(document).on('submit', '#order_assign', function (e) {
        e.preventDefault();

        let $form = $(this);
        let url = $form.attr('action');
        let method = $form.attr('method');

        let user_id = $form.find('#user_id').val();

        let order_ids = $('input.checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        /*--------Validation----------*/
        if (!user_id) {
            toastr.error('Please select a user!');
            return;
        }

        if (order_ids.length === 0) {
            toastr.error('Please select at least one order!');
            return;
        }

        let $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.prop('disabled', true).text('Processing...');

        $.ajax({
            type: method,
            url: url,
            data: {
                user_id: user_id,
                order_ids: order_ids,
            },
            success: function (res) {
                if (res.status === 'success') {
                    $("#asignUser").modal('hide');
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message || 'Something went wrong!');
                }
            },
            error: function (xhr) {
                toastr.error('Server error! Please try again.');
                console.error(xhr.responseText);
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text('Submit');
            }
        });
    });

    /*--------order status change----------*/
    $(document).on('submit', '#order_status_form', function (e) {
        e.preventDefault();

        let $form = $(this);
        let url = $form.attr('action');
        let method = $form.attr('method');

        let order_status = $form.find('#order_status').val();

        let order_ids = $('input.checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        /* -------- Validation -------- */
        if (!order_status) {
            toastr.error('Please select status!');
            return;
        }

        if (order_ids.length === 0) {
            toastr.error('Please select at least one order!');
            return;
        }

        let $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.prop('disabled', true).text('Processing...');

        $.ajax({
            type: method,
            url: url,
            data: {
                order_status: order_status,
                order_ids: order_ids,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.status === 'success') {
                    $('#changeStatus').modal('hide');
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message || 'Something went wrong!');
                }
            },
            error: function (xhr) {
                toastr.error('Server error! Please try again.');
                console.error(xhr.responseText);
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text('Submit');
            }
        });
    });
    // order delete
    $(document).on('click', '.order_delete', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();

        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }

        $.ajax({
           type:'GET',
           url:url,
           data:{order_ids},
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

    // multiple print
    $(document).on('click', '.multi_order_print', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();

        if(order_ids.length ==0){
            toastr.error('Please Select Atleast One Order!');
            return ;
        }
        $.ajax({
           type:'GET',
           url,
           data:{order_ids},
           success:function(res){
               if(res.status=='success'){
                   console.log(res.items, res.info);
                   var myWindow = window.open("", "_blank");
                   myWindow.document.write(res.view);
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
    // multiple courier
    $(document).on('click', '.multi_order_courier', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var order = $('input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var order_ids=order.get();

        if(order_ids.length ==0){
            toastr.error('Please Select An Order First !');
            return ;
        }

        $.ajax({
           type:'GET',
           url:url,
           data:{order_ids},
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
})
</script>
@endsection
