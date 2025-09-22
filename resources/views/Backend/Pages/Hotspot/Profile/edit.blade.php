@extends('Backend.Layout.App')
@section('title', 'Edit Hotspot Profile | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            @include('Backend.Component.Common.card-header', [
                'title' => 'Edit Hotspot Profile',
                'description' => 'Update MikroTik hotspot plan & timeouts',
                'icon' => '<i class="fas fa-wifi"></i>'
            ])

            <div class="card-body">
                <form id="profileForm"
                      action="{{ route('admin.hotspot.profile.update', $profile->id) }}"
                      method="post"
                      data-redirect="{{ route('admin.hotspot.profile.index') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Router -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="router_id">Router <span class="text-danger">*</span></label>
                                <select class="form-control" name="router_id" id="router_id">
                                    <option value="">-- Select Router --</option>
                                    @foreach($routers as $router)
                                        <option value="{{ $router->id }}"
                                            {{ old('router_id', $profile->router_id)==$router->id?'selected':'' }}>
                                            {{ $router->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" data-field="router_id"></span>
                            </div>
                        </div>

                        <!-- Display Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{ old('name', $profile->name) }}" placeholder="e.g. Basic 5Mbps">
                                <span class="invalid-feedback" data-field="name"></span>
                            </div>
                        </div>

                        <!-- MikroTik Profile -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mikrotik_profile">MikroTik Profile <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mikrotik_profile" name="mikrotik_profile"
                                       value="{{ old('mikrotik_profile', $profile->mikrotik_profile) }}"
                                       placeholder="e.g. hs-basic-5m">
                                <small class="text-muted">Must match <code>/ip hotspot user profile</code> name.</small>
                                <span class="invalid-feedback" data-field="mikrotik_profile"></span>
                            </div>
                        </div>

                        <!-- Rate Limit -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rate_limit">Rate Limit</label>
                                <input type="text" class="form-control" id="rate_limit" name="rate_limit"
                                       value="{{ old('rate_limit', $profile->rate_limit) }}" placeholder="e.g. 5M/5M">
                                <span class="invalid-feedback" data-field="rate_limit"></span>
                            </div>
                        </div>

                        <!-- Shared Users -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="shared_users">Shared Users</label>
                                <input type="number" min="1" class="form-control" id="shared_users" name="shared_users"
                                       value="{{ old('shared_users', $profile->shared_users) }}">
                                <span class="invalid-feedback" data-field="shared_users"></span>
                            </div>
                        </div>

                        <!-- Validity Days -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="validity_days">Validity (days)</label>
                                <input type="number" min="1" class="form-control" id="validity_days" name="validity_days"
                                       value="{{ old('validity_days', $profile->validity_days) }}">
                                <span class="invalid-feedback" data-field="validity_days"></span>
                            </div>
                        </div>

                        <!-- Price (minor unit) -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price_minor">Price (paisa)</label>
                                <input type="number" min="0" step="1" class="form-control" id="price_minor" name="price_minor"
                                       value="{{ old('price_minor', $profile->price_minor) }}">
                                <small class="text-muted">৳100.00 = <b>10000</b>.</small>
                                <span class="invalid-feedback" data-field="price_minor"></span>
                            </div>
                        </div>

                        <!-- Idle Timeout -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="idle_timeout">Idle Timeout</label>
                                <input type="text" class="form-control" id="idle_timeout" name="idle_timeout"
                                       value="{{ old('idle_timeout', $profile->idle_timeout) }}" placeholder="e.g. 5m">
                                <span class="invalid-feedback" data-field="idle_timeout"></span>
                            </div>
                        </div>

                        <!-- Keepalive Timeout -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="keepalive_timeout">Keepalive Timeout</label>
                                <input type="text" class="form-control" id="keepalive_timeout" name="keepalive_timeout"
                                       value="{{ old('keepalive_timeout', $profile->keepalive_timeout) }}" placeholder="e.g. 2m">
                                <span class="invalid-feedback" data-field="keepalive_timeout"></span>
                            </div>
                        </div>

                        <!-- Session Timeout -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="session_timeout">Session Timeout</label>
                                <input type="text" class="form-control" id="session_timeout" name="session_timeout"
                                       value="{{ old('session_timeout', $profile->session_timeout) }}" placeholder="e.g. 1d">
                                <span class="invalid-feedback" data-field="session_timeout"></span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                          placeholder="Optional notes...">{{ old('notes', $profile->notes) }}</textarea>
                                <span class="invalid-feedback" data-field="notes"></span>
                            </div>
                        </div>

                        <!-- Active Switch -->
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label>Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $profile->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                                <span class="invalid-feedback" data-field="is_active"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex">
                        <button type="submit" id="btn-submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <a href="{{ route('admin.hotspot.profile.index') }}" class="btn btn-danger ml-2">
                            <i class="fas fa-list"></i> Cancel
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
$(function () {
    const $form = $('#profileForm');
    const $submit = $('#btn-submit');

    function clearErrors() {
        $form.find('.form-control, .custom-control-input').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }
    function showErrors(errors) {
        Object.keys(errors).forEach(function (field) {
            const $input = $form.find('[name="'+ field +'"]');
            if ($input.length) $input.addClass('is-invalid');
            $form.find('.invalid-feedback[data-field="'+ field +'"]').text(errors[field][0]);
        });
    }

    $form.on('submit', function(e){
        e.preventDefault();
        clearErrors();
        $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            headers: {'Accept':'application/json'},
            success: function(res){
                if(res && res.success){
                    toastr.success(res.message || 'Updated Successfully');
                    setTimeout(function(){ window.location.href = $form.data('redirect'); }, 900);
                }else{
                    toastr.warning('Unexpected response.');
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    showErrors((xhr.responseJSON||{}).errors || {});
                    toastr.error('Please fix the highlighted fields.');
                }else{
                    toastr.error('Something went wrong.');
                }
            },
            complete: function(){
                $submit.prop('disabled', false).html('<i class="fas fa-save"></i> Update Profile');
            }
        });
    });
});
</script>
@endsection
