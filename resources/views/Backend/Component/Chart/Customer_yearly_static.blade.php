<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-dark text-white">Yearly Customers Chart</div>
        <div class="card-body">
            <canvas id="customer_chart"></canvas>
        </div>
    </div>
</div>



<script type="text/javascript">

    /*************************** Yearly Customers Chat***************************************/
    $.ajax({
        url: "{{ route('admin.customer.yearly_static') }}",
        method: 'POST',
        data: {
            pop_id: {{ $branch_user_id ?? 0 }},
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            var ctx = document.getElementById('customer_chart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Customers',
                        data: response,
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
        },
        error: function (xhr) {
            console.error('AJAX Load Failed:', xhr);
        }
    });
</script>
