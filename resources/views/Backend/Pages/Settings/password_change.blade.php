@extends('Backend.Layout.App')
@section('title', 'Password Change | Admin Panel')


@section('content')
<div class="row">
    <div class="col-md-6 m-auto">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">Password Change</h3>
            </div>
            <div class="card-body">
                <!--  Information Form -->
                <form id="informationForm" action="{{ route('admin.settings.passowrd.change.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label for="" class="col-md-3 col-form-label">Current Password</label>
                        <div class="col-md-9">
                            <input type="password" name="current_password"  placeholder="Enter Current Password"  class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-md-3 col-form-label">New Password</label>
                        <div class="col-md-9">
                            <input type="password" name="new_password"  class="form-control" placeholder="Enter New Password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-md-3 col-form-label">Confirm New Password</label>
                        <div class="col-md-9">
                            <input type="password" name="confirm_new_password"  class="form-control" placeholder="Enter Confrim Password">
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-md-9 offset-md-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i>&nbsp;Password Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        $("#informationForm").submit(function(e) {
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

                            setTimeout(() => {
                                location.reload();
                            }, 500);
                            submitBtn.html(originalBtnText);
                            submitBtn.prop('disabled', false);
                            form.find(':input').prop('disabled', false);
                        }else if(response.success == false){
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
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);
                        form.find(':input').prop('disabled', false);
                    }
                });
            });
    });
</script>
@endsection
