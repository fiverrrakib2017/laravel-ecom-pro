@extends('Backend.Layout.App')
@section('title','Dashboard | Admin Panel')
@section('style')
<style>
button#submitButton {
    margin-top: 26px;
}

</style>

@endsection
@section('content')
<!-- br-pageheader -->
<div class="row">
    <div class="container-fluid">
        <form id="form-data" action="{{ route('admin.supplier.invoice.update_invoice') }}" method="post">
            @csrf
            <input type="hidden" name="id" value="{{ $invoice_data->id }}">

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3 col-12 mb-3">
                            <label for="refer_no">Refer No:</label>
                            <input class="form-control" type="text" placeholder="Type Your Refer No" id="refer_no" name="refer_no" value="{{ $invoice_data->refer_no }}">
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <label>Supplier Name</label>
                            <div class="d-flex align-items-center gap-2 w-100">
                                <select id="client_name" name="client_id" class="form-select select2 w-100" style="width: 100%;">
                                    <option value="">---Select---</option>
                                    @php $suppliers = \App\Models\Supplier::latest()->get(); @endphp
                                    @foreach($suppliers as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == $invoice_data->supplier_id ? 'selected' : '' }}>
                                            {{ $item->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#supplierModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <label for="note">Note:</label>
                            <input class="form-control" type="text" placeholder="Notes" id="note" name="note" value="{{ $invoice_data->note }}">
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <label for="currentDate">Invoice Date</label>
                            <input class="form-control" type="date" id="currentDate" name="date" value="{{ $invoice_data->invoice_date }}">
                        </div>
                    </div>
                </div>

                <div class="card-body pb-0">
                    <div class="row align-items-end">
                        <div class="col-md-3 col-12 mb-3">
                            <label for="product_name">Product</label>
                            <div class="d-flex align-items-center gap-2 w-100">
                                <select id="product_name" class="form-select select2 w-100" style="width: 100%;">
                                    <option value="">---Select---</option>
                                    @php $products = \App\Models\Product::latest()->get(); @endphp
                                    @foreach($products as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#productModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-1 col-6 mb-3">
                            <label for="qty">Qty</label>
                            <input type="number" id="qty" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <label for="price">Price</label>
                            <input type="text" id="price" class="form-control" placeholder="00">
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <label for="total_price">Total Price</label>
                            <input id="total_price" type="text" class="form-control" placeholder="00">
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <label for="details">Notes</label>
                            <input id="details" type="text" class="form-control" placeholder="Notes">
                        </div>
                        <div class="col-md-2 col-12 mb-3 text-right">
                            <button type="button" id="submitButton" class="btn btn-success w-100">
                                <i class="fas fa-plus-circle"></i> Add Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="card shadow-sm">
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0" id="invoiceTable">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Product List</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableRow">
                            @foreach ($invoice_data->items as $item)
                            <tr>
                                <td>
                                    <input type="hidden" name="table_product_id[]" value="{{ $item->product_id }}">
                                    {{ $item->product->name }}
                                </td>
                                <td>
                                    <input type="hidden" name="table_qty[]" value="{{ $item->qty }}">
                                    {{ $item->qty }}
                                </td>
                                <td>
                                    <input type="hidden" name="table_price[]" value="{{ intval($item->price) }}">
                                    {{ intval($item->price) }}
                                </td>
                                <td>
                                    <input type="hidden" name="table_total_price[]" value="{{ intval($item->total_price) }}">
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
                </div>
                <div class="card-footer text-right">
                    <button type="submit" name="finished_btn" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Invoice
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('Backend.Modal.supplier_modal')
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
        custom_select2('#supplierModal');


        /*********** Submit Customer Form AND Product Form *****************/
        handleSubmit('#supplierForm','#supplierModal');
        handleSubmit('#productForm','#productModal');
            $("#product_name").change(function() {
                var id = $(this).val();
                $.ajax({
                    url: "{{ route('admin.product.edit', ':id') }}".replace(':id', id),
                    method: 'GET',
                    success: function(response) {
                        selectedProductId = response.data.id;
                        var price =response.data.purchase_price;

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
                $('#submitButton').html('Add Now').prop('disabled', false);
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
            $(document).on('click','button[name="finished_btn"]',function(e){
                  e.preventDefault();
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



