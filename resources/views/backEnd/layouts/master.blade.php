<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />

    <title>@yield('title') - {{$generalsetting->name}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" />

    <link href="{{ asset('backEnd/admira/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backEnd/admira/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backEnd/admira/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Bootstrap Touchspin -->
    <link href="{{ asset('backEnd/admira/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Select2 -->
    <link href="{{ asset('backEnd/admira/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Bootstrap Datepicker -->
    <link href="{{ asset('backEnd/admira/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Spectrum Colorpicker -->
    <link href="{{ asset('backEnd/admira/assets/libs/spectrum-colorpicker2/spectrum.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Bootstrap Css -->
    <link href="{{ asset('backEnd/admira/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css">

    <!-- Icons Css -->
    <link href="{{ asset('backEnd/admira/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

    <!-- App Css -->
    <link href="{{ asset('backEnd/admira/assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css">

    <!-- Toastr Css -->
    <link rel="stylesheet" href="{{ asset('backEnd/admira/assets/css/toastr/toastr.min.css') }}">

    <!-- Delete Modal Css -->
    {{-- <link rel="stylesheet" href="{{ asset('backEnd/admira/js/deleteModal.css') }}"> --}}

    <!-- Chartist Chart -->
    <link href="{{ asset('backEnd/admira/assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css">

    <!-- C3 Chart Css -->
    <link href="{{ asset('backEnd/admira/assets/libs/c3/c3.min.css') }}" rel="stylesheet" type="text/css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>



    <!-- Head js -->
    @yield('css')
    {{-- <script src="{{asset('backEnd/')}}/assets/js/head.js"></script> --}}



  </head>

  <!-- body start -->
  <body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

       
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="{{route('dashboard')}}" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="http://103.146.16.154/assets/images/it-fast.png" class="img-fluid" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="http://103.146.16.154/assets/images/it-fast.png" class="img-fluid" alt="" height="17">
                            </span>
                        </a>

                        <a href="{{route('dashboard')}}" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="http://103.146.16.154/assets/images/it-fast.png" class="img-fluid" alt="" height="22">
                            </span>
                            <span class="logo-lg">

                            <img src="http://103.146.16.154/assets/images/it-fast.png" class="img-fluid" alt="Logo" height="36">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>

                    <div class="d-none d-sm-block ms-2">
                        <h4 class="page-title"> 
                            {{-- Welcome To Dashboard --}}
                        </h4>
                    </div>
                </div>


                <div class="d-flex">
                    <div class="dropdown d-none d-lg-inline-block me-2">
                            <button type="button" class="btn header-item toggle-search noti-icon waves-effect" data-target="#search-wrap">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    <div class="dropdown d-none d-md-block me-2">
                        <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="font-size-16">
                                {{Auth::user()->name}}
                                
                            </span>
                        </button>
                    </div>


                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="http://103.146.16.154/profileImages/avatar.png" alt="Header Avatar">
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="mdi mdi-power"></i> Logout</a>
                        </div>
                    </div>

                 

                </div>
            </div>
        </header>


        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <div id="sidebar-menu">
                    <ul class="metismenu list-unstyled" id="side-menu">

                        {{-- Dashboard --}}
                        <li>
                            <a href="{{ url('admin/dashboard') }}" class="waves-effect">
                                <i class="mdi mdi-view-dashboard-outline"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>

                        {{-- Orders --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-cart-outline"></i>
                                <span> Orders </span>
                            </a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="{{ route('admin.orders',['slug'=>'all']) }}">
                                        <i class="mdi mdi-format-list-bulleted"></i> All Orders
                                    </a>
                                </li>

                                @foreach($orderstatus as $value)
                                    <li>
                                        <a href="{{ route('admin.orders',['slug'=>$value->slug]) }}">
                                            <i class="mdi mdi-package-variant-closed"></i>
                                            {{ $value->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                        {{-- Products --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-database-outline"></i>
                                <span> Products </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('products.index') }}"><i class="mdi mdi-cube-outline"></i> Product Manage</a></li>
                                <li><a href="{{ route('categories.index') }}"><i class="mdi mdi-shape-outline"></i> Categories</a></li>
                                <li><a href="{{ route('subcategories.index') }}"><i class="mdi mdi-shape-plus-outline"></i> Subcategories</a></li>
                                <li><a href="{{ route('childcategories.index') }}"><i class="mdi mdi-shape"></i> Childcategories</a></li>
                                <li><a href="{{ route('brands.index') }}"><i class="mdi mdi-tag-outline"></i> Brands</a></li>
                                <li><a href="{{ route('colors.index') }}"><i class="mdi mdi-palette-outline"></i> Colors</a></li>
                                <li><a href="{{ route('sizes.index') }}"><i class="mdi mdi-ruler"></i> Sizes</a></li>
                                <li><a href="{{ route('products.price_edit') }}"><i class="mdi mdi-currency-usd"></i> Price Edit</a></li>
                            </ul>
                        </li>

                        {{-- Landing Page --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-home"></i>
                                <span> Landing Page </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('campaign.create') }}"><i class="mdi mdi-plus-circle-outline"></i> Create</a></li>
                                <li><a href="{{ route('campaign.index') }}"><i class="mdi mdi-view-list-outline"></i> Campaign</a></li>
                            </ul>
                        </li>

                        {{-- Users --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-account-group-outline"></i>
                                <span> Users </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('users.index') }}"><i class="mdi mdi-account-outline"></i> User</a></li>
                                <li><a href="{{ route('roles.index') }}"><i class="mdi mdi-shield-account-outline"></i> Roles</a></li>
                                <li><a href="{{ route('permissions.index') }}"><i class="mdi mdi-key-outline"></i> Permissions</a></li>
                                <li><a href="{{ route('customers.index') }}"><i class="mdi mdi-account-multiple-outline"></i> Customers</a></li>
                            </ul>
                        </li>

                        {{-- Site Settings --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-cog-outline"></i>
                                <span> Site Setting </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('settings.index') }}"><i class="mdi mdi-tune"></i> General Setting</a></li>
                                <li><a href="{{ route('socialmedias.index') }}"><i class="mdi mdi-facebook"></i> Social Media</a></li>
                                <li><a href="{{ route('contact.index') }}"><i class="mdi mdi-phone-outline"></i> Contact</a></li>
                                <li><a href="{{ route('pages.index') }}"><i class="mdi mdi-file-document-outline"></i> Create Page</a></li>
                                <li><a href="{{ route('shippingcharges.index') }}"><i class="mdi mdi-truck-outline"></i> Shipping Charge</a></li>
                                <li><a href="{{ route('orderstatus.index') }}"><i class="mdi mdi-progress-check"></i> Order Status</a></li>
                            </ul>
                        </li>

                        {{-- API --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-api"></i>
                                <span> API Integration </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('paymentgeteway.manage') }}"><i class="mdi mdi-credit-card-outline"></i> Payment Gateway</a></li>
                                <li><a href="{{ route('smsgeteway.manage') }}"><i class="mdi mdi-message-text-outline"></i> SMS Gateway</a></li>
                                <li><a href="{{ route('courierapi.manage') }}"><i class="mdi mdi-truck-fast-outline"></i> Courier API</a></li>
                            </ul>
                        </li>

                        {{-- Pixel --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-google-analytics"></i>
                                <span> Pixel & GTM </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('tagmanagers.index') }}"><i class="mdi mdi-google"></i> Tag Manager</a></li>
                                <li><a href="{{ route('pixels.index') }}"><i class="mdi mdi-chart-line"></i> Pixel Manage</a></li>
                            </ul>
                        </li>

                        {{-- Banner --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-image-multiple-outline"></i>
                                <span> Banner & Ads </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('banner_category.index') }}"><i class="mdi mdi-folder-image"></i> Banner Category</a></li>
                                <li><a href="{{ route('banners.index') }}"><i class="mdi mdi-image"></i> Banner & Ads</a></li>
                            </ul>
                        </li>

                        {{-- Reports --}}
                        <li>
                            <a href="javascript:void(0);" class="has-arrow waves-effect">
                                <i class="mdi mdi-chart-areaspline"></i>
                                <span> Reports </span>
                            </a>
                            <ul class="sub-menu">
                                <li><a href="{{ route('admin.stock_report') }}"><i class="mdi mdi-warehouse"></i> Stock Report</a></li>
                                <li><a href="{{ route('customers.ip_block') }}"><i class="mdi mdi-shield-lock-outline"></i> IP Block</a></li>
                                <li><a href="{{ route('admin.order_report') }}"><i class="mdi mdi-chart-bar"></i> Order Reports</a></li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © iT-FAST.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Development <i class="mdi mdi-heart text-danger"></i><a target="__blank" href="#">iT-Fast</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->











    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    {!! Toastr::message() !!}
    <script src="{{asset('backEnd/')}}/assets/js/sweetalert.min.js"></script>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('backEnd/admira/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Buttons examples -->
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('backEnd/admira/assets/js/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/js/toastr/toastr.init.js') }}"></script>

    <!-- Datatable init js -->
    <script src="{{ asset('backEnd/admira/assets/js/pages/datatables.init.js') }}"></script>

    <!-- App Js -->
    <script src="{{ asset('backEnd/admira/assets/js/app.js') }}"></script>

    <!-- Peity chart -->
    <script src="{{ asset('backEnd/admira/assets/libs/peity/jquery.peity.min.js') }}"></script>

    <!-- C3 Chart -->
    <script src="{{ asset('backEnd/admira/assets/libs/d3/d3.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/c3/c3.min.js') }}"></script>

    <!-- jQuery Knob -->
    <script src="{{ asset('backEnd/admira/assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ asset('backEnd/admira/assets/js/pages/dashboard.init.js') }}"></script>

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

    <!-- Fluid Meter -->
    <script src="{{ asset('backEnd/admira/assets/js/js-fluid-meter.js') }}"></script>

    <!-- Plugin Js for Charts -->
    <script src="{{ asset('backEnd/admira/assets/libs/chartist/chartist.min.js') }}"></script>
    <script src="{{ asset('backEnd/admira/assets/libs/chartist-plugin-tooltips/chartist-plugin-tooltip.min.js') }}"></script>

    <!-- form wizard -->
    <script src="{{ asset('backEnd/admira/assets/libs/jquery-steps/build/jquery.steps.min.js') }}"></script>

    <!-- form wizard init -->
    <script src="{{ asset('backEnd/admira/assets/js/pages/form-wizard.init.js') }}"></script>

    <!-- Counter-Up  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>

    <script type="text/javascript">
      $(".delete-confirm").click(function (event) {
        var form = $(this).closest("form");
        event.preventDefault();
        swal({
          title: `Are you sure you want to delete this record?`,
          text: "If you delete this, it will be gone forever.",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            form.submit();
          }
        });
      });
      $(".change-confirm").click(function (event) {
        var form = $(this).closest("form");
        event.preventDefault();
        swal({
          title: `Are you sure you want to change this record?`,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            form.submit();
          }
        });
      });
    </script>
    <!--patho courier-->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.pathaocity').change(function() {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('admin/pathao-city') }}?city_id=" + id,
                        success: function(res) {
                            if (res && res.data && res.data.data) {
                                $(".pathaozone").empty();
                                $(".pathaozone").append('<option value="">Select..</option>');
                                $.each(res.data.data, function(index, zone) {
                                    $(".pathaozone").append('<option value="' + zone.zone_id + '">' + zone.zone_name + '</option>');
                                    $('.pathaozone').trigger("chosen:updated");
                                });
                            } else {
                                 $(".pathaoarea").empty();
                                $(".pathaozone").empty();
                            }
                        }
                    });
                } else {
                     $(".pathaoarea").empty();
                    $(".pathaozone").empty();
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.pathaozone').change(function() {
                var id = $(this).val();
                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('admin/pathao-zone') }}?zone_id=" + id,
                        success: function(res) {
                            if (res && res.data && res.data.data) {
                                $(".pathaoarea").empty();
                                $(".pathaoarea").append('<option value="">Select..</option>');
                                $.each(res.data.data, function(index, area) {
                                    $(".pathaoarea").append('<option value="' + area.area_id + '">' + area.area_name + '</option>');
                                    $('.pathaoarea').trigger("chosen:updated");
                                });
                            } else {
                                $(".pathaoarea").empty();
                            }
                        }
                    });
                } else {
                    $(".pathaoarea").empty();
                }
            });
        });
    </script>
    @yield('script')
  </body>
</html>
