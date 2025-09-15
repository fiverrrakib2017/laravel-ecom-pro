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
                            <h4 class="card-title m-0">Customer Operation</h4><br>
                            <small class="subtitle text-white-50">Change Expire date,Bulk Recharge, Grace Recharge & Package actions</small>
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
                                    <div class="d-flex flex-wrap justify-content-between align-items-center">

                                        <!-- Left: status/info -->
                                        <div class="mr-auto d-flex align-items-center flex-wrap">
                                            <!-- selection count / status -->
                                            <span class="badge badge-info mr-2" id="selected-count" data-toggle="tooltip" title="Selected rows">
                                                0 Selected
                                            </span>
                                            <span class="text-muted small d-none d-md-inline">Customer Actions</span>
                                        </div>

                                        <!-- Right: actions -->
                                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar">

                                            <!-- Primary actions -->
                                            <div class="btn-group btn-group-sm mr-2" role="group" aria-label="Primary actions">
                                                <!-- Change Expire Date Button -->
                                                <button type="button" class="btn btn-primary btn-icon mr-2 mb-2 mb-sm-0" id="change_expire_date_btn" data-toggle="tooltip" title="Change Expire Date">
                                                    <i class="fas fa-calendar-alt"></i><span class="ml-2 d-none d-sm-inline">Change Expire Date</span>
                                                </button>

                                                <!-- Change Package Button -->
                                                <button type="button" id="package_change_btn"  class="btn btn-warning btn-icon mr-2 mb-2 mb-sm-0" data-toggle="tooltip" title="Change Package">
                                                    <i class="fas fa-cogs"></i><span class="ml-2 d-none d-sm-inline">Change Package</span>
                                                </button>

                                                <!-- Bulk Recharge Button -->
                                                <button type="button" class="btn btn-info btn-icon mr-2 mb-2 mb-sm-0" id="bulk_recharge_btn" data-toggle="tooltip" title="Bulk Recharge">
                                                    <i class="fas fa-layer-group"></i><span class="ml-2 d-none d-sm-inline">Bulk Recharge</span>
                                                </button>

                                                <!-- Grace Recharge Button -->
                                                {{-- <button type="button" class="btn btn-success btn-icon mr-2 mb-2 mb-sm-0" id="grace_recharge_btn" data-toggle="tooltip" title="Grace Recharge">
                                                    <i class="fas fa-bolt"></i><span class="ml-2 d-none d-sm-inline">Grace Recharge</span>
                                                </button> --}}
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

    <!-------Bulk Recharge ---------->
    @include('Backend.Modal.Customer.Recharge.bulk_recharge_modal')
    <!------- Change package  ---------->
    @include('Backend.Modal.Customer.change_package_modal')
    <!------change Expire Date ---------->
    @include('Backend.Modal.Customer.change_expire_date_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            /******When  Button Clicked**********/
            _handle_trigger('#bulk_recharge_btn', '#bulk_rechargeModal', '#selectedCustomerCount');
            _handle_trigger('#package_change_btn', '#bulk_change_packageModal', '#bulk_change_packageModal #selectedCustomerCount');
            _handle_trigger('#change_expire_date_btn', '#bulk_change_expire_dateModal',
                '#bulk_change_expire_dateModal #selectedCustomerCount');

            /*---------Call Function For Submit -------*/
            _handle_ajax_submit('#bulk_rechargeForm');
            _handle_ajax_submit('#bulk_change_expire_dateForm');
            _handle_ajax_submit('#bulk_change_packageForm');

            /***-----submit form function ------****/
            function _handle_ajax_submit(formId, __success_call_back = null) {
                $(formId).submit(function(e) {
                    e.preventDefault();

                    let form = $(this);
                    let submitBtn = form.find('button[type="submit"]');
                    let originalBtnText = submitBtn.html();

                    submitBtn.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                    ).prop('disabled', true);

                    let formData = new FormData(this);

                    let customer_ids = [];
                    $(".checkSingle:checked").each(function() {
                        customer_ids.push($(this).val());
                    });
                    customer_ids.forEach(function(id) {
                        formData.append('customer_ids[]', id);
                    });

                    $.ajax({
                        type: form.attr('method'),
                        url: form.attr('action'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            form.find(':input').prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.success === true) {
                                toastr.success(response.message);
                                form[0].reset();

                                if (typeof __success_call_back === "function") {
                                    __success_call_back();
                                } else {
                                    setTimeout(() => location.reload(), 500);
                                }

                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(field, messages) {
                                    $.each(messages, function(index, message) {
                                        toastr.error(message);
                                    });
                                });
                            } else {
                                toastr.error('An error occurred. Please try again.');
                            }
                        },
                        complete: function() {
                            submitBtn.html(originalBtnText).prop('disabled', false);
                            form.find(':input').prop('disabled', false);
                        }
                    });
                });
            }
            /***Trigger button****/
            function _handle_trigger(button_selector, modalId, textSelector) {
                $(document).on('click', button_selector, function(event) {
                    event.preventDefault();

                    var __selected_customers = [];
                    $(".checkSingle:checked").each(function() {
                        __selected_customers.push($(this).val());
                    });

                    if (__selected_customers.length === 0) {
                        toastr.error('Please select at least one customer.');
                        return;
                    }

                    var countText = "You have selected " + __selected_customers.length + " customers.";
                    $(textSelector).text(countText);
                    $(modalId).modal('show');
                });
            }
        });
    </script>
@endsection
