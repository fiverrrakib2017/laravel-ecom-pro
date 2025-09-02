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
                    @include('Portal.Network_health_usages')
                    <!-- Billing Summary & Invoices -->
                     @include('Portal.Billing_summary')
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
