@extends('backEnd.layouts.master')
@section('title','Order Create | Dashboard Admin Panel')

@section('css')
<style>
    .cart-qty-input {
        width: 45px;
        text-align: center;
        border: 1px solid #ced4da;
        border-radius: 4px;
        height: 31px;
    }
    .qty-btn {
        padding: 2px 8px;
    }
    .table-cart td {
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Title & Action -->
   <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <!-- Left -->
                        <div class="d-flex align-items-center">

                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width:55px; height:55px;">
                                <i class="fas fa-shopping-bag fs-4"></i>
                            </div>

                            <div>
                                <h4 class="mb-1">Create Order</h4>
                                <small class="text-muted">
                                    Add products and complete customer orders
                                </small>
                            </div>

                        </div>

                        <!-- Right -->
                        <div class="d-flex align-items-center">

                            <form action="{{ route('admin.order.cart_clear') }}"
                                method="POST"
                                class="mb-0">

                                @csrf

                                <button type="submit"
                                        class="btn btn-danger rounded-pill px-4 delete-confirm">

                                    <i class="fas fa-trash-alt me-2"></i>
                                    Clear Cart

                                </button>

                            </form>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Main Order Form -->
    <form action="{{route('admin.order.store')}}" method="POST" class="pos_form" data-parsley-validate="" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Left Side: Product Selection & Cart Table -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <!-- Product Select -->
                        <div class="form-group mb-3">
                            <label for="cart_add" class="form-label">Select Product <span class="text-danger">*</span></label>
                            <select id="cart_add" class="form-control select2 @error('product_id') is-invalid @enderror">
                                <option value="">Search & Select Product...</option>
                                @foreach($products as $value)
                                    <option value="{{$value->id}}">{{$value->name}} (Code: {{$value->product_code ?? 'N/A'}})</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Cart Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle table-cart mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 10%;">Image</th>
                                        <th style="width: 25%;">Name</th>
                                        <th style="width: 20%;">Quantity</th>
                                        <th style="width: 15%;">Sell Price</th>
                                        <th style="width: 15%;">Discount</th>
                                        <th style="width: 10%;">Sub Total</th>
                                        <th style="width: 5%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cartTable">
                                    @php $product_discount = 0; @endphp
                                    @foreach($cartinfo as $key=>$value)
                                    <tr>
                                        <td class="text-center">
                                            <img height="35" width="35" class="rounded" src="{{asset($value->attributes->image)}}" alt="product" />
                                        </td>
                                        <td>{{$value->name}}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary qty-btn cart_decrement" value="{{$value->quantity}}" data-id="{{$value->id}}">-</button>
                                                <input type="text" class="cart-qty-input" value="{{$value->quantity}}" readonly />
                                                <button type="button" class="btn btn-sm btn-outline-secondary qty-btn cart_increment" value="{{$value->quantity}}" data-id="{{$value->id}}">+</button>
                                            </div>
                                        </td>
                                        <td>৳{{$value->price}}</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm product_discount" value="{{$value->attributes->product_discount}}" placeholder="0.00" data-id="{{$value->id}}" style="width: 80px;" />
                                        </td>
                                        <td>৳{{($value->price - $value->attributes->product_discount) * $value->quantity}}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-xs cart_remove" data-id="{{$value->id}}"><i class="fa fa-times"></i></button>
                                        </td>
                                    </tr>
                                    @php
                                        $product_discount += $value->attributes->product_discount * $value->quantity;
                                        Session::put('product_discount', $product_discount);
                                    @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Customer Info & Order Summary -->
            <div class="col-lg-4">
                <!-- Customer Details Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-light fw-bold">Customer Information</div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Full Name" name="name" value="{{ old('name') }}" required />
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="017XXXXXXXX" name="phone" value="{{ old('phone') }}" required />
                            @error('phone')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <input type="text" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Full Address" name="address" value="{{ old('address') }}" required />
                            @error('address')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="area" class="form-label">Delivery Area <span class="text-danger">*</span></label>
                            <select id="area" class="form-control form-select @error('area') is-invalid @enderror" name="area" required>
                                <option value="">Select Area...</option>
                                <option value="1">ঢাকা সিটির ভিতরে হোম ডেলিভারি</option>
                                <option value="2">ঢাকা সিটির বাহিরে হোম ডেলিভারি</option>
                                <option value="3">কুরিয়ার অফিস থেকে ডেলিভারি</option>
                            </select>
                            @error('area')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Calculation Summary Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-light fw-bold">Payment Summary</div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tbody id="cart_details">
                                @php
                                    $subtotal = Cart::session('pos_shopping')->getSubTotal();
                                    $subtotal = str_replace([',', '.00'], '', $subtotal);
                                    $shipping = Session::get('pos_shipping', 0);
                                    $total_discount = Session::get('pos_discount', 0) + Session::get('product_discount', 0);
                                @endphp
                                <tr>
                                    <td>Sub Total</td>
                                    <td class="text-end fw-bold">৳{{$subtotal}}</td>
                                </tr>
                                <tr>
                                    <td>Shipping Fee</td>
                                    <td class="text-end fw-bold">৳{{$shipping}}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td class="text-end fw-bold text-danger">৳{{$total_discount}}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">Total</td>
                                    <td class="text-end fw-bold fs-5 text-success">৳{{($subtotal + $shipping) - $total_discount}}</td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-success w-100 mt-3 py-2 fw-bold">
                            <i class="fas fa-check-circle me-1"></i> Order Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
 $(document).ready(function () {
    $("#cart_add").select2();
    // Fetch refreshed cart content HTML
    function cart_content() {
        $.ajax({
            type: "GET",
            url: "{{route('admin.order.cart_content')}}",
            dataType: "html",
            success: function (cartinfo) {
                $("#cartTable").html(cartinfo);
            }
        });
    }

    // Fetch refreshed cart total summary HTML
    function cart_details() {
        $.ajax({
            type: "GET",
            url: "{{route('admin.order.cart_details')}}",
            dataType: "html",
            success: function (cartinfo) {
                $("#cart_details").html(cartinfo);
            }
        });
    }

    // Add Product to Cart
    $("#cart_add").on("change", function () {
        var id = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id },
                url: "{{route('admin.order.cart_add')}}",
                dataType: "json",
                success: function (response) {
                    cart_content();
                    cart_details();
                    $("#cart_add").val('').val(null).trigger('change.select2');
                }
            });
        }
    });

    $(document).on("click", ".cart_increment", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                data: { id: id, qty: qty },
                type: "GET",
                url: "{{route('admin.order.cart_increment')}}",
                dataType: "json",
                success: function () {
                    cart_content();
                    cart_details();
                }
            });
        }
    });

    $(document).on("click", ".cart_decrement", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id, qty: qty },
                url: "{{route('admin.order.cart_decrement')}}",
                dataType: "json",
                success: function () {
                    cart_content();
                    cart_details();
                }
            });
        }
    });

    $(document).on("click", ".cart_remove", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: { id: id },
                url: "{{route('admin.order.cart_remove')}}",
                dataType: "json",
                success: function () {
                    cart_content();
                    cart_details();
                }
            });
        }
    });

    $(document).on("change", ".product_discount", function () {
        var id = $(this).data("id");
        var discount = $(this).val();
        $.ajax({
            cache: false,
            type: "GET",
            data: { id: id, discount: discount },
            url: "{{route('admin.order.product_discount')}}",
            dataType: "json",
            success: function () {
                cart_content();
                cart_details();
            }
        });
    });

    $(document).on("change", "#area", function () {
        var id = $(this).val();
        $.ajax({
            type: "GET",
            data: { id: id },
            url: "{{route('admin.order.cart_shipping')}}",
            dataType: "html",
            success: function () {
                cart_content();
                cart_details();
            }
        });
    });
 });
</script>
@endsection
