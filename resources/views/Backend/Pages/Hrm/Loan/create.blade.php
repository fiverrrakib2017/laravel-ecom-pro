@extends('Backend.Layout.App')
@section('title','Add Employee | Admin Panel')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <form action="{{ route('admin.hr.employee.loan.store') }}" method="POST" enctype="multipart/form-data" id="addEmployeeForm">
                @csrf
                <div class="card-body">

                    <!-- 1. Personal Information -->
                    <fieldset class="border p-4 shadow-sm rounded mb-4" style="border:2px #c9c9c9 dotted !important;">
                        <legend class="w-auto px-3 text-primary fw-bold">Personal Information</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Employee Name <span class="text-danger">*</span></label>
                                <select type="text" name="employee_id" class="form-control" required>
                                    <option value="">--Select Employee--</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Loan Amount <span class="text-danger">*</span></label>
                                <input type="text" name="loan_amount" class="form-control" placeholder="Enter Amount" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Installment Amount <span class="text-danger">*</span></label>
                                <input type="text" name="installment_amount" class="form-control" placeholder="Enter Installment Amount " required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Total Installment <span class="text-danger">*</span></label>
                                <input type="text" name="total_installments" class="form-control" placeholder="Enter Total Installment" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Paid Installments <span class="text-danger">*</span></label>
                                <input type="text" name="paid_installments" class="form-control" placeholder="Enter Paid Installment" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Issu Date <span class="text-danger">*</span></label>
                                <input type="date" name="date_issued" class="form-control" required>
                            </div>
                        </div>
                    </fieldset>

                </div>

                <div class="card-footer text-end">
                    <button type="button" onclick="history.back();" class="btn btn-danger">Back</button>
                    <button type="submit" class="btn btn-success">Apply Loan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection



@section('script')
<script type="text/javascript">
    $(document).ready(function(){

        $('#addEmployeeForm').submit(function(e) {
            e.preventDefault();

            /* Get the submit button */
            var submitBtn = $(this).find('button[type="submit"]');
            var originalBtnText = submitBtn.html();

            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>');
            submitBtn.prop('disabled', true);

            var form = $(this);
            var url = form.attr('action');
            /*Change to FormData to handle file uploads*/
            var formData = new FormData(this);

            /* Use Ajax to send the request */
            $.ajax({
                type: 'POST',
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    /* Disable the Form input */
                    form.find(':input').prop('disabled', true);
                    submitBtn.prop('disabled', true);
                },
                success: function(response) {

                    if (response.success) {
                        toastr.success(response.message);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                    if(response.success == false){
                        form.find(':input').prop('disabled', false);
                        toastr.error(response.message);
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                    form.find(':input').prop('disabled', false);

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            toastr.error(errors[field][0]);
                        }
                    } else {
                        toastr.error("Something went wrong! Please try again.");
                    }
                },
                complete: function() {
                    /* Reset button text and enable the button */
                    form.find(':input').prop('disabled', false);
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                }
            });
        });


    });
  </script>


@endsection
