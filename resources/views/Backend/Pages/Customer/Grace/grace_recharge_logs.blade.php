@extends('Backend.Layout.App')
@section('title', 'Grace Recharge Logs | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header bg-info text-white d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt me-2 text-white fs-4"></i>&nbsp;
                    <h5 class="mb-0 fw-semibold">Grace Recharge Logs </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        @include('Backend.Component.Customer.grace_recharge_logs')
                    </div>
                </div>
            </div>

        </div>
    </div>



@endsection


