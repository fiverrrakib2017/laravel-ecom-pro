<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Customer Profile</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600&display=swap" rel="stylesheet">

    @include('Backend.Include.Style')
</head>

<body>
    <style>
        .small-box .icon {
            top: 10px;
        }

        .brand-gradient {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: #fff;
        }

        .masked {
            letter-spacing: 2px;
        }

        .theme-dark .card,
        .theme-dark .modal-content {
            background: #1f2937;
            color: #cbd5e1;
        }

        .theme-dark .table {
            color: #cbd5e1;
        }

        .clickable {
            cursor: pointer;
        }

        .mini-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
        }

        .countdown {
            font-variant-numeric: tabular-nums;
        }
    </style>

    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0">Customer Portal</h1>
                <small class="text-muted">Account ID: CUST-{{ auth('customer')->user()->id ?? '' }} • Service: Home Broadband</small>
            </div>
            <div class="d-flex">
                <button id="themeToggle" class="btn btn-outline-secondary btn-sm mr-2">
                    <i class="fas fa-adjust"></i> Theme
                </button>
                <button onclick="window.print()" class="btn btn-outline-info btn-sm mr-2">
                    <i class="fas fa-print"></i> Print
                </button>
                <form action="{{ route('customer.logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Welcome Banner -->
            <div class="card brand-gradient mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">Welcome, {{ auth('customer')->user()->fullname ?? '' }}!</h3>
                        <div>Username: <strong>{{ auth('customer')->user()->username ?? '' }}</strong> • Plan: <strong>{{ \App\Models\Branch_package::find(auth('customer')->user()->package_id)->name }}</strong></div>
                    </div>
                    <span class="badge badge-pill badge-light px-3 py-2">
                        Customer Since: {{ auth('customer')->user()->created_at?->format('d M Y') }}
                    </span>

                </div>
            </div>

            <!-- Top KPI Boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 class="mb-1">{{ auth('customer')->user()->status }}</h3>
                            <p class="mb-0">Account Status</p>
                        </div>
                        <div class="icon"><i class="fas fa-signal"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 class="mb-1">0.00 ৳</h3>
                            <p class="mb-0">Total Due</p>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 class="mb-1">
                                {{ \Carbon\Carbon::parse(auth('customer')->user()->expire_date)->format('d M Y') }}
                            </h3>
                            <p class="mb-0">Next Billing</p>


                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 class="mb-1">Package</h3>
                            <p class="mb-0">{{ \App\Models\Branch_package::find(auth('customer')->user()->package_id)->name }} • Unlimited</p>
                        </div>
                        <div class="icon"><i class="fas fa-wifi"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- LEFT -->
                <div class="col-lg-8">

                    <!-- Network Health + Usage -->
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


                    <!-- Billing Summary & Invoices -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Billing Summary</h3>
                            <div class="card-tools">
                                <a class="btn btn-xs btn-outline-secondary" href="#"><i
                                        class="far fa-file-alt mr-1"></i>Download Last Invoice (PDF)</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-responsive table-hover text-nowrap table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-right">Amount (৳)</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>INV-2025-0901</td>
                                        <td>01 Sep 2025</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                        <td class="text-right">1,200.00</td>
                                        <td class="text-center"><a href="#" class="text-primary mr-2"><i
                                                    class="far fa-eye"></i></a><a href="#"
                                                class="text-secondary"><i class="fas fa-download"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>INV-2025-0815</td>
                                        <td>15 Aug 2025</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                        <td class="text-right">1,200.00</td>
                                        <td class="text-center"><a href="#" class="text-primary mr-2"><i
                                                    class="far fa-eye"></i></a><a href="#"
                                                class="text-secondary"><i class="fas fa-download"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>INV-2025-0801</td>
                                        <td>01 Aug 2025</td>
                                        <td><span class="badge badge-danger">Due</span></td>
                                        <td class="text-right">1,200.00</td>
                                        <td class="text-center"><a href="#" class="text-primary mr-2"><i
                                                    class="far fa-eye"></i></a><a href="#"
                                                class="text-secondary"><i class="fas fa-download"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex justify-content-end"><a href="#"
                                class="btn btn-success"><i class="fas fa-money-check-alt mr-1"></i> Pay Now
                                (bKash/Nagad)</a></div>
                    </div>
                    <!-- FAQ -->
                    @include('Portal.FAQ')

                </div>
                <!-- /LEFT -->

                <!-- RIGHT -->
                <div class="col-lg-4">

                    <!-- Account Summary -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Account Summary</h3>
                            <a href="#" data-toggle="modal" data-target="#changePasswordModal"
                                class="small">Change Password</a>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-5">Name</dt>
                                <dd class="col-7">John Doe</dd>
                                <dt class="col-5">Username</dt>
                                <dd class="col-7">john_doe</dd>
                                <dt class="col-5">Phone</dt>
                                <dd class="col-7">017xx-xxxxxx</dd>
                                <dt class="col-5">Address</dt>
                                <dd class="col-7">House 12, Road 7, City</dd>
                                <dt class="col-5">Area</dt>
                                <dd class="col-7">Banani</dd>
                                <dt class="col-5">POP</dt>
                                <dd class="col-7">POP-01</dd>
                                <dt class="col-5">Router</dt>
                                <dd class="col-7">Mikrotik RB750Gr3</dd>
                                <dt class="col-5">Last Seen</dt>
                                <dd class="col-7">02 Sep 2025, 09:30 AM</dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Service Details -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Service Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-2 mini-label">PPPoE</div>
                            <div class="mb-2">Username: <strong>john.pppoe</strong></div>
                            <div class="mb-3">Password:
                                <span id="pppoePass" class="masked">••••••••</span>
                                <a href="#" id="togglePass" class="small ml-1">Show</a>
                            </div>
                            <div class="mb-2">Public IP: <strong>103.100.20.30</strong></div>
                            <div class="mb-2">MAC: <strong>DC:2C:6E:AA:BB:CC</strong></div>
                            <div class="mb-2">CGNAT: <strong>Enabled</strong></div>
                            <hr>
                            <div class="text-muted small">Need static IP or upgrade plan? <a href="#">Contact
                                    sales</a>.</div>
                        </div>
                    </div>

                    <!-- Support & Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Support & Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <a href="#" class="btn btn-success btn-block mb-2"><i
                                    class="fas fa-money-check-alt mr-1"></i> Pay Now</a>
                            <a href="https://speed.cloudflare.com" target="_blank"
                                class="btn btn-info btn-block mb-2"><i class="fas fa-tachometer-alt mr-1"></i> Speed
                                Test</a>
                            <button class="btn btn-warning btn-block mb-3" data-toggle="modal"
                                data-target="#supportModal"><i class="fas fa-headset mr-1"></i> Create Support
                                Ticket</button>
                            <div>
                                <p class="mb-1"><i class="fas fa-phone-alt mr-2"></i> 096xx-xxxxxx (24/7)</p>
                                <p class="mb-1"><i class="fab fa-whatsapp mr-2"></i> <a href="#">WhatsApp
                                        Chat</a></p>
                                <p class="mb-1"><i class="fab fa-facebook-messenger mr-2"></i> <a
                                        href="#">Messenger</a></p>
                                <p class="mb-0"><i class="far fa-envelope mr-2"></i> support@yourisp.com</p>
                            </div>
                        </div>
                    </div>



                </div>
                <!-- /RIGHT -->
            </div>
        </div>
    </section>

    <!-- Support Ticket Modal -->
    <div class="modal fade" id="supportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="#">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Support Ticket</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"><label>Subject</label><input type="text" class="form-control"
                                placeholder="e.g., No Internet"></div>
                        <div class="form-group"><label>Details</label>
                            <textarea rows="4" class="form-control" placeholder="Describe your issue..."></textarea>
                        </div>
                        <div class="form-group"><label>Preferred Contact</label><select class="form-control">
                                <option>Phone</option>
                                <option>WhatsApp</option>
                                <option>Messenger</option>
                                <option>Email</option>
                            </select></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary" type="submit">Submit Ticket</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal (UI only) -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="#">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Portal Password</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"><label>Current Password</label><input type="password"
                                class="form-control" placeholder="••••••••"></div>
                        <div class="form-group"><label>New Password</label><input type="password"
                                class="form-control" placeholder="min 6 chars"></div>
                        <div class="form-group"><label>Confirm New Password</label><input type="password"
                                class="form-control" placeholder="retype new password"></div>
                        <p class="text-muted small mb-0">Note: Portal password change does not affect PPPoE password.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    @include('Backend.Include.Script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Theme toggle (light/dark) with localStorage
        (function() {
            const root = document.documentElement;
            const key = 'portal-theme';
            const apply = (mode) => {
                if (mode === 'dark') document.body.classList.add('theme-dark');
                else document.body.classList.remove('theme-dark');
            };
            apply(localStorage.getItem(key) || 'light');
            document.getElementById('themeToggle').addEventListener('click', () => {
                const cur = localStorage.getItem(key) || 'light';
                const next = cur === 'light' ? 'dark' : 'light';
                localStorage.setItem(key, next);
                apply(next);
            });
        })();


    </script>
</body>

</html>
