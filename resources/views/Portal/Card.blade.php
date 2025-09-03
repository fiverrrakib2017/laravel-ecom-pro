<div class="row">
    @php
        $status = strtolower(auth('customer')->user()->status ?? 'offline');
        $isOnline = $status === 'online';

        $get_total_due =  \App\Models\Customer_recharge::where('customer_id', auth('customer')->user()->id)->where('transaction_type', 'credit')->sum('amount') ?? 0;
        $duePaid = \App\Models\Customer_recharge::where('customer_id', auth('customer')->user()->id)->where('transaction_type', 'due_paid')->sum('amount') ?? 0;

        $totalDue = $get_total_due - $duePaid;
    @endphp

    <div class="col-lg-3 col-6">
        <div class="small-box {{ $isOnline ? 'bg-success' : 'bg-danger' }}">
            <div class="inner">
                <h3 class="mb-1">
                    {{ ucfirst($status) }}
                </h3>
                <p class="mb-0">Account Status</p>
            </div>
            <div class="icon">
                @if ($isOnline)
                    <i class="fas fa-signal"></i>
                @else
                    <i class="fas fa-times-circle"></i>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3 class="mb-1">{{$totalDue}} ৳</h3>
                <p class="mb-0">Total Due</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 class="mb-1"> {{ \Carbon\Carbon::parse(auth('customer')->user()->expire_date)->format('d M Y') }}
                </h3>
                <p class="mb-0">Next Billing</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3 class="mb-1">Package</h3>
                <p class="mb-0">{{ \App\Models\Branch_package::find(auth('customer')->user()->package_id)->name }} •
                    Unlimited</p>
            </div>
            <div class="icon"><i class="fas fa-wifi"></i></div>
        </div>
    </div>
</div>
