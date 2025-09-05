<div class="card">
    <div class="card-header bg-info text-white">
        Billing Summary
    </div>

    <div class="card-body">

         @include('Backend.Component.Customer.recharge_list', ['customer_id' => auth('customer')->user()->id])


    </div>

    <div class="card-footer bg-white d-flex justify-content-end">
    <button type="button" data-toggle="modal" data-target="#CustomerRechargeModal" class="btn btn-danger bg-white d-flex align-items-center px-4 shadow-sm">
        <img src="{{ asset('Backend/images/bkash.png') }}"alt="bKash" style="height:22px; margin-right:8px; border-radius:3px; padding:2px; background:white;">
        Pay Now via <strong class="ml-1">bKash</strong>
    </button>
</div>



</div>


@include('Backend.Modal.Customer.Recharge.Recharge_modal',
 [
    'multiple_select_month' =>'no',
    'transaction_type'      => 'disabled',
    'message_check'         =>'disabled',
    'payable_amount'        =>'disabled',
    'package_name'          =>auth('customer')->user()->package->name ?? '',
    'customer_amount'       =>auth('customer')->user()->amount ?? ''


])

