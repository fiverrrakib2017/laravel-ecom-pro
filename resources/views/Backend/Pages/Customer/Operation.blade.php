@extends('Backend.Layout.App')
@section('title', 'Customer Operation | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="card-title"><i class="fas fa-coins"></i>Customer Operation</h4>

                </div>
                <div class="card-body ">
                    @include('Backend.Component.Customer.search-form')
                </div>
                <div class="card-body d-none" id="print_area">

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" id="change_package_btn" class="btn btn-primary mb-2">
                             <i class="fas fa-credit-card"></i>&nbsp; Change Package
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive responsive-table">
                        @include('Backend.Component.Customer.table')

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

            /******When Bulk Recharge Button Clicked**********/
            handle_bulk_recharge_trigger('#bulk_recharge_btn', '#bulk_rechargeModal', '#selectedCustomerCount');
            /******When Grace Recharge Button Clicked**********/
            handle_bulk_recharge_trigger('#grace_recharge_btn', '#graceRechargeModal', '#grace_recharge_customer_Count');
            

            /*Call bulk recharge Function*/
            handle_ajax_submit('#bulk_rechargeForm');
            /*Call Grace recharge Function*/
            handle_ajax_submit('#grace_rechargeForm', function () {
                $('#graceRechargeModal').modal('hide');
                setTimeout(() => location.reload(), 500);
            });

    </script>

@endsection
