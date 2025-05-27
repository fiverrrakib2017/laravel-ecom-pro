<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-info text-white">Yearly Tickets Chart</div>
        <div class="card-body">
            <canvas id="ticketsChart"></canvas>
        </div>
    </div>
</div>

@php
    // Pop ID
    $get_pop_id = $pop_id ?? $branch_user_id ?? 0;

    // Area ID
    $get_area_id = $area_id ?? 0;

    // Get Yearly Tickets
    $yearly_tickets_query = App\Models\Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
        ->whereYear('created_at', date('Y'));

    if ($get_pop_id) {
        $yearly_tickets_query->where('pop_id', $get_pop_id);
    }
    if ($get_area_id) {
        $yearly_tickets_query->where('area_id', $get_area_id);
    }

    $yearly_tickets = $yearly_tickets_query->groupBy('month')->get()->keyBy('month');

    // Prepare data array for Chart.js
    $chartData = [];
    for ($i = 1; $i <= 12; $i++) {
        $chartData[] = $yearly_tickets[$i]->count ?? 0;
    }
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var ctx3 = document.getElementById('ticketsChart').getContext('2d');
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Total Tickets in {{ date("Y") }}',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    data: @json($chartData),
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
    });
</script>
