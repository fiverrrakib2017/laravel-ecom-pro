@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header bg-info text-white d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt me-2 text-white fs-4"></i>&nbsp;
                    <h5 class="mb-0 fw-semibold">Payment History </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        @include('Backend.Component.Customer.payment_history')
                    </div>
                </div>
            </div>

        </div>
    </div>



@endsection


