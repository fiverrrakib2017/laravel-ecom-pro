@php
    $transaction_type       = $transaction_type ?? null;
    $message_check          = $message_check ?? '';
    $payable_amount         = $payable_amount ?? '';
    $package_name           = $package_name ?? '';
    $customer_amount        = $customer_amount ?? '';
    $multiple_select_month  = $multiple_select_month ?? '';
    $recharge_url           = $recharge_url ?? '';
@endphp

<div class="modal fade bs-example-modal-lg" id="CustomerRechargeModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content col-md-12">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span class="mdi mdi mdi-battery-charging-90 mdi-18px"></span>
                    &nbsp;
                    Customer Recharge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form
                    @if($recharge_url=='')
                        action  ="{{ route('admin.customer.recharge.store') }}"
                        id      ="customerRechargeForm"
                    @else
                        action  ="{{ route('customer.portal.recharge') }}"
                      
                    @endif

                    method="POST"
                    enctype="multipart/form-data"
                >

                    @csrf
                    @php
                        $months = [
                            1 => 'January',
                            2 => 'February',
                            3 => 'March',
                            4 => 'April',
                            5 => 'May',
                            6 => 'June',
                            7 => 'July',
                            8 => 'August',
                            9 => 'September',
                            10 => 'October',
                            11 => 'November',
                            12 => 'December',
                        ];

                        /*Current Month*/
                        $currentMonth = date('n');
                    @endphp

                    @php
                        $currentYear = date('Y');
                        $years = range($currentYear, $currentYear + 5);
                    @endphp

                    <div class="form-group mb-2">
                        <label>Recharge Month & Year</label>
                        <select @if($multiple_select_month=='') name="recharge_month[]" @else name="recharge_month[]" @endif   class="form-control" @if($multiple_select_month=='') multiple @endif required>
                            @foreach ($years as $year)
                                @foreach ($months as $num => $name)
                                    @php
                                        $value = $year . '-' . str_pad($num, 2, '0', STR_PAD_LEFT); // ex: 2025-05
                                        $label = $name . ' ' . $year;
                                    @endphp
                                    <option value="{{ $value }}" {{ ($num == $currentMonth && $year == $currentYear) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="d-none">
                        <input name="pop_id" class="form-control" type="text" value="{{ $data->pop_id ?? '' }}">
                        <input name="area_id" class="form-control" type="text" value="{{ $data->area_id ?? '' }}">
                        <input name="customer_id" class="form-control" type="text" value="{{ $data->id ?? '' }}">
                    </div>
                    <div class="form-group mb-2">
                        <label>Package</label>
                        <input readonly class="form-control" type="text" value="{{ $data->package->name ?? $package_name ?? 'N/A' }}">
                    </div>
                    <div class="form-group mb-2">
                        <label>Amount</label>
                        <input readonly name="amount" placeholder="Enter Amount" class="form-control" type="number"
                            value="{{ $data->amount ?? $customer_amount ?? '0' }}" required>
                    </div>
                    @if($payable_amount !=='disabled')
                    <div class="form-group mb-2">
                        <label>Payable Amount</label>
                        <input name="payable_amount" placeholder="Enter Amount" class="form-control" type="number"
                            value="{{ $data->amount ?? '0' }}" required>
                    </div>
                    @endif
                    @if($transaction_type !=='disabled')
                    <div class="form-group mb-2">
                        <label for="">Transaction Type</label>
                        <select type="text" class="form-select" name="transaction_type" style="width: 100%;"
                            required>
                            <option value="">---Select---</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                            <option value="bkash">Bkash</option>
                            <option value="nagad">Nagad</option>
                            <option value="due_paid">Due Paid</option>
                        </select>
                    </div>
                    @endif

                    <div class="form-group mb-2" id="voucherField" style="display: none;">
                        <label>Voucher No.</label>
                        <input name="voucher_no" placeholder="Enter Voucher No." class="form-control" type="text">
                    </div>
                    <script>
                        $(document).ready(function() {
                            $('select[name="transaction_type"]').on('change', function() {
                                var selectedType = $(this).val();
                                if (selectedType === 'cash') {
                                    $('#voucherField').show();
                                }else {
                                    $('#voucherField').hide();
                                    $('input[name="voucher_no"]').val('');
                                }
                                if(selectedType==='due_paid'){
                                   $("#CustomerRechargeModal input[name=send_message]").prop('checked', false);
                                }
                            });
                        });
                    </script>

                    <div class="form-group mb-3">
                        <label>Remarks</label>
                        <input name="note" placeholder="Enter Remarks" class="form-control" type="text">
                    </div>

                    @if($message_check !=='disabled')
                    <div class="form-group">
                        <!-- Checkbox Message -->
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" id="sendMessageCheckbox" name="send_message" value="1">
                            <label for="sendMessageCheckbox">
                                Send message to the Customer
                            </label>
                        </div>
                    </div>
                    @endif
                    <div class="modal-footer ">
                        <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
<!-- Toast Message -->
<script src="{{ asset('Backend/dist/js/toastr.min.js') }}"></script>
<script>
    $(document).ready(function() {

        /*****Customer Recharge ****/
        ___handleSubmit('#customerRechargeForm', '#CustomerRechargeModal');

        function ___handleSubmit(formSelector, modalSelector) {
            $(formSelector).submit(function(e) {
                e.preventDefault();

                /* Get the submit button */
                var submitBtn = $(this).find('button[type="submit"]');
                var originalBtnText = submitBtn.html();

                submitBtn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden"></span>'
                    );
                submitBtn.prop('disabled', true);

                var form = $(this);
                var formData = new FormData(this);

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
                        if (response.success == true) {
                            toastr.success(response.message);
                            form[0].reset();
                            /* Hide the modal */
                            $(modalSelector).modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                            form.find(':input').prop('disabled', false);
                        } else if (response.success == false) {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            /* Validation error*/
                            var errors = xhr.responseJSON.errors;

                            /* Loop through the errors and show them using toastr*/
                            $.each(errors, function(field, messages) {
                                $.each(messages, function(index, message) {
                                    /* Display each error message*/
                                    toastr.error(message);
                                });
                            });
                        } else {
                            /*General error message*/
                            toastr.error('An error occurred. Please try again.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        form.find(':input').prop('disabled', false);
                    }
                });
            });
        }
        $(document).on('change', "select[name='recharge_month[]']", function() {
            let result = $(this).val();

            let per_month_amont = parseFloat($("input[name='amount']").val()) || 0;

            let payableAmount = result.length * per_month_amont;
            payableAmount = Math.round(payableAmount);
            $("input[name='payable_amount']").val(payableAmount);
        });
    });
</script>
