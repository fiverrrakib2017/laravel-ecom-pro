@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')

@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <h4>Credit Recharge List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        @php
                            $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;

                            $dataQuery = App\Models\Customer_recharge::select(DB::raw('MAX(id) as id'))->groupBy('customer_id',);

                            if ($branch_user_id) {
                                $dataQuery->whereHas('customer', function ($query) use ($branch_user_id) {
                                    $query->where('pop_id', $branch_user_id);
                                });
                            }

                            $data = $dataQuery->get();
                        @endphp

                        <table id="customer_datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>POP/Branch</th>
                                    <th>Area</th>
                                    <th>Phone Number</th>
                                    <th>Month</th>
                                    <th>Recharged</th>
                                    <th>Total Paid</th>
                                    <th>Total Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;

                                    $dataQuery = App\Models\Customer_recharge::select(DB::raw('MAX(id) as id'))->groupBy('customer_id');

                                    if ($branch_user_id) {
                                        $dataQuery->whereHas('customer', function ($query) use ($branch_user_id) {
                                            $query->where('pop_id', $branch_user_id);
                                        });
                                    }

                                    $data = $dataQuery->get();
                                @endphp

                                @foreach ($data as $item)
                                    @php
                                        /* Credit month collect*/
                                        $credit_recharges = App\Models\Customer_recharge::where(
                                            'customer_id',
                                            $item->customer_id,
                                        )
                                            ->where('transaction_type', 'credit')
                                            ->get(['recharge_month', 'amount']);

                                        /*Due paid month collect*/
                                        $due_paids = App\Models\Customer_recharge::where(
                                            'customer_id',
                                            $item->customer_id,
                                        )
                                            ->where('transaction_type', 'due_paid')
                                            ->get(['recharge_month', 'amount']);

                                        $paid_months = $due_paids->pluck('recharge_month')->toArray();

                                        $unpaid_credits = [];
                                        $total_due = 0;

                                        foreach ($credit_recharges as $credit) {
                                            if (!in_array($credit->recharge_month, $paid_months)) {
                                                $unpaid_credits[] = $credit->recharge_month;
                                                $total_due += $credit->amount;
                                            }
                                        }

                                        $total_recharge = App\Models\Customer_recharge::where(
                                            'customer_id',
                                            $item->customer_id,
                                        )
                                            ->where('transaction_type', '!=', 'due_paid')
                                            ->sum('amount');

                                        $total_paid = App\Models\Customer_recharge::where(
                                            'customer_id',
                                            $item->customer_id,
                                        )
                                            ->where('transaction_type', '!=', 'credit')
                                            ->sum('amount');
                                    @endphp

                                    @if ($total_due !== 0)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.customer.view', $item->customer->id) }}"
                                                    style="display: flex; align-items: center; text-decoration: none; color: #333;">
                                                    @if ($item->customer->status == 'online')
                                                        <i class="fas fa-unlock"
                                                            style="font-size: 15px; color: green; margin-right: 8px;"></i>
                                                    @else
                                                        <i class="fas fa-lock"
                                                            style="font-size: 15px; color: red; margin-right: 8px;"></i>
                                                    @endif
                                                    &nbsp;<span
                                                        style="font-size: 16px; font-weight: bold;">{{ $item->customer->username }}</span>
                                                </a>
                                            </td>
                                            <td>{{ $item->customer->pop->name }}</td>
                                            <td>{{ $item->customer->area->name }}</td>
                                            <td>{{ $item->customer->phone }}</td>
                                            <td>
                                                @foreach ($unpaid_credits as $month)
                                                    {{ \Carbon\Carbon::parse($month)->format('F Y') }}<br>
                                                @endforeach
                                            </td>
                                            <td>{{ $total_recharge }}</td>
                                            <td>{{ $total_paid }}</td>
                                            <td>{{ $total_due }}</td>
                                        </tr>
                                    @endif
                                @endforeach

                            </tbody>
                        </table>


                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-danger print-btn" onclick="printTable()"><i class="fa fa-print"></i></button>

                    <button class="btn btn btn-success" id="export_to_excel">Export <img
                            src="https://img.icons8.com/?size=100&id=117561&format=png&color=000000"
                            class="img-fluid icon-img" style="height: 20px !important;"></button>

                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#customer_datatable1').DataTable();
        });

        function printTable() {
            var printContents = document.getElementById('customer_datatable1').outerHTML;
            var originalContents = document.body.innerHTML;

            var newWindow = window.open('', '', 'width=800, height=600');
            newWindow.document.write('<html><head><title>Print Table</title>');
            newWindow.document.write('<style>');
            newWindow.document.write('table {width: 100%; border-collapse: collapse; border: 1px solid black;}');
            newWindow.document.write('th, td {border: 2px dotted #ababab; padding: 8px; text-align: left;}');
            newWindow.document.write('</style></head><body>');

            newWindow.document.write('<div class="header">');
            newWindow.document.write(
                '<img src="http://103.146.16.154/assets/images/it-fast.png" class="logo" alt="Company Logo" style="display:block; margin:auto; height:50px;">'
            );
            newWindow.document.write('<h2 style="text-align:center; color: #000;">Star Communication</h2>');
            newWindow.document.write('<p style="text-align:center;">Credit Recharge Report</p>');
            newWindow.document.write('</div>');

            newWindow.document.write(printContents);
            newWindow.document.write('</body></html>');

            newWindow.document.close();
            newWindow.print();
            newWindow.close();
        }
    </script>
@endsection
