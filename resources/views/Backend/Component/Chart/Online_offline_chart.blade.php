<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-primary text-white">Online vs Offline Customers</div>
        <div class="card-body">
            <canvas id="customer_online_offline_chart"></canvas>
        </div>
    </div>
</div>

@php
    /*Branch Transaction Current Balance*/
    $online_customer = App\Models\Customer::where('status','online')->count();
    $offline_customer = App\Models\Customer::where('status','offline')->count();
@endphp

<script type="text/javascript">

    $(document).ready(function() {
        var onlineCustomer = <?php echo json_encode($online_customer); ?>;
        var offlineCustomer = <?php echo json_encode($offline_customer); ?>;

        /*************************** Online vs Offline Customers ***************************************/
        var ctx2 = document.getElementById('customer_online_offline_chart').getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Online', 'Offline'],
                datasets: [{
                    data: [onlineCustomer, offlineCustomer],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true
            }
        });
    });
</script>
