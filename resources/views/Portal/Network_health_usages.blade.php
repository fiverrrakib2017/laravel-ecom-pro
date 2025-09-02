@php
    use Illuminate\Support\Facades\DB;

    $start = \Carbon\Carbon::now()->startOfMonth();
    $end = \Carbon\Carbon::now()->endOfMonth();

    $usage = DB::table('daily_usages')
        ->selectRaw('SUM(upload) as total_upload, SUM(download) as total_download')
        ->where('customer_id', auth('customer')->user()->id)
        ->whereBetween('created_at', [$start, $end])
        ->first();

    $upload_gb = round(($usage->total_upload ?? 0) / 1024, 2);
    $download_gb = round(($usage->total_download ?? 0) / 1024, 2);
@endphp

<div class="card shadow-sm">
    <div class="card-header card-header-compact d-flex align-items-center justify-content-between">
        <!-- Left: title + status -->
        <div class="d-flex align-items-center flex-wrap">
            <div class="d-flex align-items-center mr-3">
                <i class="fas fa-wifi text-primary mr-2"></i>
                <h3 class="card-title mb-0 mr-2">Network Health &amp; Usage</h3>
            </div>

            <span class="v-divider d-none d-md-inline-block"></span>

            <span class="badge badge-status mr-2" data-toggle="tooltip" title="ONU is reachable">
                <span class="status-dot"></span> Online
            </span>

            <span class="badge badge-uptime" data-toggle="tooltip" title="Router uptime since last reboot">
                <i class="far fa-clock mr-1"></i> <span id="customer_uptime"></span>
            </span>
        </div>

        <!-- Right: actions -->
        <div class="btn-toolbar">
            <div class="btn-group btn-group-sm mr-1">
                <button id="btnQuickTest" class="btn btn-soft-primary">
                    <i class="fas fa-stethoscope mr-1"></i> Quick Test
                </button>
            </div>

            <div class="btn-group btn-group-sm mr-1">
                <button id="btnRefreshNet" class="btn btn-icon btn-soft-secondary" data-toggle="tooltip"
                    title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>

            <div class="btn-group btn-group-sm">
                <button class="btn btn-icon btn-soft-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false" aria-label="More actions">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" id="copyDiag"><i class="far fa-copy mr-1"></i> Copy
                        Diagnostic</a>
                    <a class="dropdown-item" href="#"><i class="far fa-file-alt mr-1"></i> View Logs</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-muted" href="#"><i class="fas fa-power-off mr-1"></i> Restart
                        Router (UI)</a>
                </div>
            </div>
        </div>
    </div>
    <style>
        .card-header-compact {
            padding: .75rem 1rem;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(180deg, #fff, #fbfcfe);
        }

        .v-divider {
            width: 1px;
            height: 20px;
            background: #e5e7eb;
            margin: 0 .75rem;
        }

        .badge-status {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            color: #2e7d32;
            font-weight: 600;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            display: inline-block;
            margin-right: .35rem;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, .15)
        }

        .badge-uptime {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
        }

        .btn-soft-primary {
            color: #0d6efd;
            background: rgba(13, 110, 253, .08);
            border: 1px solid rgba(13, 110, 253, .15);
        }

        .btn-soft-primary:hover {
            background: rgba(13, 110, 253, .14);
        }

        .btn-soft-secondary {
            color: #334155;
            background: rgba(51, 65, 85, .08);
            border: 1px solid rgba(51, 65, 85, .15);
        }

        .btn-soft-secondary:hover {
            background: rgba(51, 65, 85, .14);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
    </style>

    <div class="card-body pt-3">
        <div class="row">
            <!-- LEFT: live status & mini KPIs -->
            <div class="col-xl-5 col-lg-6 mb-3">
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-circle text-success mr-2"></i> ONU</span>
                        <span class="badge badge-success">Active</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-long-arrow-alt-down mr-2"></i> Download</span>
                        <span class="badge badge-primary">{{$download_gb ?? ''}} GB</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-long-arrow-alt-up mr-2"></i> Upload</span>
                        <span class="badge badge-info">{{$upload_gb ?? ''}} GB</span>
                    </li>

                    <li class="list-group-item">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="mr-2"><i class="fas fa-network-wired mr-2"></i> IP</div>
                            <div class="input-group input-group-sm w-50">
                                <input type="text" class="form-control" value="{{ auth('customer')->user()->router->ip_address ?? 'N/A' }}" id="wanIp" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" id="copyIp" data-toggle="tooltip"
                                        title="Copy IP">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-server mr-2"></i> Router</span>
                        <span class="text-monospace">Mikrotik -> {{ auth('customer')->user()->router->name ?? 'N/A' }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-map-marker-alt mr-2"></i> Area/POP</span>
                        <span>{{ auth('customer')->user()->area->name ?? 'N/A' }} / {{ auth('customer')->user()->pop->name ?? 'N/A' }}</span>
                    </li>
                </ul>
            </div>

            <!-- RIGHT: chart -->
            <div class="col-xl-7 col-lg-6">
               <canvas id="liveBandwidthChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    /* subtle polish */
    .card .card-header {
        border-bottom: 0;
    }

    .progress {
        background-color: #eef2f7;
    }

    .badge {
        font-weight: 600;
    }
</style>
<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // tooltips
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // refresh btn fake spinner
        document.getElementById('btnRefreshNet').addEventListener('click', function() {
            const btn = this,
                old = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Refreshing';
            btn.disabled = true;
            setTimeout(() => {
                btn.innerHTML = old;
                btn.disabled = false;
            }, 1200);
        });

        // copy IP
        document.getElementById('copyIp').addEventListener('click', async function() {
            const ip = document.getElementById('wanIp').innerText.trim();
            try {
                await navigator.clipboard.writeText(ip);
            } catch (e) {}
            $(this).tooltip('hide').attr('data-original-title', 'Copied!').tooltip('show');
            setTimeout(() => $(this).attr('data-original-title', 'Copy IP'), 1200);
        });

        // copy diagnostic
        document.getElementById('copyDiag').addEventListener('click', function(e) {
            e.preventDefault();
            const txt = document.getElementById('diagText').innerText.trim();
            navigator.clipboard.writeText(txt).catch(() => {});
        });

        // quick test (demo)
        document.getElementById('btnQuickTest').addEventListener('click', function() {
            const old = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Running';
            this.disabled = true;
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check mr-1"></i>All Good';
            }, 1200);
            setTimeout(() => {
                this.innerHTML = old;
                this.disabled = false;
            }, 2200);
        });

        /************** Customer Bandwidth Graph Start **************************/
        const ctx = document.getElementById('liveBandwidthChart').getContext('2d');

        const labels = Array.from({
            length: 30
        }, () => '');
        const downloadData = Array(30).fill(0);
        const uploadData = Array(30).fill(0);

        const bandwidthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Download (kbps)',
                        data: downloadData,
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'Upload (kbps)',
                        data: uploadData,
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        tension: 0.4,
                    }
                ]
            },
            options: {
                responsive: true,
                animation: false,
                scales: {
                    x: {
                        display: false,
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: 5,
                    }
                }
            }
        });

        function fetch_live_bandwidth_data() {
            $.ajax({
                url: "{{ route('admin.customer.live_bandwith_update', ':id') }}".replace(':id',
                    "{{ auth('customer')->user()->id }}"),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const downloadSpeed = response.rx_mb;
                        const uploadSpeed = response.tx_mb;
                        // const rx_speed_kbps = response.rx_speed_kbps;
                        // const tx_speed_kbps = response.tx_speed_kbps;

                        const user_uptime = response.uptime;
                        const user_interface_name = response.interface_name;
                        const user_ip_address = response.ip_address;
                        const user_mac_address = response.mac_address;

                        /* Update graph data with new point (slide effect)*/
                        downloadData.push(downloadSpeed);
                        downloadData.shift();

                        uploadData.push(uploadSpeed);
                        uploadData.shift();

                        bandwidthChart.update();

                        /* Update Client Data*/
                        $("#customer_upload_speed").html(uploadSpeed);
                        $("#customer_download_speed").html(downloadSpeed);
                        $("#customer_uptime").html(user_uptime);
                        $("#customer_mac_address").html(user_mac_address);
                        $("#customer_ip_address").html(user_ip_address);
                        $("#customer_interface").html($('<div>').text(user_interface_name).html());
                    }
                }
            });


        }

        fetch_live_bandwidth_data();
        setInterval(fetch_live_bandwidth_data, 1000);
    });
</script>
