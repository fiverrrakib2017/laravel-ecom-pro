<div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h3 class="card-title mb-0 mr-2">Network Health &amp; Usage</h3>
                                <span class="badge badge-success mr-2" data-toggle="tooltip"
                                    title="ONU is reachable">Online</span>
                                <span class="badge badge-secondary" data-toggle="tooltip"
                                    title="Router uptime since last reboot">
                                    <i class="far fa-clock mr-1"></i> 12d 04h
                                </span>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button id="btnQuickTest" class="btn btn-outline-primary">
                                    <i class="fas fa-stethoscope mr-1"></i>Quick Test
                                </button>
                                <button id="btnRefreshNet" class="btn btn-outline-secondary" data-toggle="tooltip"
                                    title="Refresh">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" id="copyDiag"><i
                                            class="far fa-copy mr-1"></i>Copy Diagnostic</a>
                                    <a class="dropdown-item" href="#"><i class="far fa-file-alt mr-1"></i>View
                                        Logs</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-muted" href="#"><i
                                            class="fas fa-power-off mr-1"></i>Restart Router (UI)</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-3">
                            <div class="row">
                                <!-- LEFT: live status & mini KPIs -->
                                <div class="col-xl-5 col-lg-6 mb-3">
                                    <ul class="list-unstyled mb-3">
                                        <li class="mb-2">
                                            <i class="fas fa-circle text-success mr-2"></i> ONU:
                                            <strong>Active</strong>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-long-arrow-alt-down mr-2"></i> Download:
                                            <strong>26.8 Mbps</strong>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-long-arrow-alt-up mr-2"></i> Upload:
                                            <strong>4.2 Mbps</strong>
                                        </li>
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="fas fa-network-wired mr-2"></i> IP:&nbsp;
                                            <strong id="wanIp" class="mr-2">10.10.12.34</strong>
                                            <button class="btn btn-xs btn-outline-secondary" id="copyIp"
                                                data-toggle="tooltip" title="Copy IP">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-server mr-2"></i> Router:
                                            <strong>Mikrotik RB750Gr3</strong>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-map-marker-alt mr-2"></i> Area/POP:
                                            <strong>Banani / POP-01</strong>
                                        </li>
                                    </ul>

                                    <!-- link quality -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">Optical Power</span>
                                            <span><strong>-19.4 dBm</strong></span>
                                        </div>
                                        <div class="progress" style="height:8px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: 72%;" aria-valuenow="72" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">SNR</span>
                                            <span><strong>28 dB</strong></span>
                                        </div>
                                        <div class="progress" style="height:8px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 56%;"
                                                aria-valuenow="56" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>

                                    <!-- latency & quality chips -->
                                    <div class="d-flex flex-wrap">
                                        <span class="badge badge-light border mr-2 mb-2" data-toggle="tooltip"
                                            title="Gateway latency">
                                            <i class="fas fa-bolt mr-1"></i> 4 ms
                                        </span>
                                        <span class="badge badge-light border mr-2 mb-2" data-toggle="tooltip"
                                            title="Packet loss (1 min)">
                                            <i class="fas fa-water mr-1"></i> 0.0% loss
                                        </span>
                                        <span class="badge badge-light border mb-2" data-toggle="tooltip"
                                            title="Jitter (1 min)">
                                            <i class="fas fa-random mr-1"></i> 1.2 ms jitter
                                        </span>
                                    </div>

                                    <!-- hidden diagnostic text for copy -->
                                    <pre id="diagText" class="d-none">
                                        Account: John Doe (john_doe)
                                        IP: 10.10.12.34
                                        Router: Mikrotik RB750Gr3
                                        ONU: Active, Optical -19.4 dBm, SNR 28 dB
                                        Latency: 4 ms, Loss 0.0%, Jitter 1.2 ms
                                        Down/Up: 26.8/4.2 Mbps
                                    </pre>
                                </div>

                                <!-- RIGHT: chart -->
                                <div class="col-xl-7 col-lg-6">
                                    <canvas id="usageChart" height="140"></canvas>
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

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
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

                        // usage chart with gradient
                        (function() {
                            const ctx = document.getElementById('usageChart').getContext('2d');
                            const gradDown = ctx.createLinearGradient(0, 0, 0, 220);
                            gradDown.addColorStop(0, 'rgba(54,162,235,0.35)');
                            gradDown.addColorStop(1, 'rgba(54,162,235,0.05)');
                            const gradUp = ctx.createLinearGradient(0, 0, 0, 220);
                            gradUp.addColorStop(0, 'rgba(75,192,192,0.35)');
                            gradUp.addColorStop(1, 'rgba(75,192,192,0.05)');

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                    datasets: [{
                                            label: 'Download (GB)',
                                            data: [12, 9, 14, 18, 11, 16, 13],
                                            backgroundColor: gradDown,
                                            borderColor: 'rgba(54,162,235,1)',
                                            borderWidth: 2,
                                            fill: true,
                                            pointRadius: 2,
                                            tension: .35
                                        },
                                        {
                                            label: 'Upload (GB)',
                                            data: [2, 1.5, 2.2, 3, 2.4, 2.8, 2.1],
                                            backgroundColor: gradUp,
                                            borderColor: 'rgba(75,192,192,1)',
                                            borderWidth: 2,
                                            fill: true,
                                            pointRadius: 2,
                                            tension: .35
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'bottom'
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            grid: {
                                                color: 'rgba(0,0,0,.05)'
                                            }
                                        },
                                        x: {
                                            grid: {
                                                display: false
                                            }
                                        }
                                    }
                                }
                            });
                        })();
                    </script>
