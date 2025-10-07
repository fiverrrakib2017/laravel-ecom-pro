 <style>
    .section-divider{
        display:flex; align-items:center; gap:12px; margin:16px 0;
    }
    .section-divider::before,
    .section-divider::after{
        content:""; height:1px; flex:1;
        background:linear-gradient(to right, transparent, rgba(255, 255, 255, 0.25));
    }
    .section-divider::after{
        background:linear-gradient(to left, transparent, rgba(255, 255, 255, 0.25));
    }
    .section-divider span{
        font-weight:700; text-transform:uppercase; letter-spacing:.08em; font-size:.85rem; color:#ffffff;
    }
    body.dark-mode .section-divider::before,
    body.dark-mode .section-divider::after{ background:linear-gradient(to right, transparent, rgba(253, 253, 253, 0.25)); }
    body.dark-mode .section-divider span{ color:#ffffff; }
</style>


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
    <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('Backend/images/avatar.png')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ route('admin.dashboard') }}" class="d-block">{{ $data->name ?? 'Guest' }}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
        @include('Backend.Include._Search')


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

                {{-- ================= HOTSPOT MENU  ================= --}}
                @php
                    $active_prefix = [
                        'admin.hotspot.profile.index',
                        'admin.hotspot.profile.create',
                    ];
                @endphp
                <div class="section-divider">
                    <span>Hotspot</span>
                </div>



                <li class="nav-item">
                    <a href="{{route('admin.hotspot.user.dashbaord')}}" class="nav-link  {{ Str::startsWith($currentRoute, 'admin.hotspot.user.dashbaord') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Hotspot Dashboard</p>
                    </a>
                </li>



                {{-- Profiles --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Profiles<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview" style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{route('admin.hotspot.profile.index')}}"
                                class="nav-link {{ $route == 'admin.hotspot.profile.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Profiles</p>
                            </a>
                        </li>
                        {{-- was: route('admin.hotspot.profiles.create') --}}
                        <li class="nav-item">
                            <a href="{{route('admin.hotspot.profile.create')}}" class="nav-link {{ $route == 'admin.hotspot.profile.create' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Create Profile</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @php
                    $active_prefix = [

                        'admin.hotspot.user.index',
                        'admin.hotspot.user.create',
                        'admin.hotspot.user.bulk.create',
                        'admin.hotspot.user.bulk.import',
                        'admin.hotspot.user.edit',
                    ];
                @endphp
                {{-- Users --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview " style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Active Sessions</p>
                                <span class="right badge badge-danger">57</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.hotspot.user.index')}}" class="nav-link {{ $route == 'admin.hotspot.user.index' || $route=='admin.hotspot.user.edit' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Users</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('admin.hotspot.user.create')}}" class="nav-link {{ $route == 'admin.hotspot.user.create' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Single User Create</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.hotspot.user.bulk.create')}}" class="nav-link {{ $route == 'admin.hotspot.user.bulk.create' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bulk User Create</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.hotspot.user.bulk.import')}}" class="nav-link {{ $route == 'admin.hotspot.user.bulk.import' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bulk Import (CSV)</p>
                            </a>
                        </li>
                    </ul>
                </li>
                 @php
                    $active_prefix = [

                        'admin.hotspot.vouchers.batch.index',
                        'admin.hotspot.vouchers.batch.create',
                        'admin.hotspot.vouchers.print',
                        'admin.hotspot.vouchers.sales',
                        'admin.hotspot.vouchers.export',
                    ];
                @endphp
                {{-- Vouchers --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>Vouchers<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview" style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <li class="nav-item">
                            <a href="{{route('admin.hotspot.vouchers.batch.create')}}" class="nav-link {{ $route == 'admin.hotspot.vouchers.batch.create' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Generate Batch</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.hotspot.vouchers.batch.index') }}"
                            class="nav-link {{ $route == 'admin.hotspot.vouchers.batch.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Batches</p>
                                <span class="right badge badge-success">
                                   @php
                                        $count = \Schema::hasTable('voucher_batches')
                                            ? \App\Models\Voucher_batch::count()
                                            : 0;
                                        echo $count;
                                    @endphp
                                </span>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{route('admin.hotspot.vouchers.sales')}}" class="nav-link {{ $route == 'admin.hotspot.vouchers.sales' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sold / Activated</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Reports --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Reports<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- was: route('admin.hotspot.reports.logins') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daily Logins</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.reports.usage') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usage by Profile</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.reports.top') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Top Users</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.reports.sales') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Voucher Sales</p>
                            </a></li>
                    </ul>
                </li>

                {{-- Tools --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>Tools<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- was: route('admin.hotspot.tools.sync') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sync Sessions</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.tools.push') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Push Batch to Router</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.tools.kick') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kick User</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.tools.cleanup') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cleanup Expired</p>
                            </a></li>
                    </ul>
                </li>

                {{-- Settings --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Settings<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- was: route('admin.hotspot.settings.portal') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Portal Branding</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.settings.packages') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Packages & Pricing</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.settings.radius') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>RADIUS</p>
                            </a></li>
                    </ul>
                </li>

                {{-- Logs & Audit --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Logs & Audit<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- was: route('admin.hotspot.logs.api') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>API Logs</p>
                            </a></li>
                        {{-- was: route('admin.hotspot.logs.events') --}}
                        <li class="nav-item"><a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>System Events</p>
                            </a></li>
                    </ul>
                </li>
                {{-- ================= HOTSPOT MENU END ================= --}}
                 <div class="section-divider">
                    <span>Customers</span>
                </div>
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
                        'admin.customer.customer_operation',
                        'admin.customer.import.mikrotik',
                    ];
                @endphp

                @if (auth()->guard('admin')->user()->can('menu.access.customers'))
                    <li class="nav-item has-treeview mt-2">
                        <a href="#"
                            class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>{{ __('menu.customer') }} <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview"
                            style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">


                            @if (auth()->guard('admin')->user()->can('customer.view'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.index') }}"
                                        class="nav-link {{ $route == 'admin.customer.index' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('menu.customer_list') }}</p>
                                    </a>
                                </li>
                            @endif

                            @if (auth()->guard('admin')->user()->can('customer.create'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.create') }}"
                                        class="nav-link {{ $route == 'admin.customer.create' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('menu.add_customer') }}</p>
                                    </a>
                                </li>
                            @endif
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.customer_operation') }}"
                                        class="nav-link {{ $route == 'admin.customer.customer_operation' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Customer Operation</p>
                                    </a>
                                </li>

                            @if (auth()->guard('admin')->user()->can('customer.import'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.customer_import') }}"
                                        class="nav-link {{ $route == 'admin.customer.customer_import' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('menu.customer_import') }}</p>
                                    </a>
                                </li>
                            @endif


                            @if (auth()->guard('admin')->user()->can('customer.import.mikrotik'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.import.mikrotik') }}"
                                        class="nav-link {{ $route == 'admin.customer.import.mikrotik' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Import From Mikrotik</p>
                                    </a>
                                </li>
                            @endif


                            @if (auth()->guard('admin')->user()->can('customer.logs.view'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.log.index') }}"
                                        class="nav-link {{ $route == 'admin.customer.log.index' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('menu.customer_logs') }}</p>
                                    </a>
                                </li>
                            @endif


                            @if (auth()->guard('admin')->user()->can('router.logs.view'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.router.log.index') }}"
                                        class="nav-link {{ $route == 'admin.router.log.index' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('menu.mikrotik_logs') }}</p>
                                    </a>
                                </li>
                            @endif

                            @if (auth()->guard('admin')->user()->can('customer.restore'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.restore.index') }}"
                                        class="nav-link {{ $route == 'admin.customer.restore.index' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('menu.backup_restore') }}</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- Customer Billings And Payment --}}
                @php
                    $active_prefix = [
                        'admin.customer.payment.history',
                        'admin.customer.bill.generate',
                        'admin.customer.customer_credit_recharge_list',
                        'admin.customer.bulk.recharge',
                        'admin.customer.customer_comming_expire',
                        'admin.customer.grace_recharge.logs',
                    ];
                @endphp
                @if (auth()->guard('admin')->user()->can('menu.access.billing_payments'))
                    <li class="nav-item has-treeview">
                        <a href="#"
                            class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>
                                {{ __('menu.billing_payments') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview"
                            style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                            @if (auth()->guard('admin')->user()->can('payment.upcoming.expiry.view'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.customer_comming_expire') }}"
                                        class="nav-link {{ $route == 'admin.customer.customer_comming_expire' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Upcoming Expired <span class="right badge badge-danger">New</span></p>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ route('admin.customer.bill.generate') }}"
                                    class="nav-link {{ $route == 'admin.customer.bill.generate' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bill Generate </p>
                                </a>
                            </li>

                            @if (auth()->guard('admin')->user()->can('payment.bulk.recharge'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.bulk.recharge') }}"
                                        class="nav-link {{ $route == 'admin.customer.bulk.recharge' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Bulk/Grace Recharge</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.grace_recharge.logs') }}"
                                        class="nav-link {{ $route == 'admin.customer.grace_recharge.logs' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Grace Recharge Logs</p>
                                    </a>
                                </li>
                            @endif

                            @if (auth()->guard('admin')->user()->can('payment.history.view'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.payment.history') }}"
                                        class="nav-link {{ $route == 'admin.customer.payment.history' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('menu.payment_history') }}</p>
                                    </a>
                                </li>
                            @endif

                            @if (auth()->guard('admin')->user()->can('payment.credit.recharge.list'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.customer.customer_credit_recharge_list') }}"
                                        class="nav-link {{ $route == 'admin.customer.customer_credit_recharge_list' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>{{ __('menu.credit_recharge_list') }}</p>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>

                @endif

                {{-- Customer Package --}}
                @php
                    $active_prefix = ['admin.customer.ip_pool.index', 'admin.customer.package.index'];
                @endphp

                @if (empty($branch_user_id) || $branch_user_id == null || $branch_user_id == 0)
                    @if (auth()->guard('admin')->user()->can('menu.access.customer_package'))
                        <li class="nav-item has-treeview">
                            <a href="#"
                                class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                                <i class="nav-icon fas fa-gift"></i>
                                <p>Customer Packages <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                                @if (auth()->guard('admin')->user()->can('ip_pool.view'))
                                    <li class="nav-item">
                                        <a href="{{ route('admin.customer.ip_pool.index') }}"
                                            class="nav-link {{ $route == 'admin.customer.ip_pool.index' ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>IP Pool</p>
                                        </a>
                                    </li>
                                @endif

                                @if (auth()->guard('admin')->user()->can('package.view'))
                                    <li class="nav-item">
                                        <a href="{{ route('admin.customer.package.index') }}"
                                            class="nav-link {{ $route == 'admin.customer.package.index' ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Package</p>
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    @endif
                @endif




                {{-- Network Diagram --}}
                @php
                    $active_prefix = ['admin.network.diagram'];
                @endphp

                @if (empty($branch_user_id) || $branch_user_id == null || $branch_user_id == 0)
                    @if (auth()->guard('admin')->user()->can('menu.access.network_diagram'))
                        <li class="nav-item has-treeview">
                            <a href="#"
                                class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                                <i class="nav-icon fas fa-project-diagram"></i>
                                <p>Network Diagram <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                                @if (auth()->guard('admin')->user()->can('network.diagram.view'))
                                    <li class="nav-item">
                                        <a href="{{ route('admin.network.diagram') }}"
                                            class="nav-link {{ $route == 'admin.network.diagram' ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Network Diagram</p>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif



                @php
                    $active_prefix = ['admin.pop'];
                @endphp

                @if (empty($branch_user_id) || $branch_user_id == null || $branch_user_id == 0)
                    @if (auth()->guard('admin')->user()->can('menu.access.pop_branch'))
                        <li class="nav-item has-treeview">
                            <a href="#"
                                class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                                <i class="nav-icon fas fa-broadcast-tower"></i>
                                <p>POP/Branch <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                                @if (auth()->guard('admin')->user()->can('pop_branch.view'))
                                    <li class="nav-item">
                                        <a href="{{ route('admin.pop.index') }}"
                                            class="nav-link {{ $route == 'admin.pop.index' ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>View POP/Branch</p>
                                        </a>
                                    </li>
                                @endcan

                                @if (auth()->guard('admin')->user()->can('pop_branch.area.view'))
                                    <li class="nav-item">
                                        <a href="{{ route('admin.pop.area.index') }}"
                                            class="nav-link {{ $route == 'admin.pop.area.index' ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>POP Area</p>
                                        </a>
                                    </li>
                                @endcan
                    </ul>
                </li>
            @endif
        @else
            {{-- Branch Area WHEN Branch user LOGIN --}}
            {{-- @if (auth()->guard('admin')->user()->can('pop_branch.area.view')) --}}
            <li class="nav-item">
                <a href="{{ route('admin.pop.area.index') }}"
                    class="nav-link {{ $route == 'admin.pop.area.index' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-map-marker-alt"></i>
                    <p>{{ __('menu.branch_area') }}</p>
                </a>
            </li>
            {{-- @endif --}}
        @endif

        <!-- OLT Management -->
        @if (empty($branch_user_id) || $branch_user_id == null || $branch_user_id == 0)
            @php
                $active_prefix = ['admin.olt.index', 'admin.olt.create', 'admin.onu.index'];
            @endphp

            <li class="nav-item has-treeview">
                <a href="#"
                    class="nav-link  {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-server"></i>
                    <p>OLT Management <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview"
                    style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                    <!-- OLT Device Configuration -->
                    @if (auth()->guard('admin')->user()->can('olt.device.list'))
                        <li class="nav-item">
                            <a href="{{ route('admin.olt.index') }}"
                                class="nav-link {{ $route == 'admin.olt.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>OLT Device List</p>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('admin.olt.create') }}"
                            class="nav-link  {{ $route == 'admin.olt.create' ? 'active' : '' }}"><i
                                class="far fa-circle nav-icon"></i>
                            <p>Configure OLT Device</p>
                        </a>
                    </li>

                    <!-- ONT (Optical Network Terminal) Management -->
                    <li class="nav-item">
                        <a href="{{ route('admin.onu.index') }}"
                            class="nav-link {{ $route == 'admin.onu.index' ? 'active' : '' }}"><i
                                class="far fa-circle nav-icon"></i>
                            <p>ONT Device List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>Assign ONT to Customer</p>
                        </a>
                    </li>

                    <!-- GPON Port Management -->
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>GPON Port Configuration</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>Monitor GPON Ports</p>
                        </a>
                    </li>

                    <!-- Network Monitoring -->
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>Network Status</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>Network Traffic Monitoring</p>
                        </a>
                    </li>

                    <!-- Diagnostics and Logs -->
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>System Diagnostics</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>View System Logs</p>
                        </a>
                    </li>

                    <!-- Alarm Management -->
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>Alarm Configuration</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>View Alarms</p>
                        </a>
                    </li>

                    <!-- OLT Reports -->
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>OLT Performance Reports</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>Customer Service Reports</p>
                        </a>
                    </li>

                    <!-- OLT Settings -->
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                            <p>OLT Configuration Settings</p>
                        </a>
                    </li>
                </ul>
            </li>

        @endif

        <!-- Ticket Management -->
        @if (auth()->guard('admin')->user()->can('menu.access.tickets'))
            <li class="nav-item">

                <a href="#"
                    class="nav-link {{ Str::startsWith($currentRoute, 'admin.tickets') ? 'active' : '' }}">
                    <i class='nav-icon fas fa-ticket-alt'></i>
                    <p>&nbsp; {{ __('menu.tickets') }} <i class="right fas fa-angle-left"></i> </p>
                </a>

                <ul class="nav nav-treeview"
                    style="{{ Str::startsWith($currentRoute, 'admin.tickets') ? 'display: block;' : 'display: none;' }}">
                    @if (auth()->guard('admin')->user()->can('ticket.list.view'))
                        <li class="nav-item">
                            <a href="{{ route('admin.tickets.index') }}"
                                class="nav-link {{ $route == 'admin.tickets.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.ticket_list') }}</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can('ticket.complain_type.manage'))
                        <li class="nav-item">
                            <a href="{{ route('admin.tickets.complain_type.index') }}"
                                class="nav-link  {{ $route == 'admin.tickets.complain_type.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.complain_type') }}</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can('ticket.assign.manage'))
                        <li class="nav-item">
                            <a href="{{ route('admin.tickets.assign.index') }}"
                                class="nav-link  {{ $route == 'admin.tickets.assign.index' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.ticket_assign') }}</p>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif


        @php
            $active_prefix = [
                'admin.sms.config',
                'admin.sms.template_list',
                'admin.sms.message_send_list',
                'admin.sms.bulk.message_send_list',
                'admin.sms.logs',
                'admin.sms.report',
            ];
        @endphp
        @if (auth()->guard('admin')->user()->can('menu.access.sms'))
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link{{ in_array($route, $active_prefix) ? ' active' : '' }}">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p>{{ __('menu.sms') }} <i class="right fas fa-angle-left"></i></p>
                </a>

                <ul class="nav nav-treeview"
                    style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                    @if (auth()->guard('admin')->user()->can('sms.send'))
                        <li class="nav-item">
                            <a href="{{ route('admin.sms.message_send_list') }}"
                                class="nav-link {{ $route == 'admin.sms.message_send_list' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.send_sms') }}</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can('sms.bulk.send'))
                        <li class="nav-item">
                            <a href="{{ route('admin.sms.bulk.message_send_list') }}"
                                class="nav-link {{ $route == 'admin.sms.bulk.message_send_list' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.send_bulk_sms') }}</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can('sms.template.manage'))
                        <li class="nav-item">
                            <a href="{{ route('admin.sms.template_list') }}"
                                class="nav-link {{ $route == 'admin.sms.template_list' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.sms_template') }}</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can('sms.logs.view'))
                        <li class="nav-item">
                            <a href="{{ route('admin.sms.logs') }}"
                                class="nav-link {{ $route == 'admin.sms.logs' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.sms_logs') }}</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can('sms.report.view'))
                        <li class="nav-item">
                            <a href="{{ route('admin.sms.report') }}"
                                class="nav-link {{ $route == 'admin.sms.report' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('menu.sms_report') }}</p>
                            </a>
                        </li>
                    @endif
                    @if (empty($branch_user_id))
                        @if (auth()->guard('admin')->user()->can('sms.config.manage'))
                            <li class="nav-item">
                                <a href="{{ route('admin.sms.config') }}"
                                    class="nav-link {{ $route == 'admin.sms.config' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ __('menu.sms_config') }}</p>
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </li>
        @endif

        <!-- HR Management -->
        @php
            $active_prefix = [
                'admin.hr.shift.index',
                'admin.hr.department.index',
                'admin.hr.designation.index',
                'admin.hr.employee.create',
                'admin.hr.employee.store',
                'admin.hr.employee.index',
                'admin.hr.employee.update',
                'admin.hr.employee.leave.index',
                'admin.hr.employee.salary.index',
                'admin.hr.employee.salary.advance.index',
                'admin.hr.employee.salary.advance.report',
                'admin.hr.employee.payroll.create',
                'admin.hr.employee.payroll.index',
                'admin.hr.employee.loan.index',
                'admin.hr.employee.loan.create',
                'admin.hr.employee.loan.edit',
                'admin.hr.employee.loan.show',
            ];
        @endphp
        @if (empty($branch_user_id) || $branch_user_id == null || $branch_user_id == 0)
            @if (auth()->guard('admin')->user()->can('menu.access.hr_management'))
                <li class="nav-item has-treeview">
                    <a href="#"
                        class="nav-link{{ in_array($route, $active_prefix) ? ' active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>HR Management <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        <!-- Employee Management -->
                        @if (auth()->guard('admin')->user()->can('hr.employee.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.index' || $route == 'admin.hr.employee.update' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Employee List</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.employee.create'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.create') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.create' || $route == 'admin.hr.employee.store' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Add New Employee</p>
                                </a>
                            </li>
                        @endif
                        {{-- <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Employee Documents</p>
                                    </a>
                                </li> --}}
                        @if (auth()->guard('admin')->user()->can('hr.employee.leave.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.leave.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.leave.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Leave Management</p>
                                </a>
                            </li>
                        @endif
                        <!-- Attendance Management -->
                        @if (auth()->guard('admin')->user()->can('hr.attendance.view'))
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                    <p>Attendance </p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.attendance.report.view'))
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="far fa-circle nav-icon"></i>
                                    <p>Attendance Report</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.salary.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.salary.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.salary.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Salary</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.salary.advance.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.salary.advance.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.salary.advance.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Advance Salary</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.salary.advance.report.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.salary.advance.report') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.salary.advance.report' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Advance Salary Report</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.loan.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.loan.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.loan.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Employee Loans</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.loan.create'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.loan.create') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.loan.create' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Apply Loan</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.payroll.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.employee.payroll.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.employee.payroll.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Payroll Management</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.designation.view'))
                            <!-- Department & Designation -->
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.designation.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.designation.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Designations</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.department.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.department.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.department.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Departments</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.shift.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.hr.shift.index') }}"
                                    class="nav-link {{ $route == 'admin.hr.shift.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Shift Management</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.promotion.manage'))
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Employee Promotions</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.transfer.manage'))
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Employee Transfers</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.resignation.manage'))
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Resignation</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.performance.manage'))
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Performance Evaluation</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.training.manage'))
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Training Records</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('hr.notice_board.manage'))
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Notice Board</p>
                                </a>
                            </li>
                        @endif


                    </ul>
                </li>
            @endif
        @endif



        <!-----------Invenotry Menu------------------->
        @php
            $active_prefix = [
                'admin.category.index',
                'admin.brand.index',
                'admin.store.index',
                'admin.unit.index',
                'admin.supplier.index',
                'admin.supplier.invoice.create_invoice',
                'admin.supplier.invoice.show_invoice',
                'admin.client.index',
                'admin.client.invoice.create_invoice',
                'admin.client.invoice.show_invoice',
                'admin.product.index',
            ];
        @endphp
        @if (empty($branch_user_id) || $branch_user_id == null || $branch_user_id == 0)
            @if (auth()->guard('admin')->user()->can('menu.access.inventory'))
                <li class="nav-item">
                    <a href="#"
                        class="nav-link {{ in_array($route, $active_prefix) ? ' active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>&nbsp; Inventory <i class="right fas fa-angle-left"></i> </p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        @if (auth()->guard('admin')->user()->can('inventory.sale.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.client.invoice.create_invoice') }}"
                                    class="nav-link  {{ Route::currentRouteName() == 'admin.client.invoice.create_invoice' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Sale</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.sale_invoice.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.client.invoice.show_invoice') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.client.invoice.show_invoice' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Sale Invoice</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.purchase.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.supplier.invoice.create_invoice') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.supplier.invoice.create_invoice' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Purchase</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.purchase_invoice.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.supplier.invoice.show_invoice') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.supplier.invoice.show_invoice' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Purchase Invoice</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.brand.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.brand.index') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.brand.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Brand</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.category.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.category.index') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.category.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Category</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.unit.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.unit.index') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.unit.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Units</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.store.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.store.index') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.store.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Store</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.product.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.product.index') }}"
                                    class="nav-link  {{ Route::currentRouteName() == 'admin.product.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Products</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.supplier.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.supplier.index') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.supplier.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Supplier</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('inventory.client.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.client.index') }}"
                                    class="nav-link {{ Route::currentRouteName() == 'admin.client.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Client</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        @endif

        <!-----------------Accounts--------------------->
        @php
            $active_prefix = [
                'admin.account.index',
                'admin.account.transaction.index',
                'admin.account.ledger.index',
                'admin.account.trial_balance.index',
                'admin.account.income_statment.index',
                'admin.account.balance_sheet.index',
            ];
        @endphp
        @if (empty($branch_user_id) || $branch_user_id == null || $branch_user_id == 0)
            @if (auth()->guard('admin')->user()->can('menu.access.accounts'))
                <li class="nav-item has-treeview">
                    <a href="#"
                        class="nav-link {{ in_array($route, $active_prefix) ? ' active' : '' }}">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>Accounts <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">

                        @if (auth()->guard('admin')->user()->can('accounts.list.view'))
                            <!-- Account List -->
                            <li class="nav-item">
                                <a href="{{ route('admin.account.index') }}"
                                    class="nav-link {{ $route == 'admin.account.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Account List</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('accounts.transaction.view'))
                            <!-- Account Transaction -->
                            <li class="nav-item">
                                <a href="{{ route('admin.account.transaction.index') }}"
                                    class="nav-link {{ $route == 'admin.account.transaction.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Account Transaction</p>
                                </a>
                            </li>
                        @endif

                        @if (auth()->guard('admin')->user()->can('accounts.ledger.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.account.ledger.index') }}"
                                    class="nav-link {{ $route == 'admin.account.ledger.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Ledger Report</p>
                                </a>
                            </li>
                        @endif

                        @if (auth()->guard('admin')->user()->can('accounts.trial_balance.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.account.trial_balance.index') }}"
                                    class="nav-link {{ $route == 'admin.account.trial_balance.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Trial Balance</p>
                                </a>
                            </li>
                        @endif

                        @if (auth()->guard('admin')->user()->can('accounts.profit_loss.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.account.income_statment.index') }}"
                                    class="nav-link {{ $route == 'admin.account.income_statment.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Profit & Loss</p>
                                </a>
                            </li>
                        @endif

                        @if (auth()->guard('admin')->user()->can('accounts.balance_sheet.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.account.balance_sheet.index') }}"
                                    class="nav-link {{ $route == 'admin.account.balance_sheet.index' ? 'active' : '' }}"><i
                                        class="far fa-circle nav-icon"></i>
                                    <p>Balance Sheet</p>
                                </a>
                            </li>
                        @endif


                    </ul>
                </li>
            @endif
        @endif

        <!-----------------Settings--------------------->
        @php
            $active_prefix = [
                'admin.settings.information.index',
                'admin.settings.passowrd.change.index',
                'admin.settings.payment.method.index',
            ];
        @endphp
        @if (auth()->guard('admin')->user()->can('menu.access.settings'))
            <li class="nav-item">
                <a href="#"
                    class="nav-link  {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                    <i class="fa fa-cog mr-2 "></i>
                    <p>&nbsp; Settings <i class="right fas fa-angle-left"></i> </p>
                </a>
                <ul class="nav nav-treeview"
                    style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                    @if (auth()->guard('admin')->user()->can('settings.information.view'))
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.information.index') }}"
                                class="nav-link {{ $route == 'admin.settings.information.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Application Information</p>
                            </a>
                        </li>
                    @endif
                    @if (auth()->guard('admin')->user()->can('settings.password.change'))
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.passowrd.change.index') }}"
                                class="nav-link {{ $route == 'admin.settings.passowrd.change.index' ? 'active' : '' }}"><i
                                    class="far fa-circle nav-icon"></i>
                                <p>Chagne Password</p>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.payment.method.index') }}"
                            class="nav-link {{ $route == 'admin.settings.payment.method.index' ? 'active' : '' }}"><i
                                class="far fa-circle nav-icon"></i>
                            <p>Payment Method</p>
                        </a>
                    </li>

                </ul>
            </li>
        @endif
        @php
            $active_prefix = ['admin.router'];
        @endphp
        <!-----------------Router Management--------------------->
        @if (empty($branch_user_id) || $branch_user_id == null || $branch_user_id == 0)

            @if (auth()->guard('admin')->user()->can('menu.access.server'))
                <li class="nav-item has-treeview">
                    <a href="#"
                        class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-server"></i>
                        <p>Server <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        @if (auth()->guard('admin')->user()->can('server.router.add'))
                            <li class="nav-item">
                                <a href="{{ route('admin.router.index') }}"
                                    class="nav-link {{ $route == 'admin.router.index' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Mikrotik Router Add</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('server.router.sync'))
                            <li class="nav-item">
                                <a href="{{ route('admin.router.sync') }}"
                                    class="nav-link {{ $route == 'admin.router.sync' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Mikrotik Sync</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('server.nas.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.router.nas.show_nas_server') }}"
                                    class="nav-link {{ $route == 'admin.router.nas.show_nas_server' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>NAS Server</p>
                                </a>
                            </li>
                        @endif
                        {{-- <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Settings</p>
                                    </a>
                                </li> --}}
                    </ul>
                </li>
            @endif
            @php
                $active_prefix = [
                    'admin.user.index',
                    'admin.user.store',
                    'admin.user.permission',
                    'admin.user.role.index',
                ];
            @endphp

            @if (auth()->guard('admin')->user()->can('menu.access.user_management'))
                <li class="nav-item has-treeview ">
                    <a href="#"
                        class="nav-link {{ Str::startsWith($currentRoute, $active_prefix) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>User Management <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview"
                        style="{{ Str::startsWith($currentRoute, $active_prefix) ? 'display: block;' : 'display: none;' }}">
                        @if (auth()->guard('admin')->user()->can('user.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.user.index') }}"
                                    class="nav-link {{ $route == 'admin.user.index' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('role.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.user.role.index') }}"
                                    class="nav-link {{ $route == 'admin.user.role.index' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Roles</p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->guard('admin')->user()->can('permission.view'))
                            <li class="nav-item">
                                <a href="{{ route('admin.user.permission') }}"
                                    class="nav-link {{ $route == 'admin.user.permission' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Permissions</p>
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif
        @endif
    </ul>
</nav>
<!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
</aside>
