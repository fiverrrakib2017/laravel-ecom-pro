<div class="col-md-12">
    <div class="card">
        <div class="card-header bg-primary text-white">Daily Usages By Customers</div>
        <div class="card-body">
            <canvas id="customer_usages_chart"></canvas>
        </div>
    </div>
</div>

@php
    // Fetch daily usage data (upload and download) from daily_usages table
    $dailyUsages = DB::table('daily_usages')
                    ->select('date', DB::raw('sum(upload) as total_upload, sum(download) as total_download'))
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get();

    // Convert MB to GB by dividing the totals by 1024
    $dates = $dailyUsages->pluck('date')->toArray();
    $uploadsInGB = $dailyUsages->pluck('total_upload')->map(function($upload) {
        return $upload / 1024;  // Convert to GB
    })->toArray();

    $downloadsInGB = $dailyUsages->pluck('total_download')->map(function($download) {
        return $download / 1024;  // Convert to GB
    })->toArray();
@endphp

<script type="text/javascript">
    $(document).ready(function() {
        var dates = @json($dates);
        var uploadsInGB = @json($uploadsInGB);
        var downloadsInGB = @json($downloadsInGB);

        /*************************** Daily Usages By Customers (Upload and Download) ***************************************/
        var ctx2 = document.getElementById('customer_usages_chart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Total Upload (GB)',
                        data: uploadsInGB,
                        borderColor: '#28a745',
                        fill: false,
                    },
                    {
                        label: 'Total Download (GB)',
                        data: downloadsInGB,
                        borderColor: '#dc3545',
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2) + ' GB';  // Show 2 decimal places
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Usage (GB)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
