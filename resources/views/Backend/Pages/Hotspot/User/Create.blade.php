@extends('Backend.Layout.App')
@section('title', 'Add Hotspot User | Admin Panel')


@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
        @include('Backend.Component.Common.card-header', [
            'title' => 'Add Hotspot User',
            'description' => 'Create credentials & assign MikroTik profile',
            'icon' => '<i class="fas fa-wifi"></i>'
        ])

      <div class="card-body">
        <form id="userForm"
              action="{{ route('admin.hotspot.user.store') }}"
              method="post"
              data-redirect="{{ route('admin.hotspot.user.index') }}">
          @csrf
          <div class="row">
            <!-- Router -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="router_id">Router <span class="text-danger">*</span></label>
                <select class="form-control" id="router_id" name="router_id">
                  <option value="">-- Select Router --</option>
                  @foreach($routers as $router)
                    <option value="{{ $router->id }}">{{ $router->name }}</option>
                  @endforeach
                </select>
                <span class="invalid-feedback" data-field="router_id"></span>
              </div>
            </div>

            <!-- Profile  -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="hotspot_profile_id">Hotspot Profile <span class="text-danger">*</span></label>
                <select class="form-control" id="hotspot_profile_id" name="hotspot_profile_id" disabled>
                  <option value="">-- Select Profile --</option>
                </select>
                <small class="text-muted">Only active profiles for the selected router are shown.</small>
                <span class="invalid-feedback" data-field="hotspot_profile_id"></span>
              </div>
            </div>

            <!-- Username -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="username">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="username" name="username" placeholder="e.g. user123">
                <span class="invalid-feedback" data-field="username"></span>
              </div>
            </div>

            <!-- Password with generate / show -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="password">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="text" class="form-control" id="password" name="password" placeholder="Type or generate">
                  <div class="input-group-append">
                    <button class="btn btn-secondary" type="button" id="btn-generate" title="Generate random"><i class="fas fa-magic"></i></button>
                    <button class="btn btn-secondary" type="button" id="btn-showhide" data-state="show" title="Show/Hide"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-secondary" type="button" id="btn-copy" title="Copy"><i class="fas fa-copy"></i></button>
                  </div>
                </div>
                <small class="text-muted">Stored encrypted for later print/recovery.</small>
                <span class="invalid-feedback" data-field="password"></span>
              </div>
            </div>

            <!-- MAC Lock -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="mac_lock">MAC Lock</label>
                <input type="text" class="form-control" id="mac_lock" name="mac_lock" placeholder="e.g. AA:BB:CC:DD:EE:FF">
                <span class="invalid-feedback" data-field="mac_lock"></span>
              </div>
            </div>

            <!-- Status -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select class="form-control" id="status" name="status">
                  <option value="active" selected>Active</option>
                  <option value="disabled">Disabled</option>
                  <option value="expired">Expired</option>
                  <option value="blocked">Blocked</option>
                </select>
                <span class="invalid-feedback" data-field="status"></span>
              </div>
            </div>

            <!-- Expiry -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="expires_at">Expires At</label>
                <input type="datetime-local" class="form-control" id="expires_at" name="expires_at">
                <span class="invalid-feedback" data-field="expires_at"></span>
              </div>
            </div>

            <!-- Comment -->
            <div class="col-md-12">
              <div class="form-group">
                <label for="comment">Comment</label>
                <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Optional notes..."></textarea>
                <span class="invalid-feedback" data-field="comment"></span>
              </div>
            </div>
          </div>

          <div class="mt-3 d-flex">
            <button type="submit" id="btn-submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Save User
            </button>
            <a href="{{ route('admin.hotspot.user.index') }}" class="btn btn-default ml-2">
              <i class="fas fa-list"></i> Back to List
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
    const $form = $('#userForm');
    const $submit = $('#btn-submit');
    const $router = $('#router_id');
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

    /*------ Load profiles by selected router------*/
    function loadProfiles(routerId, selectedId){
        $profile.prop('disabled', true).html('<option value="">Loading...</option>');
        if(!routerId){
            $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', true);
            return;
        }
        let url = "{{ route('admin.hotspot.profile.get_profile', ':id') }}";
        url = url.replace(':id', routerId);
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            headers: {'Accept': 'application/json'},
            success: function(res){
                let opts = '<option value="">-- Select Profile --</option>';
                if(res && res.success){
                    (res.profiles || []).forEach(function(p){
                        const sel = (selectedId && Number(selectedId) === Number(p.id)) ? 'selected' : '';
                        opts += '<option value="'+p.id+'" '+sel+'>'+p.name+' ('+p.mikrotik_profile+')</option>';
                    });
                }
                $profile.html(opts).prop('disabled', false);
            },
            error: function(){
                $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', false);
                toastr.error('Could not load profiles for router.');
            }
        });
    }

    $router.on('change', function(){
        loadProfiles($(this).val(), null);
    });

    /*-----Password helpers------**/
    $('#btn-generate').on('click', function(){
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#';
        let s = '';
        for(let i=0;i<10;i++){ s += chars.charAt(Math.floor(Math.random()*chars.length)); }
        $('#password').val(s);
    });
    $('#btn-showhide').on('click', function(){
        const $btn = $(this), $pw = $('#password');
        const state = $btn.data('state');
        if(state === 'show'){
            $pw.attr('type','text');
            $btn.data('state','hide').find('i').removeClass('fa-eye').addClass('fa-eye-slash');
        }else{
            $pw.attr('type','password');
            $btn.data('state','show').find('i').removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    $('#btn-copy').on('click', function(){
        const $pw = $('#password');
        $pw[0].select(); $pw[0].setSelectionRange(0, 99999);
        document.execCommand('copy');
        toastr.success('Password copied to clipboard');
    });

    /*------Submit AJAX-------*/
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
                    toastr.success(res.message || 'Added Successfully');
                    setTimeout(function(){
                        window.location.href = $form.data('redirect');
                    }, 900);
                }else{
                    toastr.warning('Unexpected response.');
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    showErrors((xhr.responseJSON||{}).errors || {});
                    toastr.error('Please fix the highlighted fields.');
                }else{
                    toastr.error('Something went wrong. Please try again.');
                }
            },
            complete: function(){
                $submit.prop('disabled', false).html('<i class="fas fa-save"></i> Save User');
            }
        });
    });
});
</script>
@endsection
