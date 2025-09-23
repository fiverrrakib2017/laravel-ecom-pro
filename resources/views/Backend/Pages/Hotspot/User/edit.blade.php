@extends('Backend.Layout.App')
@section('title', 'Edit Hotspot User | Admin Panel')


@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <!-- Card Header -->
            @include('Backend.Component.Common.card-header', [
                'title' => 'Update Hotspot User',
                'description' => 'Hostpot user with status, usage & expiry details',
                'icon' => '<i class="fas fa-wifi"></i>',
                'button' => '<button type="button" onclick="window.location=\''.route('admin.hotspot.user.create').'\'" class="btn btn-header">
                    <i class="fas fa-user-plus"></i> Add User
                </button>'

            ])

      <div class="card-body">
        <form id="userForm"
              action="{{ route('admin.hotspot.user.update', $user->id) }}"
              method="post"
              data-redirect="{{ route('admin.hotspot.user.index') }}">
          @csrf
          @method('PUT')

          <div class="row">
            <!-- Router -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="router_id">Router <span class="text-danger">*</span></label>
                <select class="form-control" id="router_id" name="router_id">
                  <option value="">-- Select Router --</option>
                  @foreach($routers as $router)
                    <option value="{{ $router->id }}" {{ old('router_id',$user->router_id)==$router->id ? 'selected' : '' }}>
                      {{ $router->name }}
                    </option>
                  @endforeach
                </select>
                <span class="invalid-feedback" data-field="router_id"></span>
              </div>
            </div>

            <!-- Profile (dependent) -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="hotspot_profile_id">Hotspot Profile <span class="text-danger">*</span></label>
                <select class="form-control" id="hotspot_profile_id" name="hotspot_profile_id">
                  <option value="">-- Select Profile --</option>
                  @foreach($profiles as $pf)
                    <option value="{{ $pf->id }}"
                      {{ old('hotspot_profile_id', $user->hotspot_profile_id)==$pf->id ? 'selected' : '' }}>
                      {{ $pf->name }} ({{ $pf->mikrotik_profile }})
                    </option>
                  @endforeach
                </select>
                <small class="text-muted">Profiles shown belong to the selected router.</small>
                <span class="invalid-feedback" data-field="hotspot_profile_id"></span>
              </div>
            </div>

            <!-- Username -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="username">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="username" name="username"
                       value="{{ old('username', $user->username) }}">
                <span class="invalid-feedback" data-field="username"></span>
              </div>
            </div>

            <!-- Password (optional) -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="password">Password (leave blank to keep current)</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Optional: set a new password">
                  <div class="input-group-append">
                    <button class="btn btn-secondary" type="button" id="btn-generate"><i class="fas fa-magic"></i></button>
                    <button class="btn btn-secondary" type="button" id="btn-showhide" data-state="show"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-secondary" type="button" id="btn-copy"><i class="fas fa-copy"></i></button>
                  </div>
                </div>
                <small class="text-muted">Stored encrypted; not shown here for security.</small>
                <span class="invalid-feedback" data-field="password"></span>
              </div>
            </div>

            <!-- MAC Lock -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="mac_lock">MAC Lock</label>
                <input type="text" class="form-control" id="mac_lock" name="mac_lock"
                       value="{{ old('mac_lock', $user->mac_lock) }}" placeholder="e.g. AA:BB:CC:DD:EE:FF">
                <span class="invalid-feedback" data-field="mac_lock"></span>
              </div>
            </div>

            <!-- Status -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select class="form-control" id="status" name="status">
                  @foreach(['active','disabled','expired','blocked'] as $s)
                    <option value="{{ $s }}" {{ old('status', $user->status)===$s ? 'selected' : '' }}>
                      {{ ucfirst($s) }}
                    </option>
                  @endforeach
                </select>
                <span class="invalid-feedback" data-field="status"></span>
              </div>
            </div>

            <!-- Expiry -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="expires_at">Expires At</label>
                <input type="datetime-local" class="form-control" id="expires_at" name="expires_at"
                       value="{{ old('expires_at', optional($user->expires_at)->format('Y-m-d\TH:i')) }}">
                <span class="invalid-feedback" data-field="expires_at"></span>
              </div>
            </div>

            <!-- Comment -->
            <div class="col-md-12">
              <div class="form-group">
                <label for="comment">Comment</label>
                <textarea class="form-control" id="comment" name="comment" rows="3"
                          placeholder="Optional notes...">{{ old('comment', $user->comment) }}</textarea>
                <span class="invalid-feedback" data-field="comment"></span>
              </div>
            </div>
          </div>

          <div class="mt-3 d-flex">
            <button type="submit" id="btn-submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update User
            </button>
            <a href="{{ route('admin.hotspot.user.index') }}" class="btn btn-danger ml-2">
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
    const $form    = $('#userForm');
    const $submit  = $('#btn-submit');
    const $router  = $('#router_id');
    const $profile = $('#hotspot_profile_id');

    function clearErrors(){
        $form.find('.form-control').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }
    function showErrors(errors){
        Object.keys(errors).forEach(function(field){
            const $input = $form.find('[name="'+field+'"]');
            if($input.length){ $input.addClass('is-invalid'); }
            $form.find('.invalid-feedback[data-field="'+field+'"]').text(errors[field][0]);
        });
    }

    function loadProfiles(routerId, selectedId){
        if(!routerId){
            $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', true);
            return;
        }
        $profile.prop('disabled', true).html('<option>Loading...</option>');
        let url = "{{ route('admin.hotspot.profile.get_profile', ':id') }}";
        url = url.replace(':id', routerId);
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            headers: {'Accept':'application/json'},
            success: function(res){
                let html = '<option value="">-- Select Profile --</option>';
                if(res && res.success){
                    (res.profiles||[]).forEach(function(p){
                        const sel = (Number(selectedId)===Number(p.id)) ? 'selected' : '';
                        html += '<option value="'+p.id+'" '+sel+'>'+p.name+' ('+p.mikrotik_profile+')</option>';
                    });
                }
                $profile.html(html).prop('disabled', false);
            },
            error: function(){
                $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', false);
                toastr.error('Could not load profiles.');
            }
        });
    }

    // Change router -> refresh profiles
    $router.on('change', function(){
        loadProfiles($(this).val(), null);
    });

    // Password helpers
    $('#btn-generate').on('click', function(){
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#';
        let s = '';
        for(let i=0;i<10;i++){ s += chars.charAt(Math.floor(Math.random()*chars.length)); }
        $('#password').val(s).attr('type','text');
        $('#btn-showhide').data('state','hide').find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    });
    $('#btn-showhide').on('click', function(){
        const $btn = $(this), $pw = $('#password');
        if($btn.data('state')==='show'){
            $pw.attr('type','text'); $btn.data('state','hide').find('i').removeClass('fa-eye').addClass('fa-eye-slash');
        }else{
            $pw.attr('type','password'); $btn.data('state','show').find('i').removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    $('#btn-copy').on('click', function(){
        const $pw = $('#password'); $pw[0].select(); $pw[0].setSelectionRange(0,99999);
        document.execCommand('copy'); toastr.success('Password copied');
    });

    // Submit AJAX
    $form.on('submit', function(e){
        e.preventDefault();
        clearErrors();
        $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST', // _method=PUT included
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
                $submit.prop('disabled', false).html('<i class="fas fa-save"></i> Update User');
            }
        });
    });
});
</script>
@endsection
