<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        @php
            $user = auth()->guard('admin')->user();

            $data = \App\Models\Website_information::where('pop_id', $user->pop_id)->latest()->first();

            if ($user->pop_id === null) {
                $data = \App\Models\Website_information::whereNull('pop_id')->latest()->first();
            }
        @endphp

        @if (!empty($data) && $data->logo)
            <img src="{{ asset('Backend/uploads/photos/' . $data->logo) }}" alt="Logo" class="brand-image elevation-3">
        @else
            <img src="{{ asset('Backend/img/default-logo.png') }}" alt="Default Logo" class="brand-image elevation-3">
        @endif




    </a>
    <!-- Sidebar -->
    <div class="sidebar">


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item ">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link  {{ $route == 'admin.dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('menu.dashboard') }}</p>
                    </a>
                </li>

                <li class="nav-item ">
                    <select class="form-control" name="sidebar_customer_id" style="width: 100%; font-size: 13px;">
                        <option >---Select---</option>
                        @php
                            if (!Cache::has('sidebar_customers')) {
                                if (!empty($branch_user_id)) {
                                    $customers = \App\Models\Customer::where('pop_id', $branch_user_id)->latest()->get();
                                } else {
                                    $customers = \App\Models\Customer::latest()->get();
                                }

                                Cache::put('sidebar_customers', $customers, now()->addHours(2)); // optional expiry
                            } else {
                                $customers = Cache::get('sidebar_customers');
                            }
                        @endphp

                        {{-- Check if customers are not empty --}}

                        @if ($customers->isNotEmpty())
                            @foreach ($customers as $item)
                                @php
                                    $status_icon = $item->status == 'online' ? '🟢' : '🔴';
                                @endphp

                               <option value="{{ $item->id }}">{!! $status_icon !!} [{{ $item->id }}] - {{ $item->username }} || {{ $item->fullname }}, ({{ $item->phone }})</option>
                            @endforeach
                        @else
                        @endif
                    </select>
                    <script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
                    <script src="{{ asset('Backend/plugins/select2/js/select2.full.min.js') }}"></script>
                    <script>
                        $(document).ready(function() {
                            $('select').select2();
                            $("select[name='sidebar_customer_id']").change(function() {
                                var customer_id = $(this).val();
                                if (customer_id) {
                                    window.location.href = "{{ route('admin.customer.view', ':id') }}".replace(':id',
                                        customer_id);
                                }
                            });
                        });
                    </script>
                </li>
                @php
                    $active_prefix = [
                        'admin.customer.index',
                        'admin.customer.create',
                        'admin.customer.restore.index',
                        'admin.customer.log.index',
                        'admin.customer.customer_import',
                        'admin.router.log.index',
                        'admin.customer.onu_list',
                        'admin.customer.view',
                        'admin.customer.edit',
                    ];
                @endphp
                <li class="nav-item has-treeview mt-2">
                    <a href="#" class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('menu.customer') }} <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('admin.customer.index') }}"
                            class="nav-link {{ $route == 'admin.customer.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.customer_list') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.customer.create') }}"
                            class="nav-link {{ $route == 'admin.customer.create' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.add_customer') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.customer.customer_import') }}"
                            class="nav-link {{ $route == 'admin.customer.customer_import' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.customer_import') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.customer.log.index') }}"
                            class="nav-link {{ $route == 'admin.customer.log.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.customer_logs') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.router.log.index') }}"
                            class="nav-link {{ $route == 'admin.router.log.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.mikrotik_logs') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.customer.restore.index') }}"
                            class="nav-link {{ $route == 'admin.customer.restore.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.backup_restore') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Customer Billings And Payment --}}
                @php
                    $active_prefix = ['admin.customer.payment.history', 'admin.customer.customer_credit_recharge_list','admin.customer.bulk.recharge','admin.customer.customer_comming_expire'];
                @endphp
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>{{ __('menu.billing_payments') }} <i class="right fas fa-angle-left"></i></p>
                    </a>

                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                        <!-- Bulk Recharge -->
                        <li class="nav-item">
                            <a href="{{ route('admin.customer.customer_comming_expire') }}"
                                class="nav-link {{ $route == 'admin.customer.customer_comming_expire' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Upcomming Expired </p>
                            </a>
                        </li>
                        <!-- Bulk Recharge -->
                        <li class="nav-item">
                            <a href="{{ route('admin.customer.bulk.recharge') }}"
                                class="nav-link {{ $route == 'admin.customer.bulk.recharge' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bulk Recharge</p>
                            </a>
                        </li>
                        <!-- Payment Management -->
                        <li class="nav-item">
                            <a href="{{ route('admin.customer.payment.history') }}"
                                class="nav-link {{ $route == 'admin.customer.payment.history' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.payment_history') }}</p>
                            </a>
                        </li>

                        <!-- Credit Recharge -->
                        <li class="nav-item">
                            <a href="{{ route('admin.customer.customer_credit_recharge_list') }}"
                                class="nav-link {{ $route == 'admin.customer.customer_credit_recharge_list' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.credit_recharge_list') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Customer Package --}}
                @php
                    $active_prefix = ['admin.customer.ip_pool.index', 'admin.customer.package.index'];
                @endphp
                @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)
                <li class="nav-item has-treeview">
                    <a href="#"
                        class="nav-link  {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-gift"></i>
                        <p>Cusomer Packages <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                        <li class="nav-item"><a href="{{ route('admin.customer.ip_pool.index') }}"
                                class="nav-link {{ $route == 'admin.customer.ip_pool.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>IP Pool</p>
                            </a></li>

                        <li class="nav-item">
                            <a href="{{ route('admin.customer.package.index') }}"
                                class="nav-link {{ $route == 'admin.customer.package.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Package </p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- Network Diagram --}}
                @php
                    $active_prefix = ['admin.network.diagram'];
                @endphp
                 @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)
                <li class="nav-item has-treeview">
                    <a href="#"
                        class="nav-link  {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-project-diagram"></i>
                        <p>Network Diagram <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                        <!-- Add Network Diagram menu item here -->
                        <li class="nav-item">
                            <a href="{{ route('admin.network.diagram') }}"
                                class="nav-link {{ $route == 'admin.network.diagram' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Network Diagram</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @php
                    $active_prefix = ['admin.pop'];
                @endphp
                  @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)
                    <li class="nav-item has-treeview">
                    <a href="#"
                        class="nav-link  {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-broadcast-tower"></i>
                        <p>POP/Branch <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                            <li class="nav-item"><a href="{{ route('admin.pop.index') }}"
                                class="nav-link  {{ $route == 'admin.pop.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>View POP/Branch </p>
                                </a>
                            </li>
                            <li class="nav-item"><a href="{{ route('admin.pop.area.index') }}"
                                class="nav-link {{ $route == 'admin.pop.area.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>POP Area</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @else
                    <!-- Branch Area WHEN Branch user lOGIN -->
                    <li class="nav-item ">
                        <a href="{{ route('admin.pop.area.index') }}"
                            class="nav-link  {{ $route == 'admin.pop.area.index' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-map-marker-alt"></i>
                            <p>{{ __('menu.branch_area') }}</p>
                        </a>
                    </li>
                @endif

                <!-- OLT Management -->
                @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)
                 @php
                    $active_prefix = ['admin.olt.index','admin.olt.create','admin.onu.index'];
                @endphp
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link  {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-server"></i>
                        <p>OLT Management <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview" style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <!-- OLT Device Configuration -->
                        <li class="nav-item">
                            <a href="{{ route('admin.olt.index') }}" class="nav-link {{ $route == 'admin.olt.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>OLT Device List</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.olt.create') }}" class="nav-link  {{ $route == 'admin.olt.create' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Configure OLT Device</p></a>
                        </li>

                        <!-- ONT (Optical Network Terminal) Management -->
                        <li class="nav-item">
                            <a href="{{ route('admin.onu.index') }}" class="nav-link {{ $route == 'admin.onu.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>ONT Device List</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Assign ONT to Customer</p></a>
                        </li>

                        <!-- GPON Port Management -->
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>GPON Port Configuration</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Monitor GPON Ports</p></a>
                        </li>

                        <!-- Network Monitoring -->
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Network Status</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Network Traffic Monitoring</p></a>
                        </li>

                        <!-- Diagnostics and Logs -->
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>System Diagnostics</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>View System Logs</p></a>
                        </li>

                        <!-- Alarm Management -->
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Alarm Configuration</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>View Alarms</p></a>
                        </li>

                        <!-- OLT Reports -->
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>OLT Performance Reports</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Customer Service Reports</p></a>
                        </li>

                        <!-- OLT Settings -->
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i><p>OLT Configuration Settings</p></a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Ticket Management -->
               <li class="nav-item">
                    <a href="#"
                        class="nav-link {{ Str::startsWith($currentRoute, 'admin.tickets') ? 'active' : '' }}">
                        <i class='nav-icon fas fa-ticket-alt'></i>
                        <p>&nbsp; {{ __('menu.tickets') }} <i class="right fas fa-angle-left"></i> </p>
                    </a>

                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, 'admin.tickets') ? 'display: block;' : 'display: none;' }}">

                        <li class="nav-item">
                            <a href="{{ route('admin.tickets.index') }}"
                                class="nav-link {{ $route == 'admin.tickets.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.ticket_list') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.tickets.complain_type.index') }}"
                                class="nav-link  {{ $route == 'admin.tickets.complain_type.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.complain_type') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.tickets.assign.index') }}"
                                class="nav-link  {{ $route == 'admin.tickets.assign.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.ticket_assign') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>


                @php
                    $active_prefix = ['admin.sms.config', 'admin.sms.template_list', 'admin.sms.message_send_list','admin.sms.bulk.message_send_list','admin.sms.logs','admin.sms.report'];
                @endphp
               <li class="nav-item has-treeview">
                    <a href="#" class="nav-link{{ in_array($route, $active_prefix) ? ' active' : '' }}">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>{{ __('menu.sms') }} <i class="right fas fa-angle-left"></i></p>
                    </a>

                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                        <li class="nav-item">
                            <a href="{{ route('admin.sms.message_send_list') }}"
                                class="nav-link {{ $route == 'admin.sms.message_send_list' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.send_sms') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.sms.bulk.message_send_list') }}"
                                class="nav-link {{ $route == 'admin.sms.bulk.message_send_list' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.send_bulk_sms') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.sms.template_list') }}"
                                class="nav-link {{ $route == 'admin.sms.template_list' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.sms_template') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.sms.logs') }}" class="nav-link {{ $route == 'admin.sms.logs' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.sms_logs') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.sms.report') }}" class="nav-link {{ $route == 'admin.sms.report' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.sms_report') }}</p>
                            </a>
                        </li>
                        @if (empty($branch_user_id))
                            <li class="nav-item">
                                <a href="{{ route('admin.sms.config') }}"
                                    class="nav-link {{ $route == 'admin.sms.config' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __('menu.sms_config') }}</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                <!-- HR Management -->
                 @php
                    $active_prefix = ['admin.hr.shift.index','admin.hr.department.index','admin.hr.designation.index','admin.hr.employee.create','admin.hr.employee.store','admin.hr.employee.index', 'admin.hr.employee.update', 'admin.hr.employee.leave.index','admin.hr.employee.salary.index','admin.hr.employee.salary.advance.index', 'admin.hr.employee.salary.advance.report','admin.hr.employee.payroll.create', 'admin.hr.employee.payroll.index','admin.hr.employee.loan.index','admin.hr.employee.loan.create','admin.hr.employee.loan.edit','admin.hr.employee.loan.show'];
                @endphp
                @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link{{ in_array($route, $active_prefix) ? ' active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>HR Management <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"  style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <!-- Employee Management -->
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.index') }}" class="nav-link {{ $route=='admin.hr.employee.index' || $route=='admin.hr.employee.update' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Employee List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.create') }}" class="nav-link {{ $route == 'admin.hr.employee.create' ||$route=='admin.hr.employee.store' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Add New Employee</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Employee Documents</p>
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.leave.index') }}" class="nav-link {{ $route == 'admin.hr.employee.leave.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Leave Management</p>
                            </a>
                        </li>
                        <!-- Attendance Management -->
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                <p>Attendance </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                <p>Attendance Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.salary.index') }}" class="nav-link {{ $route == 'admin.hr.employee.salary.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Salary</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.salary.advance.index') }}" class="nav-link {{ $route == 'admin.hr.employee.salary.advance.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Advance Salary</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.salary.advance.report') }}" class="nav-link {{ $route == 'admin.hr.employee.salary.advance.report' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Advance Salary Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.loan.index') }}" class="nav-link {{ $route == 'admin.hr.employee.loan.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Employee Loans</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.loan.create') }}" class="nav-link {{ $route == 'admin.hr.employee.loan.create' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Apply Loan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.employee.payroll.index') }}" class="nav-link {{ $route == 'admin.hr.employee.payroll.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Payroll Management</p>
                            </a>
                        </li>

                        <!-- Department & Designation -->
                        <li class="nav-item">
                            <a href="{{ route('admin.hr.designation.index') }}" class="nav-link {{ $route == 'admin.hr.designation.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Designations</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.hr.department.index') }}" class="nav-link {{ $route == 'admin.hr.department.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Departments</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.hr.shift.index') }}" class="nav-link {{ $route == 'admin.hr.shift.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Shift Management</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Employee Promotions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Employee Transfers</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Resignation / Termination</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Performance Evaluation</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Training Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Notice Board</p>
                            </a>
                        </li>


                    </ul>
                </li>
                @endif



                <!-----------Invenotry Menu------------------->
                @php
                    $active_prefix = ['admin.category.index','admin.brand.index','admin.store.index','admin.unit.index','admin.supplier.index','admin.supplier.invoice.create_invoice','admin.supplier.invoice.show_invoice','admin.client.index','admin.client.invoice.create_invoice','admin.client.invoice.show_invoice', 'admin.product.index'];
                @endphp
                @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)

                <li class="nav-item">
                    <a href="#" class="nav-link {{ in_array($route, $active_prefix) ? ' active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>&nbsp; Inventory <i class="right fas fa-angle-left"></i> </p>
                    </a>
                    <ul class="nav nav-treeview"  style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                        <li class="nav-item">
                            <a href="{{ route('admin.client.invoice.create_invoice') }}"
                                class="nav-link  {{ Route::currentRouteName() == 'admin.client.invoice.create_invoice' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Sale</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.invoice.show_invoice') }}" class="nav-link {{ Route::currentRouteName() == 'admin.client.invoice.show_invoice' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Sale Invoice</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.supplier.invoice.create_invoice') }}" class="nav-link {{ Route::currentRouteName() == 'admin.supplier.invoice.create_invoice' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Purchase</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.supplier.invoice.show_invoice') }}" class="nav-link {{ Route::currentRouteName() == 'admin.supplier.invoice.show_invoice' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Purchase Invoice</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.brand.index') }}" class="nav-link {{ Route::currentRouteName() == 'admin.brand.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Brand</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.category.index') }}"
                                class="nav-link {{ Route::currentRouteName() == 'admin.category.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Category</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.unit.index') }}" class="nav-link {{ Route::currentRouteName() == 'admin.unit.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Units</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.store.index') }}" class="nav-link {{ Route::currentRouteName() == 'admin.store.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Store</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.product.index') }}" class="nav-link  {{ Route::currentRouteName() == 'admin.product.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.supplier.index') }}" class="nav-link {{ Route::currentRouteName() == 'admin.supplier.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Supplier</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.client.index') }}" class="nav-link {{ Route::currentRouteName() == 'admin.client.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Client</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-----------------Accounts--------------------->
                @php
                    $active_prefix = ['admin.account.index','admin.account.transaction.index','admin.account.ledger.index','admin.account.trial_balance.index','admin.account.income_statment.index','admin.account.balance_sheet.index'];
                @endphp
                @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ in_array($route, $active_prefix) ? ' active' : '' }}">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>Accounts <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"  style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <!-- Account List -->
                        <li class="nav-item">
                            <a href="{{ route('admin.account.index') }}" class="nav-link {{ $route == 'admin.account.index' ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Account List</p>
                            </a>
                        </li>
                        <!-- Account Transaction -->
                        <li class="nav-item">
                            <a href="{{ route('admin.account.transaction.index') }}" class="nav-link {{ $route=='admin.account.transaction.index' ? 'active': '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Account Transaction</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.account.ledger.index') }}" class="nav-link {{ $route=='admin.account.ledger.index' ? 'active': '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Ledger Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.account.trial_balance.index') }}" class="nav-link {{ $route=='admin.account.trial_balance.index' ? 'active': '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Trial Balance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.account.income_statment.index') }}" class="nav-link {{ $route=='admin.account.income_statment.index' ? 'active': '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Profit & Loss</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.account.balance_sheet.index') }}" class="nav-link {{ $route=='admin.account.balance_sheet.index' ? 'active': '' }}"><i class="far fa-circle nav-icon"></i>
                                <p>Balance Sheet</p>
                            </a>
                        </li>


                    </ul>
                </li>
                @endif

                <!-----------------Task Management--------------------->
                @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)
                {{-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>Task Management <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Tasks</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Create Task</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Task Types</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Task Report</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}
                @endif
                <!-----------------Settings--------------------->
                @php
                    $active_prefix = ['admin.settings.information.index','admin.settings.passowrd.change.index'];
                @endphp
                <li class="nav-item">
                    <a href="#"
                        class="nav-link  {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="fa fa-cog mr-2 "></i>
                        <p>&nbsp; Settings <i class="right fas fa-angle-left"></i> </p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                        <li class="nav-item">
                            <a href="{{ route('admin.settings.information.index') }}"
                                class="nav-link {{ $route == 'admin.settings.information.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Application Information</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.passowrd.change.index') }}"
                                class="nav-link {{ $route == 'admin.settings.passowrd.change.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Chagne Password</p>
                            </a>
                        </li>

                    </ul>
                </li>
                @php
                    $active_prefix = ['admin.router'];
                @endphp
                <!-----------------Router Management--------------------->
                @if (empty($branch_user_id)||$branch_user_id == null || $branch_user_id == 0)
                <li class="nav-item has-treeview">
                    <a href="#"
                        class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Mikrotik Server <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{ route('admin.router.index') }}"
                                class="nav-link {{ $route == 'admin.router.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mikrotik Router Add</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.router.nas.show_nas_server') }}"
                                class="nav-link {{ $route == 'admin.router.nas.show_nas_server' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>NAS Server</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Settings</p>
                            </a>
                        </li> --}}
                    </ul>
                </li>
                @endif



            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
