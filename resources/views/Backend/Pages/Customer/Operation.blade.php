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
                        <button type="button" class="btn btn-header btn-sm mr-2">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-header btn-sm">
                            <i class="fas fa-cog"></i> Settings
                        </button>
                    </div>
                </div>

                <div class="card-body ">
                    @include('Backend.Component.Customer.search-form')
                </div>
                <div class="card-body d-none" id="print_area">

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" id="change_package_btn" class="btn btn-primary mb-2">
                                <i class="fas fa-credit-card"></i>&nbsp; Change Package
                            </button>
                        </div>
                    </div>

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
