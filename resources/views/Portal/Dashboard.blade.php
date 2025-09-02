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
    @include('Portal.Header')
    <section class="content">
        <div class="container-fluid">

            <!-- Welcome Banner -->
              @include('Portal.Welcome')

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
                      @include('Portal.Account_summary')

                    <!-- Service Details -->
                     @include('Portal.Service_summary')

                    <!-- Support & Quick Actions -->
                     @include('Portal.Support_and_tickets')



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
