@extends('Backend.Layout.App')
@section('title','Dashboard | Admin Panel')
@section('style')
<style>
button#submitButton {
    margin-top: 29px;
}

</style>

@endsection
@section('content')
<!-- br-pageheader -->
<div class="row">
    <div class="container-fluid">
        <form id="form-data" action="{{ route('admin.client.invoice.update_invoice') }}" method="post">
            @csrf
            <input type="text" value="{{ $invoice_data->id }}" name="id" class="d-none">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                        <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="refer_no" class="form-label">Refer No:</label>
                                        <input class="form-control" type="text" placeholder="Type Your Refer No" id="refer_no" name="refer_no">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group ">
                                        <label>Client Name</label>
                                        <div class="d-flex">
                                        <select type="text" id="client_name" name="client_id" class="form-select select2" style="width: 100%;">
                                            <option value="">---Select---</option>
                                            @php
                                            $customers = \App\Models\Client::latest()->get();
                                            @endphp
                                            @if($customers->isNotEmpty())
                                                @foreach($customers as $item)
                                                <option value="{{ $item->id }}" {{ $item->id == $invoice_data->client_id ? 'selected' : '' }}>
                                                    {{ $item->fullname }}
                                                </option>
                                                @endforeach
                                            @endif
                                        </select>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#CustomerModal"><span>+</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="note" class="form-label">Note:</label>
                                        <input class="form-control" type="text" placeholder="Notes" id="note" name="note" value="{{ $invoice_data->note }}">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="currentDate" class="form-label">Invoice Date</label>
                                        <input class="form-control" type="date" id="currentDate" name="date" value="{{ $invoice_data->invoice_date }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="product_item" class="form-label">Product</label>
                                        <div class="d-flex">
                                            <select id="product_name" class="form-select select2" aria-label="Product Name">
                                                <option value="">---Select---</option>
                                                @php
                                                $products = \App\Models\Product::latest()->get();
                                                @endphp
                                                @if($products->isNotEmpty())
                                                    @foreach($products as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <button type="button" class="btn btn-primary add-product-btn" data-bs-toggle="modal" data-bs-target="#productModal">
                                                <span>+</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="qty" class="form-label">Quantity</label>
                                        <input type="number" id="qty" class="form-control" min="1" value="1">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="text" class="form-control price" id="price" placeholder="00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="total_price" class="form-label">Total Price</label>
                                        <input id="total_price" type="text" class="form-control total_price" placeholder="00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="details" class="form-label">Notes</label>
                                        <input id="details" type="text" class="form-control" placeholder="Notes">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group ">
                                    <button type="button" id="submitButton" class="btn btn-primary">Add Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered" id="invoiceTable">
                    <thead class="bg bg-info text-white">
                        <th>Product List</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th></th>
                    </thead>
                    <tbody id="tableRow">
                        @foreach ($invoice_data->items  as $item)
                        <tr>
                            <td>
                                <input type="hidden" name="table_product_id[]" value="{{ $item->product_id }}" class="d-none">
                                {{ $item->product->name }}
                            </td>
                            <td>
                                <input type="hidden" name="table_qty[]" value="{{ $item->qty }}" class="d-none">
                                {{ $item->qty }}
                            </td>
                            <td>
                                <input type="hidden" name="table_price[]" value="{{ $item->price }}" class="d-none">
                                {{ intval($item->price) }}
                            </td>
                            <td>
                                <input type="hidden" name="table_total_price[]" value="{{ intval($item->total_price) }}" class="d-none">
                                {{ intval($item->total_price) }}
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm removeRow">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    </table>
                    <div class="form-group text-end">
                        <button type="button" name="finished_btn" class="btn btn-success"><i class="fe fe-dollar"></i>Process</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@include('Backend.Modal.Client.client_modal')
@include('Backend.Modal.product_modal')
@include('Backend.Modal.invoice_modal')
@endsection


@section('script')
<script  src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
<script  src="{{ asset('Backend/assets/js/custom_select.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var selectedProductId = null;
        /*********** Select 2 *****************/
        custom_select2_without_modal('#form-data');
        custom_select2('#productModal');
        custom_select2('#CustomerModal');


        /*********** Submit Customer Form AND Product Form *****************/
        handleSubmit('#CustomerForm','#CustomerModal');
        handleSubmit('#productForm','#productModal');
            $("#product_name").change(function() {
                var id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.product.edit', ':id') }}".replace(':id', id),
                    method: 'GET',
                    success: function(response) {
                        selectedProductId = response.data.id;
                        var price =response.data.sale_price;

                        $('#price').val(price);
                        updateTotalPrice();

                    },
                    error: function(xhr, status, error) {
                        /*handle the error response*/
                        console.log(error);
                    }
                });
            });
            $('#qty').on('input', function() {
                updateTotalPrice();
            });
            $('#price').on('input', function() {
                updateTotalPrice();
            });

            function updateTotalPrice() {
                var qty = $('#qty').val();
                var price = $('#price').val();
                var total = qty * price;
                $('#total_price').val(total);
            }
            $(document).on('click','#submitButton',function(e){
                e.preventDefault();
                var productName = $('#product_name option:selected').text();
                var quantity = $('#qty').val();
                var price = $('#price').val();
                var totalPrice = $('#total_price').val();

                if(!selectedProductId || !quantity || !price || !totalPrice) {
                    toastr.error('Please fill in all fields');
                    return;
                }
                /*Check if the product is already added to the table*/
                var isProductAdded = false;
                $('#tableRow tr').each(function() {
                    var tableProductId = $(this).find('input[name="table_product_id[]"]').val();
                    if (tableProductId == selectedProductId) {
                        isProductAdded = true;
                        return false;
                    }
                });

                if (isProductAdded) {
                    toastr.error('Product is already added. <br> Please update the quantity instead.');
                    return;
                }
                $('#submitButton').html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>`).prop('disabled', true);
                $.ajax({
                    url: "{{ route('admin.product.check_product_qty') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { product_id: selectedProductId, qty: quantity },
                    success: function(response) {
                        if (response.success==true) {
                            var row = `<tr>
                                        <td>
                                            <input type="hidden" name="table_product_id[]" value="${selectedProductId}" class="d-none">
                                            ${productName}
                                        </td>
                                        <td>
                                            <input type="hidden" name="table_qty[]" value="${quantity}" class="d-none">
                                            ${quantity}
                                        </td>
                                        <td>
                                            <input type="hidden" name="table_price[]" value="${price}" class="d-none">
                                            ${price}
                                        </td>
                                        <td>
                                            <input type="hidden" name="table_total_price[]" value="${totalPrice}" class="d-none">
                                            ${totalPrice}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm removeRow">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>`;
                                $("#tableRow").append(row);



                            calculateTotalAmount();

                            /*Clear The Fild*/
                            $('#product_name').val('');
                            $('#qty').val('1');
                            $('#price').val('');
                            $('#total_price').val('');
                            selectedProductId = null;
                        } else if(response.success==false) {
                            /*IF The Stock Is Not Available*/
                            toastr.error(response.message);
                        }

                    },
                    error: function() {
                      //  toastr.error('Error checking product stock.');

                    },
                    complete:function(){
                        $('#submitButton').html('Submit').prop('disabled', false);
                    }
                });
            });
            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                calculateTotalAmount();
            });
            /* Calculate total amount function*/
            function calculateTotalAmount() {
                var totalAmount = 0;
                $('#tableRow tr').each(function() {
                    var total_price = $(this).find('input[name="table_total_price[]"]').val();
                    totalAmount += parseFloat(total_price);
                });
                $('input[name="table_total_amount"]').val(totalAmount);

                // Calculate Due Amount
                var paidAmount = parseFloat($('input[name="table_paid_amount"]').val()) || 0;
                var discountAmount = parseFloat($('input[name="table_discount_amount"]').val()) || 0;
                var dueAmount = totalAmount - paidAmount - discountAmount;
                $('input[name="table_due_amount"]').val(dueAmount);
            }
            /*Update Due Amount when Paid Amount or Discount changes*/
            $(document).on('input', 'input[name="table_paid_amount"], input[name="table_discount_amount"]', function() {
                calculateTotalAmount();
            });
            $('#save_invoice_btn').on('click', function() {
                var mainFormData = $('#form-data').serializeArray();
                var modalFormData = $('#paymentForm').serializeArray();
                var allFormData = $.merge(mainFormData, modalFormData);
                var isValid = true;
                /*Validate each form data field*/
                $.each(allFormData, function(index, field) {
                    if (field.name==='client_id' && field.value === '') {
                        toastr.error("Client must be selected!");
                        isValid = false;
                        return false;
                    }
                    else if (field.name === 'table_paid_amount' && field.value === '') {
                        toastr.error("Paid Amount is required");
                        isValid = false;
                        return false;
                    }
                    else if (field.name === 'date' && field.value === '') {
                        toastr.error("Invoice Date is required!");
                        isValid = false;
                        return false;
                    }
                    else if (field.name === 'table_status' && field.value === '') {
                        toastr.error("Type is required!");
                        isValid = false;
                        return false;
                    }
                });

                if (!isValid) {
                    return false;
                }
                $(this).prop('disable',true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>');
                $.ajax({
                    type:'POST',
                    url:$("#form-data").attr('action'),
                    data:$.param(allFormData),
                    dataType: 'json',
                    success: function(response) {
                        if (response.original.success) {
                            toastr.success(response.original.message);
                            /*Close the invoice modal*/
                            $('#invoiceModal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            toastr.error(response.original.message);
                        }
                        $('#save_invoice_btn').prop('disabled', false).html('Save Invoice');
                    },
                    error:function(xhr,status,error){
                        toastr.error("Error:"+error);
                        $('#save_invoice_btn').prop('disabled', false).html('Save Invoice');
                    }
                });
            });
            $(document).on('click','button[name="finished_btn"]',function(){
                $('#invoiceModal').modal('show');
            });
        });
    function __get_short_string(str,num){
        if(str.length <=num){
          return str;
        }
       return str.slice(0,num)+'...';
    }
</script>


@endsection



