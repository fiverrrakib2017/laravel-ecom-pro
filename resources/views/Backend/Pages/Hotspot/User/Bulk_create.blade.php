@extends('Backend.Layout.App')
@section('title', 'Bulk User Create | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            <!-- Card Header -->
            @include('Backend.Component.Common.card-header', [
                'title' => 'Bulk User Create',
                'description' => 'Generate multiple hotspot users quickly',
                'icon' => '<i class="fas fa-wifi"></i>',
                'button' => '<button type="button" onclick="window.location=\''.route('admin.hotspot.user.index').'\'" class="btn btn-header">
                    <i class="fas fa-user-plus"></i> User List
                </button>'

            ])

            <div class="card-body">
                <form id="bulkForm" action="{{ route('admin.hotspot.user.bulk.store') }}" method="post">
                    @csrf

                    <div class="row">
                        <!-- Router -->
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

                        <!-- Profile -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hotspot Profile <span class="text-danger">*</span></label>
                                <select name="hotspot_profile_id" id="hotspot_profile_id" class="form-control" disabled>
                                    <option value="">-- Select Profile --</option>
                                </select>
                                <small class="text-muted">Active profiles for selected router.</small>
                                <span class="invalid-feedback" data-field="hotspot_profile_id"></span>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control">
                                    @foreach(['active','disabled','expired','blocked'] as $s)
                                        <option value="{{ $s }}" {{ $s==='active'?'selected':'' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" data-field="status"></span>
                            </div>
                        </div>

                        <!-- Expiry + Comment -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Expires At</label>
                                <input type="datetime-local" name="expires_at" class="form-control">
                                <span class="invalid-feedback" data-field="expires_at"></span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Comment (applied to all)</label>
                                <input type="text" name="comment" class="form-control" placeholder="Optional note for vouchers">
                                <span class="invalid-feedback" data-field="comment"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Mode -->
                    <fieldset class="border mt-2">
                        <legend class="w-50">Usernames Source</legend>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="mode" id="mode_pattern" value="pattern" checked>
                                    <label class="custom-control-label" for="mode_pattern">Pattern</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="mode" id="mode_list" value="list">
                                    <label class="custom-control-label" for="mode_list">Paste List</label>
                                </div>
                            </div>

                            <!-- Pattern inputs -->
                            <div id="pattern_wrap" class="form-group col-md-12">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Prefix</label>
                                        <input type="text" name="prefix" class="form-control" placeholder="e.g. HS">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Start From <span class="text-danger">*</span></label>
                                        <input type="number" name="start_from" class="form-control" value="1" min="1">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Count <span class="text-danger">*</span></label>
                                        <input type="number" name="count" class="form-control" value="50" min="1" max="1000">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Pad</label>
                                        <input type="number" name="pad" class="form-control" value="3" min="1" max="10">
                                    </div>
                                </div>
                                <small class="text-muted">Example: Prefix=HS, Start=1, Pad=3 → HS001, HS002, HS003…</small>
                            </div>

                            <!-- List inputs -->
                            <div id="list_wrap" class="form-group col-md-12" style="display:none">
                                <label>Usernames (one per line)</label>
                                <textarea name="usernames_text" class="form-control text-mono" rows="7" placeholder="user001
user002
user003"></textarea>
                                <span class="invalid-feedback" data-field="usernames_text"></span>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Password options -->
                    <fieldset class="border mt-3">
                        <legend class="w-50">Password Options</legend>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="password_mode" id="pm_same" value="same" checked>
                                    <label class="custom-control-label" for="pm_same">Same as Username</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="password_mode" id="pm_fixed" value="fixed">
                                    <label class="custom-control-label" for="pm_fixed">Fixed</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="password_mode" id="pm_random" value="random">
                                    <label class="custom-control-label" for="pm_random">Random</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="password_mode" id="pm_list" value="list">
                                    <label class="custom-control-label" for="pm_list">Passwords List</label>
                                </div>
                            </div>

                            <div class="form-group col-md-4 pm pm-fixed" style="display:none">
                                <label>Fixed Password</label>
                                <input type="text" name="fixed_password" class="form-control" placeholder="e.g. 12345678">
                            </div>
                            <div class="form-group col-md-4 pm pm-random" style="display:none">
                                <label>Random Length</label>
                                <input type="number" name="password_length" class="form-control" value="8" min="4" max="32">
                            </div>
                            <div class="form-group col-md-12 pm pm-list" style="display:none">
                                <label>Passwords (one per line, matches usernames)</label>
                                <textarea name="passwords_text" class="form-control text-mono" rows="5" placeholder="pass001
pass002
pass003"></textarea>
                                <span class="invalid-feedback" data-field="passwords_text"></span>
                            </div>
                        </div>
                    </fieldset>

                    <div class="mt-3 d-flex">
                        <button type="submit" id="btn-submit" class="btn btn-primary">
                            <i class="fas fa-bolt"></i> Generate & Save
                        </button>
                        <a href="{{ route('admin.hotspot.user.index') }}" class="btn btn-default ml-2">
                            <i class="fas fa-list"></i> Back to List
                        </a>
                    </div>
                </form>

                <!-- Results -->
                <div id="result-wrap" class="mt-4">
                    <div class="alert alert-info mb-2" id="result-summary"></div>
                    <div class="d-flex mb-2">
                        <button class="btn btn-sm btn-success mr-2" id="btn-dl-csv"><i class="fas fa-file-csv"></i> Download CSV</button>
                        <button class="btn btn-sm btn-secondary" id="btn-copy-table"><i class="fas fa-copy"></i> Copy Table</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0" id="result-table">
                            <thead class="thead-light">
                                <tr><th>#</th><th>Username</th><th>Password</th></tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="mt-3" id="skipped-wrap" style="display:none">
                        <h6>Skipped</h6>
                        <ul id="skipped-list" class="small mb-0"></ul>
                    </div>
                </div>

            </div><!--/card-body-->
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function(){
    const $form = $('#bulkForm');
    const $submit = $('#btn-submit');
    const $router = $('#router_id');
    const $profile = $('#hotspot_profile_id');

    function clearErrors(){
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }
    function showErrors(errors){
        Object.keys(errors).forEach(function(field){
            const $el = $form.find('[name="'+field+'"]');
            if($el.length){ $el.addClass('is-invalid'); }
            $form.find('.invalid-feedback[data-field="'+field+'"]').text(
                Array.isArray(errors[field]) ? errors[field][0] : errors[field]
            );
        });
    }

    /*----------------Mode toggle--------------*/ 
    function toggleMode(){
        const mode = $('input[name="mode"]:checked').val();
        $('#pattern_wrap').toggle(mode==='pattern');
        $('#list_wrap').toggle(mode==='list');
    }
    $('input[name="mode"]').on('change', toggleMode); toggleMode();

    /*----------Password sections----------*/ 
    function togglePw(){
        const val = $('input[name="password_mode"]:checked').val();
        $('.pm').hide();
        $('.pm-' + val).show();
    }
    $('input[name="password_mode"]').on('change', togglePw); togglePw();

    /*-------------- Dependent profiles----------------*/
    function loadProfiles(routerId){
        $profile.prop('disabled', true).html('<option>Loading...</option>');
        if(!routerId){
            $profile.prop('disabled', true).html('<option value="">-- Select Profile --</option>');
            return;
        }
        let url = "{{ route('admin.hotspot.profile.get_profile', ':id') }}";
        url = url.replace(':id', routerId);
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            headers: {'Accept':'application/json'},
            success: function(res){
                let opts = '<option value="">-- Select Profile --</option>';
                if(res && res.success){
                    (res.profiles || []).forEach(function(p){
                        opts += '<option value="'+p.id+'">'+p.name+' ('+p.mikrotik_profile+')</option>';
                    });
                }
                $profile.html(opts).prop('disabled', false);
            },
            error: function(){
                $profile.prop('disabled', false).html('<option value="">-- Select Profile --</option>');
                toastr.error('Could not load profiles.');
            }
        });
    }
    $router.on('change', function(){ loadProfiles($(this).val()); });

    /*------------Submit AJAX---------------*/ 
    $form.on('submit', function(e){
        e.preventDefault();
        clearErrors();
        $('#result-wrap').hide();
        $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Working...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            headers: {'Accept':'application/json'},
            success: function(res){
                if(res && res.success){
                    toastr.success(res.message || 'Bulk created');
                    renderResult(res);
                } else {
                    toastr.warning('Unexpected response.');
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    const resp = xhr.responseJSON || {};
                    showErrors(resp.errors || {});
                    toastr.error('Please fix the highlighted fields.');
                }else{
                    toastr.error('Something went wrong.');
                }
            },
            complete: function(){
                $submit.prop('disabled', false).html('<i class="fas fa-bolt"></i> Generate & Save');
            }
        });
    });

    function renderResult(res){
        /*--------summary------*/ 
        $('#result-summary').text(res.message || '');
        /*------------created table--------------*/ 
        const $tb = $('#result-table tbody'); $tb.empty();
        (res.created || []).forEach(function(row, idx){
            $tb.append('<tr><td>'+(idx+1)+'</td><td class="text-mono">'+escapeHtml(row.username)+'</td><td class="text-mono">'+escapeHtml(row.password)+'</td></tr>');
        });
        // skipped
        const $sk = $('#skipped-list'); $sk.empty();
        if((res.skipped||[]).length){
            $('#skipped-wrap').show();
            res.skipped.forEach(function(s){
                $sk.append('<li><span class="text-mono">'+escapeHtml(s.username)+'</span> — '+escapeHtml(s.reason)+'</li>');
            });
        } else {
            $('#skipped-wrap').hide();
        }
        $('#result-wrap').show();
    }

    /*-----CSV download------*/ 
    $('#btn-dl-csv').on('click', function(){
        const rows = [['username','password']];
        $('#result-table tbody tr').each(function(){
            const u = $(this).find('td').eq(1).text();
            const p = $(this).find('td').eq(2).text();
            rows.push([u,p]);
        });
        if(rows.length<=1){ toastr.info('Nothing to download'); return; }
        const csv = rows.map(r => r.map(s => `"${(s||'').replace(/"/g,'""')}"`).join(',')).join('\r\n');
        const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = 'hotspot_users.csv';
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });

    /***-----------Copy table-----------***/ 
    $('#btn-copy-table').on('click', function(){
        const lines = [];
        $('#result-table tbody tr').each(function(){
            const tds = $(this).find('td');
            lines.push(tds.eq(1).text() + '\t' + tds.eq(2).text());
        });
        if(!lines.length){ toastr.info('Nothing to copy'); return; }
        const ta = document.createElement('textarea');
        ta.value = lines.join('\n');
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        toastr.success('Copied to clipboard');
    });

    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
});
</script>
@endsection
