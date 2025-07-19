@extends('Backend.Layout.App')
@section('title', 'Customer List | Admin Panel')
@section('style')

{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
@endsection
@section('content')
    <div class="row">

        <div class="col-md-12 ">
            <div class="card">
             @php
                use App\Models\Customer;
                $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;

                $baseQuery = Customer::query()->where('is_delete', '0');
                if (!empty($branch_user_id)) {
                    $baseQuery->where('pop_id', $branch_user_id);
                }

                $statusCounts = [
                    'online' => (clone $baseQuery)->where('status', 'online')->count(),
                    'offline' => (clone $baseQuery)->where('status', 'offline')->count(),
                    'expired' => (clone $baseQuery)->where('status', 'expired')->count(),
                    'disabled' => (clone $baseQuery)->where('status', 'disabled')->count(),
                    'discontinue' => (clone $baseQuery)->where('status', 'discontinue')->count(),
                ];

                $active_customer = (clone $baseQuery)
                    ->whereNotIn('status', ['disabled', 'discontinue'])
                    ->count();

                $total_customer = (clone $baseQuery)->count();
            @endphp

            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <!-- Add Customer Button -->
                    <button data-toggle="modal" data-target="#addCustomerModal" type="button" class="btn btn-success mb-2">
                        <i class="fas fa-user-plus"></i> Add New Customer
                    </button>

                    <!-- Customer Counters -->
                    <div class="d-flex flex-wrap" style="gap: 5px;">
                        <a href="{{ url()->current() }}" class="badge badge-info p-2 text-white">
                            <i class="fas fa-users me-1"></i>
                            Total: <span class="counter-value">{{ $total_customer }}</span>
                        </a>
                        <a href="{{ url()->current() }}?status=online" class="badge badge-success p-2 text-white">
                            <i class="fas fa-user-check me-1"></i>
                            Online: <span class="counter-value">{{ $statusCounts['online'] }}</span>
                        </a>
                        <a href="{{ url()->current() }}?status=offline" class="badge badge-warning p-2 text-white">
                            <i class="fas fa-user-clock me-1"></i>
                            Offline: <span class="counter-value">{{ $statusCounts['offline'] }}</span>
                        </a>
                        <a href="{{ url()->current() }}?status=expired" class="badge badge-danger p-2 text-white">
                            <i class="fas fa-user-times me-1"></i>
                            Expired: <span class="counter-value">{{ $statusCounts['expired'] }}</span>
                        </a>
                        <a href="{{ url()->current() }}?status=disabled" class="badge badge-secondary p-2 text-white">
                            <i class="fas fa-user-lock me-1"></i>
                            Disable: <span class="counter-value">{{ $statusCounts['disabled'] }}</span>
                        </a>
                    </div>
                </div>
            </div>



{{--
                <div class="card-header">
                    <button data-toggle="modal" data-target="#addCustomerModal" type="button" class=" btn btn-success">
                        <i class="fas fa-users"></i> Add New Customer</button>

                    <button type="button" id="bulk_recharge" class="btn btn-danger d-none">Bulk Recharge</button>

                    <button type="button"  id="send_message" class="btn btn-primary text-white d-none"><i class="fas fa-envelope"></i>Send Sms </button>

                    <button type="button" id="change_billing" class="btn btn-primary text-white d-none"><i class="fas fa-envelope"></i>Change Billing </button>

                </div> --}}
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
