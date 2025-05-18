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
                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label for="month">Month</label>
                    <input type="month" name="month_year" class="form-control" required>
                </div>

                <div class="form-group col-md-2">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" class="form-control" style="width: 100%;">
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
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                    </select>
                </div>

                <hr class="my-3 w-100">

                <div class="form-group col-md-3">
                    <label>Basic Salary</label>
                    <input type="text" id="basic_salary" class="form-control" readonly>
                </div>

                <div class="form-group col-md-3">
                    <label>Allowances</label>
                    <input type="text" id="allowances" class="form-control" readonly>
                </div>

                <div class="form-group col-md-3">
                    <label>Tax</label>
                    <input type="text" id="tax" class="form-control" readonly>
                </div>

                <div class="form-group col-md-3">
                    <label>Advance Salary</label>
                    <input type="number" name="advance_salary" class="form-control" value="0">
                </div>

                <div class="form-group col-md-3">
                    <label>Loan Deduction</label>
                    <input type="number" name="loan_deduction" class="form-control" value="0">
                </div>

                <div class="form-group col-md-3">
                    <label>Net Salary</label>
                    <input type="text" name="net_salary" id="net_salary" class="form-control" readonly>
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
<script>
    $('#employee_id').change(function() {
        let empId = $(this).val();
        if (empId) {
            $.ajax({
                url: '{{ route("admin.hr.employee.salary.get_employee_salary") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    employee_id: empId
                },
                success: function(data) {
                    $('#basic_salary').val(data.basic_salary);
                    $('#allowances').val(data.house_allowance + data.medical_allowance + data.other_allowance);
                    $('#tax').val(data.tax);

                    // Calculate net_salary
                    let total = parseFloat(data.basic_salary) + parseFloat(data.house_allowance) +
                                parseFloat(data.medical_allowance) + parseFloat(data.other_allowance);
                    total = total - parseFloat(data.tax);
                    $('#net_salary').val(total.toFixed(2));
                }
            });
        }
    });
</script>
@endsection
