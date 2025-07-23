<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Responsive Customer Search -->
     <ul class="navbar-nav ml-2 flex-grow-1">
        <li class="nav-item w-100">
            <form class="form-inline w-100">
               <select class="form-control" name="sidebar_customer_id" id="sidebar_customer_id" style="width: 100%; font-size: 13px; visibility: hidden;">
                        @include('Backend.Component.Common.Customer')
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
            </form>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
       <!-- Notifications Dropdown Menu -->
       <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-danger navbar-badge">1</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">1 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
        </div>
      </li>


        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
                <img src="{{asset('Backend/images/avatar.png')}}" alt="User Image" class="user-img border" style="width: 40px; height: 40px; object-fit: cover; border-radius:50%; margin-right: 10px;">
                <span>
                    <b>{{ Auth::guard('admin')->user()->name }}</b>
                </span>
                <i class="fa fa-angle-down ml-2"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow" aria-labelledby="account_settings">
                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.settings.information.index')}}">
                    <i class="fa fa-cog mr-2 text-muted"></i> Manage Account
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center" href="{{route('admin.settings.passowrd.change.index')}}">
                    <i class="fa fa-key mr-2 text-muted"></i> Change Password
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('admin.logout') }}">
                    <i class="fa fa-power-off mr-2"></i> Logout
                </a>
            </div>
        </li>
        <!-- Language Select Dropdown -->
        <!-- Language Selector -->
        {{-- <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                🌐 {{ strtoupper(app()->getLocale()) }}
                <i class="fa fa-angle-down ml-1"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ url('lang/en') }}" class="dropdown-item">English</a>
                <a href="{{ url('lang/bn') }}" class="dropdown-item">বাংলা</a>
                <a href="{{ url('lang/es') }}" class="dropdown-item">Español</a>
            </div>
        </li> --}}

    </ul>
  </nav>

