@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')

{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <button data-toggle="modal" data-target="#addCustomerModal" type="button" class=" btn btn-success">
                        <i class="fas fa-users"></i> Add New Customer</button>

                    <button type="button" id="bulk_recharge" class="btn btn-danger d-none">Bulk Recharge</button>

                    <button type="button"  id="send_message" class="btn btn-primary text-white d-none"><i class="fas fa-envelope"></i>Send Sms </button>

                    <button type="button" id="change_billing" class="btn btn-primary text-white d-none"><i class="fas fa-envelope"></i>Change Billing </button>

                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        @include('Backend.Component.Customer.Customer')
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('Backend.Modal.Customer.customer_modal')



@endsection

@section('script')
@endsection
