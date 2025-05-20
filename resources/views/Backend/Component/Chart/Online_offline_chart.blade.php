<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-primary text-white">Online vs Offline Customers</div>
        <div class="card-body">
            <canvas id="customer_online_offline_chart"></canvas>
        </div>
    </div>
</div>

@php
    /*Get Customer Online Offline Count*/

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

    /*Get Online Customer*/
    $online_customer_query = App\Models\Customer::where('status', 'online');
    if (!empty($get_pop_id)) {
        $online_customer_query->where('pop_id', $get_pop_id);
    }
    if (!empty($get_area_id)) {
        $online_customer_query->where('area_id', $get_area_id);
    }
    $online_customer = $online_customer_query->count();


    /*Get Offline Customer*/
    $offline_customer_query = App\Models\Customer::where('status', 'offline');
    if (!empty($get_pop_id)) {
        $offline_customer_query->where('pop_id', $get_pop_id);
    }
      if (!empty($get_area_id)) {
        $offline_customer_query->where('area_id', $get_area_id);
    }
    $offline_customer = $offline_customer_query->count();
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
