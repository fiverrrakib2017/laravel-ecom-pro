
<!-- Modal for Bulk Recharge -->
<div class="modal fade bs-example-modal-lg" id="bulk_rechargeModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content col-md-12">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span class="mdi mdi-account-check mdi-18px"></span>&nbsp;Bulk
                    Recharge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" id="selectedCustomerCount"></div>
                <form action="{{ route('admin.customer.bulk.recharge.store') }}" id="bulk_rechargeForm" method="POST">
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
                        <select name="recharge_month[]" class="form-control" multiple required>
                            @foreach ($years as $year)
                                @foreach ($months as $num => $name)
                                    @php
                                        $value = $year . '-' . str_pad($num, 2, '0', STR_PAD_LEFT); // ex: 2025-05
                                        $label = $name . ' ' . $year; // ex: May 2025
                                    @endphp
                                    <option value="{{ $value }}"
                                        {{ $num == $currentMonth && $year == $currentYear ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="">Transaction Type</label>
                        <select type="text" class="form-select" name="transaction_type" style="width: 100%;"
                            required>
                            <option value="">---Select---</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                            <option value="bkash">Bkash</option>
                            <option value="nagad">Nagad</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Remarks</label>
                        <input name="note" placeholder="Enter Remarks" class="form-control" type="text">
                    </div>
                    <div class="modal-footer ">
                        <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-money-bill-alt"></i>&nbsp; Confirm Recharge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
