@extends('Backend.Layout.App')
@section('title', 'Dashboard | OLT Management | Admin Panel')
@section('content')


<div class="container-fluid">
    <div class="row">

        @php
            $devices = [
                ['id' => 1, 'name' => 'Huawei OLT 1234', 'ip' => '192.168.1.100', 'status' => 'active', 'brand' => 'Huawei', 'model' => 'HG8245H', 'location' => 'Dhaka', 'cpu' => 55, 'ram' => 70],
                
                ['id' => 2, 'name' => 'ZTE OLT 5678', 'ip' => '192.168.1.101', 'status' => 'inactive', 'brand' => 'ZTE', 'model' => 'ZXA10', 'location' => 'Chittagong', 'cpu' => 30, 'ram' => 45],

                ['id' => 3, 'name' => 'Fiberhome OLT 9012', 'ip' => '192.168.1.102', 'status' => 'maintenance', 'brand' => 'Fiberhome', 'model' => 'AN5506-04', 'location' => 'Khulna', 'cpu' => 80, 'ram' => 60],

                ['id' => 4, 'name' => 'Viewsonic OLT 9012', 'ip' => '192.168.1.102', 'status' => 'active', 'brand' => 'Fiberhome', 'model' => 'AN5506-04', 'location' => 'Cumilla', 'cpu' => 80, 'ram' => 60],
            ];
        @endphp

        @foreach($devices as $device)
            <div class="col-md-6">
                <div class="card @if($device['status'] == 'active') card-success 
                                 @elseif($device['status'] == 'inactive') card-danger 
                                 @elseif($device['status'] == 'maintenance') card-warning 
                                 @else card-secondary @endif">
                    <div class="card-header">
                        <h5 class="card-title">{{ $device['name'] }}</h5>
                    </div>
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="col-md-6">
                            <p><strong>IP Address:</strong> {{ $device['ip'] }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge 
                                    @if($device['status'] == 'active') badge-success 
                                    @elseif($device['status'] == 'inactive') badge-danger 
                                    @elseif($device['status'] == 'maintenance') badge-warning 
                                    @else badge-secondary @endif">
                                    {{ ucfirst($device['status']) }}
                                </span>
                            </p>
                            <p><strong>Brand:</strong> {{ $device['brand'] }}</p>
                            <p><strong>Model:</strong> {{ $device['model'] }}</p>
                            <p><strong>Location:</strong> {{ $device['location'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <canvas id="usageChart-{{ $device['id'] }}" style="width: 100px; height: 120px;"></canvas>
                        </div>
                      

                       {{-- <canvas id="usageChart-{{ $device['id'] }}" style="width: 100px; height: 120px;"></canvas> --}}
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const devices = @json($devices);

        devices.forEach(device => {
            const ctx = document.getElementById(`usageChart-${device.id}`).getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['CPU Usage (%)', 'RAM Usage (%)', 'Free (%)'],
                    datasets: [{
                        data: [device.cpu, device.ram, 100 - Math.max(device.cpu, device.ram)],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',    // Blue for CPU
                            'rgba(255, 206, 86, 0.7)',    // Yellow for RAM
                            'rgba(201, 203, 207, 0.3)'    // Gray for free
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(201, 203, 207, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            enabled: true
                        }
                    }
                }
            });
        });
    });
</script>
@endsection