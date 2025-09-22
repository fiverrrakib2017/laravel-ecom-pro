@extends('Backend.Layout.App')
@section('title', 'Hotspot Profile Create | Admin Panel')



@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            @include('Backend.Component.Common.card-header', [
                'title' => 'Hotspot Profile Create',
                'description' => 'Manage MikroTik hotspot plans & timeouts',
                'icon' => '<i class="fas fa-wifi"></i>'
            ])


            <div class="card-body">
                <form id="profileForm"action="{{ route('admin.hotspot.profile.store') }}" method="post">
                    @csrf

                    <div class="row">
                        <!-- Router -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="router_id">Router <span class="text-danger">*</span></label>
                                <select class="form-control" name="router_id" id="router_id">
                                    <option value="">-- Select Router --</option>
                                    @foreach(($routers ?? []) as $router)
                                        <option value="{{ $router->id }}">{{ $router->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" data-field="router_id"></span>
                            </div>
                        </div>

                        <!-- Name (display label) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Basic 5Mbps">
                                <span class="invalid-feedback" data-field="name"></span>
                            </div>
                        </div>

                        <!-- MikroTik Profile (exact profile name on router) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mikrotik_profile">MikroTik Profile <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mikrotik_profile" name="mikrotik_profile" placeholder="e.g. hs-basic-5m">
                                <small class="text-muted">Must match <code>/ip hotspot user profile</code> name.</small>
                                <span class="invalid-feedback" data-field="mikrotik_profile"></span>
                            </div>
                        </div>

                        <!-- Rate Limit -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rate_limit">Rate Limit</label>
                                <input type="text" class="form-control" id="rate_limit" name="rate_limit" placeholder="e.g. 5M/5M">
                                <small class="text-muted">Format: <code>rx/tx</code> (e.g. 5M/5M). Leave blank to use router profile default.</small>
                                <span class="invalid-feedback" data-field="rate_limit"></span>
                            </div>
                        </div>

                        <!-- Shared Users -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="shared_users">Shared Users</label>
                                <input type="number" min="1" class="form-control" id="shared_users" name="shared_users" value="1">
                                <span class="invalid-feedback" data-field="shared_users"></span>
                            </div>
                        </div>

                        <!-- Validity Days -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="validity_days">Validity (days)</label>
                                <input type="number" min="1" class="form-control" id="validity_days" name="validity_days" value="1">
                                <span class="invalid-feedback" data-field="validity_days"></span>
                            </div>
                        </div>

                        <!-- Price (minor unit / paisa) -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price_minor">Price (paisa)</label>
                                <input type="number" min="0" step="1" class="form-control" id="price_minor" name="price_minor" value="0">
                                <small class="text-muted">Store as minor unit (e.g. ৳100.00 = <b>10000</b>).</small>
                                <span class="invalid-feedback" data-field="price_minor"></span>
                            </div>
                        </div>

                        <!-- Idle Timeout -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="idle_timeout">Idle Timeout</label>
                                <input type="text" class="form-control" id="idle_timeout" name="idle_timeout" placeholder="e.g. 5m">
                                <small class="text-muted">Examples: <code>5m</code>, <code>1h</code>. Leave blank for router default.</small>
                                <span class="invalid-feedback" data-field="idle_timeout"></span>
                            </div>
                        </div>

                        <!-- Keepalive Timeout -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="keepalive_timeout">Keepalive Timeout</label>
                                <input type="text" class="form-control" id="keepalive_timeout" name="keepalive_timeout" placeholder="e.g. 2m">
                                <span class="invalid-feedback" data-field="keepalive_timeout"></span>
                            </div>
                        </div>

                        <!-- Session Timeout -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="session_timeout">Session Timeout</label>
                                <input type="text" class="form-control" id="session_timeout" name="session_timeout" placeholder="e.g. 1d">
                                <span class="invalid-feedback" data-field="session_timeout"></span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Optional notes..."></textarea>
                                <span class="invalid-feedback" data-field="notes"></span>
                            </div>
                        </div>

                        <!-- Active Switch -->
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label>Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                                <span class="invalid-feedback" data-field="is_active"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex">
                        <button type="submit" id="btn-submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Profile
                        </button>
                        <button type="button" onclick="history.back()" class="btn btn-danger ml-2">
                            <i class="fas fa-list"></i> Back to List
                        </button>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script>



    // AJAX create
    $(function () {
        const $form = $('#profileForm');
        const $submit = $('#btn-submit');

        function clearErrors() {
            $form.find('.form-control, .custom-control-input').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');
        }

        function showErrors(errors) {
            // errors is an object: { field: [msg1, msg2], ... }
            Object.keys(errors).forEach(function (field) {
                const msgs = errors[field];
                const $input = $form.find('[name="'+ field +'"]');
                if ($input.length) {
                    $input.addClass('is-invalid');
                }
                $form.find('.invalid-feedback[data-field="'+ field +'"]').text(msgs[0]);
            });
        }

        $form.on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                headers: {
                    'Accept': 'application/json'
                },
                success: function (res) {
                    if (res && res.success) {
                        toastr.success(res.message || 'Added Successfully');
                        const redirectUrl = $form.data('redirect');
                        // Redirect after a short pause
                        setTimeout(function () {
                            if (redirectUrl) {
                                window.location.href = redirectUrl;
                            } else {
                                window.location.reload();
                            }
                        }, 900);
                    } else {
                        toastr.warning('Unexpected response received.');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const resp = xhr.responseJSON || {};
                        showErrors(resp.errors || {});
                        toastr.error('Please fix the highlighted fields.');
                    } else {
                        toastr.error('Something went wrong. Please try again.');
                    }
                },
                complete: function () {
                    $submit.prop('disabled', false).html('<i class="fas fa-save"></i> Save Profile');
                }
            });
        });
    });
</script>
@endsection
