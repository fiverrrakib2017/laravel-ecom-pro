@extends('Backend.Layout.App')
@section('title', 'Application Settings | Admin Panel')

@section('style')
    <!-- Custom styles for the page can be added here -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-md-7 m-auto">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">App Information Settings</h3>
            </div>
            <div class="card-body">
                <!--  Information Form -->
                <form id="informationForm" action="{{ route('admin.settings.information.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id ?? '' }}">
                    <div class="form-group row">
                        <label for="logo" class="col-md-3 col-form-label">App Logo</label>
                        <div class="col-md-9">
                            <input type="file" id="logo" name="logo" class="form-control">
                            @if (!empty($data->logo))
                            <img height="100" width="100" src="{{ asset('Backend/uploads/photos/' . $data->logo) }}" class="img-fluid" alt="Logo">
                        @endif


                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="school_name" class="col-md-3 col-form-label">Company Name</label>
                        <div class="col-md-9">
                            <input type="text" name="name"  class="form-control"  value="{{ $data->name ?? '' }}">

                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="address" class="col-md-3 col-form-label">Address</label>
                        <div class="col-md-9">
                            <textarea id="address" name="address" class="form-control" rows="3">{{ $data->address ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="phone_number" class="col-md-3 col-form-label">Phone Number</label>
                        <div class="col-md-9">
                            <input type="text"  name="phone_number"  class="form-control" value="{{ $data->phone_number ?? '' }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-md-3 col-form-label">Email Address</label>
                        <div class="col-md-9">
                            <input type="email"  name="email" class="form-control" value="{{ $data->email ?? '' }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-9 offset-md-3">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
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
