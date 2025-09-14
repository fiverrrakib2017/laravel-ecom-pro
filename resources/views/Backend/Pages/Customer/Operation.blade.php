@extends('Backend.Layout.App')
@section('title', 'Customer Operation | Admin Panel')
@section('style')
    <style>
        .card-header-pro {
            position: relative;
            overflow: hidden;
            padding: 16px 20px;
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #17a2b8 0%, #0ea5e9 45%, #2563eb 100%);
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .2);
            border-top-left-radius: .25rem;
            /* Bootstrap card radius keep */
            border-top-right-radius: .25rem;
        }

        /* soft glow decor */
        .card-header-pro::after {
            content: "";
            position: absolute;
            right: -30px;
            top: -30px;
            width: 180px;
            height: 180px;
            pointer-events: none;
            background: radial-gradient(circle at 30% 30%,
                    rgba(255, 255, 255, .35), rgba(255, 255, 255, 0) 60%);
            transform: rotate(25deg);
            opacity: .7;
        }

        .card-header-pro .card-title {
            font-weight: 700;
            letter-spacing: .2px;
        }

        .card-header-pro .icon-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .15);
            color: #fff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08),
                inset 0 0 0 1px rgba(255, 255, 255, .25);
        }

        .card-header-pro .subtitle {
            display: block;
            margin-top: 2px;
            font-size: .825rem;
        }

        /* optional header buttons */
        .btn-header {
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .25);
            color: #fff;
        }

        .btn-header:hover {
            background: rgba(255, 255, 255, .25);
            color: #fff;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header card-header-pro d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="icon-badge mr-2">
                            <i class="fas fa-coins"></i>
                        </span>
                        <div class="lh-1">
                            <h4 class="card-title m-0">Customer Operation</h4>
                            <small class="subtitle text-white-50">Bulk, Grace & Package actions</small>
                        </div>
                    </div>
                    <div class="header-actions d-none d-md-flex">
                        <button type="button" id="btn-refresh" class="btn btn-header btn-sm mr-2">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <script>
                            $(document).on('click', '#btn-refresh', function() {
                                var $btn = $(this);
                                var html0 = $btn.html();

                                $btn.prop('disabled', true)
                                    .html('<i class="fas fa-sync-alt fa-spin"></i> Refreshing...');

                                setTimeout(function() {
                                    toastr.success('Refresh Completed');
                                }, 2000);
                                setTimeout(function() {
                                    var url = new URL(window.location.href);
                                    url.searchParams.set('_r', Date.now().toString());
                                    window.location.replace(url.toString());
                                }, 2000);
                            });
                            $(function() {
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                        </script>

                        <a href="{{ route('admin.settings.information.index') }}" class="btn btn-header btn-sm">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </div>
                </div>

                <div class="card-body ">
                    @include('Backend.Component.Customer.search-form')
                </div>
                <div class="card-body d-none" id="print_area">
                    <!-- Toolbar (AdminLTE 3) -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card card-outline card-primary toolbar-card shadow-sm">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center flex-wrap">

                                        <!-- Left: status/info -->
                                        <div class="mr-auto d-flex align-items-center flex-wrap">
                                            <!-- selection count / status -->
                                            <span class="badge badge-info mr-2" id="selected-count" data-toggle="tooltip"
                                                title="Selected rows">
                                                0 Selected
                                            </span>
                                            <span class="text-muted small d-none d-md-inline">Customer Actions</span>
                                        </div>

                                        <!-- Right: actions -->
                                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar">

                                            <!-- Primary actions -->
                                            <div class="btn-group btn-group-sm mr-2" role="group"
                                                aria-label="Primary actions">
                                                <button type="button" class="btn btn-primary btn-icon  mr-2"
                                                    data-toggle="tooltip" title="Change Package">
                                                    <i class="fas fa-credit-card"></i><span class="lbl"> Change
                                                        Package</span>
                                                </button>

                                                <button type="button" class="btn btn-info btn-icon mr-2"
                                                    id="bulk_recharge_btn" data-toggle="tooltip" title="Bulk Recharge">
                                                    <i class="fas fa-layer-group"></i><span class="lbl"> Bulk
                                                        Recharge</span>
                                                </button>

                                                <button type="button" class="btn btn-success btn-icon mr-2"
                                                    id="grace_recharge_btn" data-toggle="tooltip" title="Grace Recharge">
                                                    <i class="fas fa-bolt"></i><span class="lbl"> Grace</span>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive responsive-table">
                        @include('Backend.Component.Customer.table')

                    </div>
                </div>
            </div>

        </div>
    </div>


    @include('Backend.Modal.Customer.Recharge.bulk_recharge_modal')
@endsection

@section('script')
    <script type="text/javascript">
    $(document).ready(function(){
        /******When -Bulk Recharge Butto/n Clicked**********/
        handle_trigger('#bulk_recharge_btn', '#bulk_rechargeModal','#selectedCustomerCount');
        /*Call bulk recharge Function*/
        handle_ajax_submit('#bulk_rechargeForm');
    });


    </script>
@endsection
