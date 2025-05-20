@extends('Backend.Layout.App')
@section('title', 'Dashboard | Payroll Management | Admin Panel')
@section('content')
    <div class="container-fluid">
        <div class="card ">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-money-bill-wave"></i>&nbsp; Create Employee Payroll
                </h3>

            </div>
            <form action="" method="POST" id="payrollForm">
                @csrf
                <div class="card-body row">
                    <div class="form-group col-md-3">
                        <label for="employee_id">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" id="employee_id" class="form-control" style="width: 100%;" required>
                            <option value="">-- Select Employee --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} || {{ $employee->phone }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="month">Month</label>
                        <input type="month" name="month_year" class="form-control" disabled required>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" class="form-control" style="width: 100%;">
                            <option>---Selete---</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" style="width: 100%;">
                            <option>---Selete---</option>
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                        </select>
                    </div>

                    <hr class="my-3 w-100">

                    <div class="form-group col-md-3">
                        <label>Basic Salary</label>
                        <input type="text" id="basic_salary" class="form-control" value="0" readonly>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Allowances</label>
                        <input type="text" id="allowances" class="form-control" value="0" readonly>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Tax</label>
                        <input type="text" id="tax" class="form-control" value="0" readonly>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Advance Salary</label>
                        <input type="text" name="advance_salary" id="advance_salary"  class="form-control" value="0" readonly>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Loan Deduction</label>
                        <input type="text" name="loan_deduction" class="form-control" value="0" readonly>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Net Salary</label>
                        <input type="text" name="net_salary" id="net_salary" class="form-control" value="0"
                            readonly>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit Payroll</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#employee_id').change(function() {

                if ($(this).val() !== '') {
                    $('input[name="month_year"]').prop('disabled', false);
                } else {
                    $('input[name="month_year"]').prop('disabled', true);
                    $('input[name="month_year"]').val('');
                }
                let empId = $(this).val();
                if (empId) {
                    $.ajax({
                        url: '{{ route('admin.hr.employee.salary.get_employee_salary') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            employee_id: empId
                        },
                        success: function(data) {
                            $('#basic_salary').val(data.basic_salary);
                            $('#allowances').val(data.total_allowance);
                            $('#advance_salary').val(data.advance_salary);
                            $('#loan_deduction').val(data.loan);
                            $('#tax').val(data.tax);
                            calculate_net_salary();
                        }
                    });
                }
            });

            function calculate_net_salary() {

                let basic   = parseFloat($('#basic_salary').val()) || 0;
                let advance = parseFloat($('#advance_salary').val()) || 0;
                let loan    = parseFloat($('#loan_deduction').val()) || 0;
                let tax     = parseFloat($('#tax').val()) || 0;

                let net = basic - (advance + loan + tax);
                $('#net_salary').val(net.toFixed(2));
            }
            $('#basic_salary, #advance_salary, #loan_deduction, #tax').on('input', calculate_net_salary);

            $(document).on('change', 'input[name="month_year"]', function() {
                var employee_id     = $('#employee_id').val();
                var month_year      = $(this).val();

                if (!employee_id) {
                    toastr.error("Please select employee first");
                    $(this).val('');
                    $(this).prop('disabled', true);
                }
                $.ajax({
                    url: '{{ route('admin.hr.employee.advance.get_advance_salary_by_month') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id : employee_id,
                        month_year  : month_year
                    },
                    success: function(response) {
                        $("#advance_salary").val(response.total_advance);
                    }
                });
            });
        });
    </script>
@endsection
