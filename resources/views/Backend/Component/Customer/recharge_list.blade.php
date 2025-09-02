 @php
    $customer_id ?? ''
@endphp

 <table id="recharge_datatable"
     class="table table-bordered dt-responsive nowrap"style="border-collapse: collapse; border-spacing: 0; width: 100%;">
     <thead>
         <tr>
             <th>Date</th>
             <th>Months</th>
             <th>Type</th>
             <th>Remarks</th>
             <th>Paid until</th>
             <th>Amount</th>
             <th>Vouchar No.</th>
             <th></th>
         </tr>
     </thead>
     <tbody>
         @php
             $total_recharge_data = App\Models\Customer_recharge::where('customer_id', auth('customer')->user()->id)
                 ->latest()
                 ->get();
         @endphp
         @foreach ($total_recharge_data as $item)
             <tr>
                 <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                 </td>
                 @php
                     $months = explode(',', $item->recharge_month);

                     $cleanMonths = array_filter(array_map('trim', $months));
                     $uniqueMonths = array_unique($cleanMonths);

                     $formattedMonths = [];
                     foreach ($uniqueMonths as $month) {
                         /*Convert month string to timestamp and format it*/
                         $time = strtotime($month);
                         if ($time !== false) {
                             $formattedMonths[] = date('F Y', $time);
                         } else {
                             $formattedMonths[] = $month;
                         }
                     }
                 @endphp
                 <td>{!! implode('<br>', $formattedMonths) !!}</td>
                 <td>
                     @if ($item->transaction_type == 'cash')
                         <span class="badge bg-success">{{ ucfirst($item->transaction_type) }}</span>
                     @elseif($item->transaction_type == 'credit')
                         <span class="badge bg-danger">{{ ucfirst($item->transaction_type) }}</span>
                     @elseif($item->transaction_type == 'due_paid')
                         <span class="badge bg-success">{{ ucfirst($item->transaction_type) }}</span>
                     @elseif($item->transaction_type == 'bkash')
                         <span class="badge bg-success">{{ ucfirst($item->transaction_type) }}</span>
                     @else
                         <span class="badge bg-danger">{{ ucfirst($item->transaction_type) }}</span>
                     @endif
                 </td>

                 <td>{{ ucfirst($item->note ?? 'N/A') }}</td>
                 <td>
                     @if ($item->paid_until)
                         {{ \Carbon\Carbon::parse($item->paid_until)->format('d M Y') }}
                     @else
                         <span class="text-muted">N/A</span>
                     @endif
                 </td>


                 <td>{{ number_format($item->amount, 2) }} BDT</td>
                 <td>{{ $item->voucher_no ?? 'N/A' }} </td>
                 <td>
                     <button class="btn btn-success btn-sm customer_recharge_print_btn" data-id="{{ $item->id }}"><i
                             class="fas fa-print"></i>
                     </button>
                 </td>

             </tr>
         @endforeach
     </tbody>
 </table>



 <script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {
 $("#recharge_datatable").DataTable({
        "responsive": true,
        "autoWidth": false,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "emptyTable": "No recharge data available",
            "zeroRecords": "No matching records found"
        },
        "order": [[0, 'desc']],
    });
 });
</script>
