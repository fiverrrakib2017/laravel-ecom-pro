@extends('Backend.Layout.App')
@section('title', 'Generate Vouchers Batch | Admin Panel')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <!-- Card Header -->
                @include('Backend.Component.Common.card-header', [
                    'title' => 'Generate Batch',
                    'description' => 'Create voucher',
                    'icon' => ' <i class="fas fa-cogs mr-2" style="font-size: 1.2rem;"></i>',
                    'button' =>
                        '<button type="button" onclick="window.location=\'' .
                        route('admin.hotspot.vouchers.batch.index') .
                        '\'" class="btn btn-header">
                               <i class="fas fa-list"></i> All Batches
                            </button>',
                ])

                <div class="card-body">
                    <form id="batchForm" action="{{ route('admin.hotspot.vouchers.batch.store') }}" method="post"
                        data-redirect="{{ route('admin.hotspot.vouchers.batch.index') }}">
                        @csrf
                        <div class="row">
                            @include('Backend.Component.Common.Select.hotspot_router_select_package')

                            <!-- Batch Name -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Batch Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Eid Offer 1GB 2H"
                                        required>
                                    <span class="invalid-feedback" data-field="name"></span>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="qty" class="form-control" value="50" min="1"
                                        max="2000" required>
                                    <span class="invalid-feedback" data-field="qty"></span>
                                </div>
                            </div>

                            <!-- Code Prefix -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Code Prefix</label>
                                    <input type="text" name="code_prefix" class="form-control" placeholder="e.g. EID"
                                        maxlength="10">
                                    <small class="small-muted">Optional (max 10)</small>
                                    <span class="invalid-feedback" data-field="code_prefix"></span>
                                </div>
                            </div>

                            <!-- Username Length -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Username Length <span class="text-danger">*</span></label>
                                    <input type="number" name="username_length" class="form-control" value="8"
                                        min="4" max="16" required>
                                    <span class="invalid-feedback" data-field="username_length"></span>
                                </div>
                            </div>

                            <!-- Password Length -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Password Length <span class="text-danger">*</span></label>
                                    <input type="number" name="password_length" class="form-control" value="6"
                                        min="4" max="16" required>
                                    <span class="invalid-feedback" data-field="password_length"></span>
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input type="checkbox" class="custom-control-input" id="pw_same"
                                            name="password_same_as_username" value="1">
                                        <label class="custom-control-label" for="pw_same">Password = Username</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Validity Days Override -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Validity (days override)</label>
                                    <input type="number" name="validity_days_override" class="form-control" min="1"
                                        max="3650">
                                    <span class="invalid-feedback" data-field="validity_days_override"></span>
                                </div>
                            </div>

                            <!-- Batch Expiry Date -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Batch Expires At</label>
                                    <input type="datetime-local" name="expires_at" class="form-control">
                                    <span class="invalid-feedback" data-field="expires_at"></span>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="number" name="price_minor" class="form-control" value="0"
                                        min="0">
                                    <small class="small-muted">৳100.00 => 10000</small>
                                    <span class="invalid-feedback" data-field="price_minor"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 d-flex justify-content-start">
    <button type="submit" id="btn-submit" class="btn btn-primary mr-2 d-flex align-items-center">
        <i class="fas fa-cogs mr-2" style="font-size: 1.2rem;"></i> Generate
    </button>
    <a href="{{ route('admin.hotspot.vouchers.batch.index') }}" class="btn btn-secondary d-flex align-items-center">
        <i class="fas fa-list mr-2" style="font-size: 1.2rem;"></i> All Batches
    </a>
</div>


                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            const $form = $('#batchForm'),
                $submit = $('#btn-submit');

            function clearErrors() {
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').text('');
            }

            function showErrors(errors) {
                Object.keys(errors).forEach(function(f) {
                    const $el = $form.find('[name="' + f + '"]');
                    if ($el.length) {
                        $el.addClass('is-invalid');
                    }
                    $form.find('.invalid-feedback[data-field="' + f + '"]').text((errors[f] || [])[0] ||
                    '');
                });
            }

            $form.on('submit', function(e) {
                e.preventDefault();
                clearErrors();
                $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(res) {
                        if (res && res.success) {
                            toastr.success(res.message || 'Batch generated');
                            // Take user straight to print
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('admin.hotspot.vouchers.print') }}" +
                                    "?batch_id=" + res.batch_id;
                            }, 800);
                        } else {
                            toastr.warning('Unexpected response.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showErrors((xhr.responseJSON || {}).errors || {});
                            toastr.error('Please fix the highlighted fields.');
                        } else {
                            toastr.error('Something went wrong.');
                        }
                    },
                    complete: function() {
                        $submit.prop('disabled', false).html(
                            '<i class="fas fa-bolt"></i> Generate');
                    }
                });
            });
        });
    </script>
@endsection
