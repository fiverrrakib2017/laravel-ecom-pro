@extends('Backend.Layout.App')
@section('title','Generate Vouchers Batch | Admin Panel')

@section('style')
<style>
.card-header-pro{position:relative;overflow:hidden;padding:16px 20px;border:0;color:#fff;background:linear-gradient(135deg,#17a2b8 0%,#0ea5e9 45%,#2563eb 100%);}
.card-header-pro::after{content:"";position:absolute;right:-30px;top:-30px;width:180px;height:180px;background:radial-gradient(circle at 30% 30%,rgba(255,255,255,.35),rgba(255,255,255,0) 60%);transform:rotate(25deg);opacity:.7;}
.invalid-feedback{display:block}
.small-muted{font-size:.85rem;color:#6c757d}
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-pro d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <span class="mr-2"><i class="fas fa-ticket-alt"></i></span>
          <div>
            <h4 class="m-0">Generate Batch</h4>
            <small class="text-white-50">Create voucher codes like Mikhmon</small>
          </div>
        </div>
        <div class="d-none d-md-block">
          <a href="{{ route('admin.hotspot.vouchers.batch.index') }}" class="btn btn-header btn-sm text-white"><i class="fas fa-list"></i> All Batches</a>
        </div>
      </div>

      <div class="card-body">
        <form id="batchForm" action="{{ route('admin.hotspot.vouchers.batch.store') }}" method="post" data-redirect="{{ route('admin.hotspot.vouchers.batch.index') }}">
          @csrf
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Router <span class="text-danger">*</span></label>
                <select name="router_id" id="router_id" class="form-control">
                  <option value="">-- Select Router --</option>
                  @foreach($routers as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                  @endforeach
                </select>
                <span class="invalid-feedback" data-field="router_id"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Hotspot Profile <span class="text-danger">*</span></label>
                <select name="hotspot_profile_id" id="hotspot_profile_id" class="form-control" disabled>
                  <option value="">-- Select Profile --</option>
                </select>
                <span class="invalid-feedback" data-field="hotspot_profile_id"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Batch Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Eid Offer 1GB 2H">
                <span class="invalid-feedback" data-field="name"></span>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Quantity <span class="text-danger">*</span></label>
                <input type="number" name="qty" class="form-control" value="50" min="1" max="2000">
                <span class="invalid-feedback" data-field="qty"></span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Code Prefix</label>
                <input type="text" name="code_prefix" class="form-control" placeholder="e.g. EID">
                <small class="small-muted">Optional (max 10)</small>
                <span class="invalid-feedback" data-field="code_prefix"></span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Username Length <span class="text-danger">*</span></label>
                <input type="number" name="username_length" class="form-control" value="8" min="4" max="16">
                <span class="invalid-feedback" data-field="username_length"></span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Password Length <span class="text-danger">*</span></label>
                <input type="number" name="password_length" class="form-control" value="6" min="4" max="16">
                <span class="invalid-feedback" data-field="password_length"></span>
                <div class="custom-control custom-checkbox mt-1">
                  <input type="checkbox" class="custom-control-input" id="pw_same" name="password_same_as_username" value="1">
                  <label class="custom-control-label" for="pw_same">Password = Username</label>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Validity (days override)</label>
                <input type="number" name="validity_days_override" class="form-control" min="1" max="3650">
                <span class="invalid-feedback" data-field="validity_days_override"></span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Batch Expires At</label>
                <input type="datetime-local" name="expires_at" class="form-control">
                <span class="invalid-feedback" data-field="expires_at"></span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Price </label>
                <input type="number" name="price_minor" class="form-control" value="0" min="0">
                <small class="small-muted">৳100.00 => 10000</small>
                <span class="invalid-feedback" data-field="price_minor"></span>
              </div>
            </div>
          </div>

          <div class="mt-2 d-flex">
            <button type="submit" id="btn-submit" class="btn btn-primary"><i class="fas fa-bolt"></i> Generate</button>
            <a href="{{ route('admin.hotspot.vouchers.batch.index') }}" class="btn btn-default ml-2"><i class="fas fa-list"></i> All Batches</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
$(function(){
  const $form = $('#batchForm'), $submit = $('#btn-submit');
  const $router = $('#router_id'), $profile = $('#hotspot_profile_id');

  function clearErrors(){ $form.find('.is-invalid').removeClass('is-invalid'); $form.find('.invalid-feedback').text(''); }
  function showErrors(errors){ Object.keys(errors).forEach(function(f){ const $el=$form.find('[name="'+f+'"]'); if($el.length){$el.addClass('is-invalid');} $form.find('.invalid-feedback[data-field="'+f+'"]').text((errors[f]||[])[0]||''); }); }

  function loadProfiles(routerId){
    $profile.prop('disabled', true).html('<option>Loading...</option>');
    if(!routerId){ $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', true); return; }
    let url = "{{ route('admin.hotspot.profile.get_profile', ':id') }}";
    url = url.replace(':id', routerId);
    $.ajax({
      url:url,
      type: 'GET', dataType:'json', headers:{'Accept':'application/json'},
      success: function(res){
        let opts = '<option value="">-- Select Profile --</option>';
        if(res && res.success){ (res.profiles||[]).forEach(function(p){ opts += '<option value="'+p.id+'">'+p.name+' ('+p.mikrotik_profile+')</option>'; }); }
        $profile.html(opts).prop('disabled', false);
      },
      error: function(){ $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', false); toastr.error('Could not load profiles.'); }
    });
  }
  $router.on('change', function(){ loadProfiles($(this).val()); });

  $form.on('submit', function(e){
    e.preventDefault(); clearErrors();
    $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
    $.ajax({
      url: $form.attr('action'), type:'POST', data:$form.serialize(), dataType:'json', headers:{'Accept':'application/json'},
      success: function(res){
        if(res && res.success){
          toastr.success(res.message || 'Batch generated');
          // Take user straight to print
          setTimeout(function(){
            window.location.href = "{{ route('admin.hotspot.vouchers.print') }}" + "?batch_id=" + res.batch_id;
          }, 800);
        } else {
          toastr.warning('Unexpected response.');
        }
      },
      error: function(xhr){
        if(xhr.status===422){ showErrors((xhr.responseJSON||{}).errors||{}); toastr.error('Please fix the highlighted fields.'); }
        else { toastr.error('Something went wrong.'); }
      },
      complete: function(){ $submit.prop('disabled', false).html('<i class="fas fa-bolt"></i> Generate'); }
    });
  });
});
</script>
@endsection
