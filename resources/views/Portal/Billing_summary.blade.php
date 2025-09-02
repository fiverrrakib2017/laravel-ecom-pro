<div class="card">
    <div class="card-header bg-info text-white">
        Billing Summary
    </div>

    <div class="card-body">

         @include('Backend.Component.Customer.recharge_list', ['customer_id' => auth('customer')->user()->id])


    </div>

    <div class="card-footer d-flex justify-content-between align-items-right">
        <a href="#" class="btn btn-success">
            <i class="fas fa-money-check-alt"></i> Pay Now (bKash/Nagad)
        </a>
    </div>
</div>
