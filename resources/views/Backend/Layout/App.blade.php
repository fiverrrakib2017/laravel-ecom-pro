@php

    $prefix = Request::route()->getPrefix();
    $route = Route::current()->getname();
    $currentRoute = Route::currentRouteName();
    $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
@endphp




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600&display=swap" rel="stylesheet">

    <title> @yield('title')</title>

    @include('Backend.Include.Style')
    <style>
        .menu-open {
            display: block;
        }

        .sidebar,
        .navbar,
        .content-wrapper {
            font-family: 'Hind Siliguri', sans-serif !important;
        }

        .layout-navbar-fixed .wrapper .sidebar-dark-primary .brand-link:not([class*=navbar]) {
            background-color: #fff !important;
        }



        /*----------------Loader--------------*/
        #loaderOverlay {
            display: flex;
            /* Initially visible */
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        #loaderBox {
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.15);
            display: flex;
            gap: 12px;
            align-items: center;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Backend.Include.Navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Backend.Include.Sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">

                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active"><a
                                        href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                @yield('header_title')
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <!-- Loader Overlay -->
            <div id="loaderOverlay">
                <div id="loaderBox">
                    <img src="{{asset('Backend/images/loading.gif')}}" class="img-fluid" alt="Loading...">
                </div>
            </div>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <!-- @include('Backend.Include.Footer') -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <script type="text/javascript">
        function showLoader() {
            $('#loaderOverlay').fadeIn(150);
        }

        function hideLoader() {
            $('#loaderOverlay').fadeOut(150);
        }

        $(window).on('load', function() {
            hideLoader();
        });
    </script>
    @include('Backend.Include.Script')

</body>

</html>
