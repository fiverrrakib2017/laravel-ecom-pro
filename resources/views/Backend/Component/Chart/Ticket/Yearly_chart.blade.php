<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-info text-white">Yearly Tickets Chart (Status Wise)</div>
        <div class="card-body">
            <canvas id="ticketsChart"></canvas>
        </div>
    </div>
</div>

@php
    $get_pop_id = $pop_id ?? $branch_user_id ?? 0;
    $get_area_id = $area_id ?? 0;

    $statuses = ['total' => null, 'complete' => '1', 'pending' => '0'];

    $tickets_by_status = [];

    foreach ($statuses as $key => $status) {
        $query = App\Models\Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'));

        if ($get_pop_id) {
            $query->where('pop_id', $get_pop_id);
        }
        if ($get_area_id) {
            $query->where('area_id', $get_area_id);
        }
        if (!is_null($status)) {
            $query->where('status', $status);
        }

        $data = $query->groupBy('month')->get()->keyBy('month');

        /*prepare 12 months data*/
        $monthly = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthly[] = $data[$i]->count ?? 0;
        }
        $tickets_by_status[$key] = $monthly;
    }
@endphp

<script>
    $(document).ready(function () {
        var ctx = document.getElementById('ticketsChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    // {
                    //     label: 'Total Tickets',
                    //     borderColor: 'rgba(54, 162, 235, 1)',
                    //     backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    //     data: @json($tickets_by_status['total']),
                    //     fill: true,
                    //     tension: 0.4
                    // },
                    {
                        label: 'Completed',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        data: @json($tickets_by_status['complete']),
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Pending',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        data: @json($tickets_by_status['pending']),
                        fill: true,
                        tension: 0.4
                    }
                ]
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
