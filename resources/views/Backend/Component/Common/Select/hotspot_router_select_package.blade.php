 <div class="col-md-4">
     <div class="form-group">
         <label>Router <span class="text-danger">*</span></label>
         <select name="router_id" id="router_id" class="form-control">
             <option value="">-- Select Router --</option>

             @php
                $branch_user_id = Auth::guard('admin')->user()->pop_id ?? null;
                if (empty($pop_id)) {
                    $pop_id = $branch_user_id;
                }
                if ($branch_user_id != null) {
                    $pops = App\Models\Router::where('status', 'active')->where('id', $branch_user_id)->get();
                } else {
                    $pops = App\Models\Router::where('status', 'active')->latest()->get();
                }
            @endphp
            @foreach ($pops as $item)
                <option value="{{ $item->id }}" @if ($item->id == $pop_id) selected @endif>
                    {{ $item->name }}</option>
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
 <script type="text/javascript">
     $(document).ready(function() {
         const $router = $('#router_id'), $profile = $('#hotspot_profile_id');
         function loadProfiles(routerId) {
             $profile.prop('disabled', true).html('<option>Loading...</option>');
             if (!routerId) {
                 $profile.html('<option value="">-- Select Profile --</option>').prop('disabled', true);
                 return;
             }
             let url = "{{ route('admin.hotspot.profile.get_profile', ':id') }}";
             url = url.replace(':id', routerId);
             $.ajax({
                 url: url,
                 type: 'GET',
                 dataType: 'json',
                 headers: {
                     'Accept': 'application/json'
                 },
                 success: function(res) {
                     let opts = '<option value="">-- Select Profile --</option>';
                     if (res && res.success) {
                         (res.profiles || []).forEach(function(p) {
                             opts += '<option value="' + p.id + '">' + p.name + ' (' + p
                                 .mikrotik_profile + ')</option>';
                         });
                     }
                     $profile.html(opts).prop('disabled', false);
                 },
                 error: function() {
                     $profile.html('<option value="">-- Select Profile --</option>').prop('disabled',
                         false);
                     toastr.error('Could not load profiles.');
                 }
             });
         }
         $router.on('change', function() {
             loadProfiles($(this).val());
         });
     });
 </script>
