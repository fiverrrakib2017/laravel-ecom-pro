@extends('Backend.Layout.App')
@section('title', 'Customer Operation | Admin Panel')
@section('style')
<style>
.card-header-pro{
  position: relative;
  overflow: hidden;
  padding: 16px 20px;
  border: 0;
  color: #fff;
  background: linear-gradient(135deg, #17a2b8 0%, #0ea5e9 45%, #2563eb 100%);
  box-shadow: inset 0 -1px 0 rgba(255,255,255,.2);
  border-top-left-radius: .25rem; /* Bootstrap card radius keep */
  border-top-right-radius: .25rem;
}

/* soft glow decor */
.card-header-pro::after{
  content: "";
  position: absolute;
  right: -30px; top: -30px;
  width: 180px; height: 180px;
  pointer-events: none;
  background: radial-gradient(circle at 30% 30%,
             rgba(255,255,255,.35), rgba(255,255,255,0) 60%);
  transform: rotate(25deg);
  opacity: .7;
}

.card-header-pro .card-title{
  font-weight: 700;
  letter-spacing: .2px;
}

.card-header-pro .icon-badge{
  width: 44px; height: 44px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,.15);
  color: #fff;
  box-shadow: 0 6px 16px rgba(0,0,0,.08),
              inset 0 0 0 1px rgba(255,255,255,.25);
}

.card-header-pro .subtitle{
  display: block;
  margin-top: 2px;
  font-size: .825rem;
}

/* optional header buttons */
.btn-header{
  background: rgba(255,255,255,.16);
  border: 1px solid rgba(255,255,255,.25);
  color: #fff;
}
.btn-header:hover{
  background: rgba(255,255,255,.25);
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
                        <button type="button"  id="btn-refresh" class="btn btn-header btn-sm mr-2">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <script>
                            $(document).on('click', '#btn-refresh', function () {
                                var $btn  = $(this);
                                var html0 = $btn.html();

                                $btn.prop('disabled', true)
                                    .html('<i class="fas fa-sync-alt fa-spin"></i> Refreshing...');

                                 setTimeout(function () {
                                    toastr.success('Refresh Completed');
                                }, 2000);
                                setTimeout(function () {
                                    var url = new URL(window.location.href);
                                    url.searchParams.set('_r', Date.now().toString());
                                    window.location.replace(url.toString());
                                }, 2000);
                            });
                             $(function(){ $('[data-toggle="tooltip"]').tooltip(); });
                        </script>

                        <a href="{{route('admin.settings.information.index')}}" class="btn btn-header btn-sm">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </div>
                </div>

                <div class="card-body ">
                    @include('Backend.Component.Customer.search-form')
                </div>
                <div class="card-body d-none" id="print_area">

                    <div class="row">
                        <div class="col-12">
                            <div class="toolbar-pro d-flex align-items-center">
                            <div class="tp-left d-flex align-items-center mr-auto">
                              
                            </div>

                            <div class="tp-right d-flex align-items-center flex-wrap">
                                <button type="button" class="btn-sm btn btn-primary js-change-package" data-toggle="tooltip" title="Change Package">
                                <i class="fas fa-credit-card"></i><span class="lbl"> Change Package</span>
                                </button>

                                <button type="button" class="tp-btn tp-primary" id="bulk_recharge_btn" data-toggle="tooltip" title="Bulk Recharge">
                                <i class="fas fa-layer-group"></i><span class="lbl"> Bulk Recharge</span>
                                </button>

                                <button type="button" class="tp-btn tp-success" id="grace_recharge_btn" data-toggle="tooltip" title="Grace Recharge">
                                <i class="fas fa-bolt"></i><span class="lbl"> Grace</span>
                                </button>

                                <button type="button" class="tp-btn tp-ghost" id="btn-export" data-toggle="tooltip" title="Export CSV">
                                <i class="fas fa-file-export"></i><span class="lbl"> Export</span>
                                </button>

                                <button type="button" class="tp-btn tp-ghost" onclick="window.print()" data-toggle="tooltip" title="Print">
                                <i class="fas fa-print"></i><span class="lbl"> Print</span>
                                </button>

                                <div class="dropdown tp-drop">
                                <button class="tp-btn tp-ghost dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i><span class="lbl"> More</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow">
                                    <a class="dropdown-item" href="#" id="btn-refresh"><i class="fas fa-sync-alt"></i> Refresh</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" id="btn-clear"><i class="fas fa-times-circle"></i> Clear Selection</a>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <style>


                    </style>


                    <div class="table-responsive responsive-table">
                        @include('Backend.Component.Customer.table')

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        /******When Bulk Recharge Button Clicked**********/
        handle_bulk_recharge_trigger('#bulk_recharge_btn', '#bulk_rechargeModal', '#selectedCustomerCount');
        /******When Grace Recharge Button Clicked**********/
        handle_bulk_recharge_trigger('#grace_recharge_btn', '#graceRechargeModal', '#grace_recharge_customer_Count');


        /*Call bulk recharge Function*/
        handle_ajax_submit('#bulk_rechargeForm');
        /*Call Grace recharge Function*/
        handle_ajax_submit('#grace_rechargeForm', function() {
            $('#graceRechargeModal').modal('hide');
            setTimeout(() => location.reload(), 500);
        });
    </script>

@endsection
