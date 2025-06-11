<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-primary text-white">Customer Payment(This Month) Status Chart</div>
        <div class="card-body">
            <canvas id="paymentChart"></canvas>
        </div>
    </div>
</div>


<script>
    /*************************** Customer Payment Status Chart ***************************************/
    $(document).ready(function () {
        var ctx4 = document.getElementById('paymentChart').getContext('2d');
        new Chart(ctx4, {
            type: 'doughnut',
            data: {
                labels: ['Recharged', 'Total Paid', 'Total Due', 'Due Paid'],
                datasets: [{
                    data: [total_recharged, totalPaid, totalDue, duePaid],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
                }]
            },
            options: {
                responsive: true
            }
        });
    });
</script>
