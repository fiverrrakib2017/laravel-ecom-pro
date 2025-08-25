@extends('Backend.Layout.App')
@section('title', 'Hostpot Customer List | Admin Panel')
@section('style')

@endsection
@section('content')
    <div class="row">

        <div class="col-md-12 ">
            <div class="card">


            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <!-- Add Customer Button -->
                    <button data-toggle="modal" data-target="#addCustomerModal" type="button" class="btn btn-success mb-2">
                        <i class="fas fa-user-plus"></i> Create Hostpot User
                    </button>


                </div>
            </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        @include('Backend.Component.Customer.Customer', ['connection_type_dropdown'=>false, 'hostpot'=>true]);
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('Backend.Modal.Customer.customer_modal', ['request_from'=>'hotspot',])



@endsection

@section('script')


@endsection
