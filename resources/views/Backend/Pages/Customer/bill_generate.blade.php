@extends('Backend.Layout.App')
@section('title', 'Bill Generate | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                @include('Backend.Component.Common.card-header', [
                    'title' => 'Bill Generate',
                    'description' => 'Generate and manage user bills for the current month.',
                    'icon' => '<i class="fas fa-money-bill-alt"></i>',
                ])

                <div class="card-body">

                    <div class="table-responsive responsive-table">

                        <table id="datatable1" class="table table-bordered table-striped nowrap"
                            style="border-collapse: collapse; width: 100%;">
                            <thead >
                                <tr>
                                    <th >ID</th>
                                    <th >Username</th>
                                    <th >Month</th>
                                    <th >Price</th>
                                    <th >
                                        <input type="checkbox" id="selectAll" class="customer-checkbox">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td >{{ $row['id'] }}</td>
                                        <td >{!! $row['username'] !!}</td>
                                        <td >{!! $row['month'] !!}</td>
                                        <td >{!! $row['price'] !!}</td>
                                        <td >
                                            <input type="checkbox" class="customer-checkbox checkSingle"
                                                value="{{ $row['id'] }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right">
                                        <button type="button" id="bulk_recharge_btn" class="btn btn-success">
                                            <i class="fas fa-money-bill-alt"></i> Mark as Bulk Recharge
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>


                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-------Bulk Recharge Modal---------->
    @include('Backend.Modal.Customer.Recharge.bulk_recharge_modal');
@endsection

@section('script')

    <script type="text/javascript">
        $(document).ready(function() {
            $("#datatable1").DataTable();
            $('#selectAll').on('click', function() {
                $('.customer-checkbox').prop('checked', this.checked);

            });

            $('.customer-checkbox').on('click', function() {
                    if ($('.customer-checkbox:checked').length == $('.customer-checkbox').length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
            });
            /******When  Button Clicked**********/
            _handle_trigger('#bulk_recharge_btn', '#bulk_rechargeModal', '#selectedCustomerCount');
            /*---------Call Function For Submit -------*/
            _handle_ajax_submit('#bulk_rechargeForm');
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
