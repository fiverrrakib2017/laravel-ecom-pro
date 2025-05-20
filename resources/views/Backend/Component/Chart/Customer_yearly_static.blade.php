<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-dark text-white">Yearly Customers Chart</div>
        <div class="card-body">
            <canvas id="customer_chart"></canvas>
        </div>
    </div>
</div>

@php
    /*When Pass Pop Id*/
     $get_pop_id=0;
    if(!empty($pop_id)){
        $get_pop_id=$pop_id;
    }else if(!empty($branch_user_id)){
        $get_pop_id=$branch_user_id;
    }else{
        $get_pop_id=0;
    }
    /*When Pass Area Id*/
    $get_area_id=0;
    if(!empty($area_id)){
        $get_area_id=$area_id;
    }else{
        $get_area_id=0;
    }
    
    $year = now()->year;
    $monthly_customers = \App\Models\Customer::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->whereYear('created_at', $year)
        ->when($get_pop_id, fn($q) => $q->where('pop_id', $get_pop_id))
        ->when($get_area_id, fn($q) => $q->where('area_id', $get_area_id))
        ->groupByRaw('MONTH(created_at)')
        ->orderByRaw('MONTH(created_at)')
        ->pluck('total', 'month')
        ->toArray();

    $chart_data = [];
    for ($i = 1; $i <= 12; $i++) {
        $chart_data[] = $monthly_customers[$i] ?? 0;
    }
@endphp
<script type="text/javascript">
$(document).ready(function(){
const customerData = @json($chart_data);

    const ctx = document.getElementById('customer_chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Customers',
                data: customerData,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

</script>

