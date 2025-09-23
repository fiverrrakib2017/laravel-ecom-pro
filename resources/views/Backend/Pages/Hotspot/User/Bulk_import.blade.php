@extends('Backend.Layout.App')
@section('title', 'Bulk Import (CSV) | Admin Panel')

@section('content')
<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            @include('Backend.Component.Common.card-header', [
                'title' => 'Bulk Import (CSV)',
                'description' => 'Upload CSV: username,password,mac_lock,status,expires_at,comment',
                'icon' => '<i class="fas fa-wifi"></i>',
                   'button' => '<button type="button" onclick="window.location=\''.route('admin.hotspot.user.index').'\'" class="btn btn-header">
                    <i class="fas fa-user-plus"></i> User List
                </button>'
            ])

            <div class="card-body">
                <form id="importForm" action="{{ route('admin.hotspot.user.bulk.import.store') }}" method="post" enctype="multipart/form-data">
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
                                <small class="text-muted d-block">Active profiles for selected router.</small>
                                <span class="invalid-feedback" data-field="hotspot_profile_id"></span>
                            </div>
                        </div>

                        <!-- CSV -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CSV File <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="csv_file" name="csv_file" accept=".csv,text/csv,text/plain">
                                    <label class="custom-file-label" for="csv_file">Choose CSV…</label>
                                </div>
                                <small class="text-muted">Max 10MB. First row can be header.</small>
                                <span class="invalid-feedback" data-field="csv_file"></span>
                            </div>
                        </div>

                        <!-- CSV Options -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Delimiter</label>
                                <select name="delimiter" class="form-control">
                                    <option value="auto" selected>Auto-detect</option>
                                    <option value=",">Comma (,)</option>
                                    <option value=";">Semicolon (;)</option>
                                    <option value="\t">Tab</option>
                                </select>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="has_header" name="has_header" value="1" checked>
                                <label class="custom-control-label" for="has_header">CSV has header</label>
                            </div>
                        </div>

                        <!-- Overrides -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status (override)</label>
                                <select name="status_override" class="form-control">
                                    <option value="">— Use CSV / default —</option>
                                    @foreach(['active','disabled','expired','blocked'] as $s)
                                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" data-field="status_override"></span>
                            </div>
                            <div class="form-group">
                                <label>Expires Default</label>
                                <input type="datetime-local" name="expires_default" class="form-control">
                                <span class="invalid-feedback" data-field="expires_default"></span>
                            </div>
                        </div>

                        <!-- Password fallback -->
                        <div class="col-md-3">
                            <label>Password Fallback <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="password_fallback" id="pf_none" value="none" checked>
                                <label class="custom-control-label" for="pf_none">None (password required in CSV)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="password_fallback" id="pf_username" value="username">
                                <label class="custom-control-label" for="pf_username">Use username when missing</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="password_fallback" id="pf_fixed" value="fixed">
                                <label class="custom-control-label" for="pf_fixed">Use fixed when missing</label>
                            </div>
                            <div class="form-group mt-2 pf pf-fixed" style="display:none;">
                                <input type="text" name="fixed_password" class="form-control" placeholder="Fixed password (min 4)">
                                <span class="invalid-feedback" data-field="fixed_password"></span>
                            </div>
                        </div>

                        <!-- Comment default -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Comment (default)</label>
                                <input type="text" name="comment_default" class="form-control" placeholder="Optional note for vouchers">
                                <span class="invalid-feedback" data-field="comment_default"></span>
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-template">
                                        <i class="fas fa-download"></i> CSV Template
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 d-flex">
                        <button type="button" id="btn-preview" class="btn btn-info mr-2">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <button type="button" id="btn-import" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import Now
                        </button>
                        <a href="{{ route('admin.hotspot.user.index') }}" class="btn btn-danger ml-2">
                            <i class="fas fa-list"></i> Back to List
                        </a>
                    </div>
                </form>

                <!-- Results -->
                <div id="result-wrap" class="mt-4">
                    <div class="alert alert-info mb-2" id="result-summary"></div>
                    <div class="d-flex mb-2">
                        <button class="btn btn-sm btn-success mr-2" id="btn-dl-csv"><i class="fas fa-file-csv"></i> Download Created (CSV)</button>
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
    const $form     = $('#importForm');
    const $router   = $('#router_id');
    const $profile  = $('#hotspot_profile_id');
    const $file     = $('#csv_file');
    const $btnPrev  = $('#btn-preview');
    const $btnImp   = $('#btn-import');

    // show chosen filename
    $file.on('change', function(){
        const name = this.files && this.files.length ? this.files[0].name : 'Choose CSV…';
        $(this).siblings('.custom-file-label').text(name);
    });

    // toggle fixed password field
    $('input[name="password_fallback"]').on('change', function(){
        const val = $('input[name="password_fallback"]:checked').val();
        $('.pf').hide();
        if(val === 'fixed') $('.pf-fixed').show();
    }).trigger('change');

    // dependent profile load
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

    // CSV Template
    $('#btn-template').on('click', function(){
        const header = 'username,password,mac_lock,status,expires_at,comment\r\n';
        const sample = [
            'john,123456,AA:BB:CC:DD:EE:FF,active,2025-12-31 23:59,First voucher',
            'mike,, ,,,', // password empty → fallback rules
        ].join('\r\n');
        downloadText('hotspot_users_template.csv', header + sample);
    });

    // Preview & Import
    $btnPrev.on('click', function(){ submitForm(1); });  // dry_run=1
    $btnImp.on('click',  function(){ submitForm(0); });  // dry_run=0

    function submitForm(dry){
        clearErrors();
        $('#result-wrap').hide();
        const btn = dry ? $btnPrev : $btnImp;
        const btnTxt = dry ? '<i class="fas fa-spinner fa-spin"></i> Previewing...' : '<i class="fas fa-spinner fa-spin"></i> Importing...';
        const btnOrig = btn.html();
        btn.prop('disabled', true).html(btnTxt);

        const fd = new FormData($form[0]);
        fd.set('dry_run', String(dry));

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {'Accept':'application/json'},
            success: function(res){
                if(res && res.success){
                    toastr.success(res.message || (dry ? 'Preview ready' : 'Import completed'));
                    renderResult(res);
                } else {
                    toastr.warning('Unexpected response.');
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    showErrors((xhr.responseJSON||{}).errors || {});
                    toastr.error('Please fix the highlighted fields.');
                } else {
                    toastr.error('Something went wrong.');
                }
            },
            complete: function(){
                btn.prop('disabled', false).html(btnOrig);
            }
        });
    }

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

    function renderResult(res){
        $('#result-summary').text(res.message || '');
        const $tb = $('#result-table tbody'); $tb.empty();
        (res.created || []).forEach(function(row, idx){
            $tb.append('<tr><td>'+(idx+1)+'</td><td class="text-mono">'+escapeHtml(row.username)+'</td><td class="text-mono">'+escapeHtml(row.password)+'</td></tr>');
        });

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

    $('#btn-dl-csv').on('click', function(){
        const rows = [['username','password']];
        $('#result-table tbody tr').each(function(){
            const u = $(this).find('td').eq(1).text();
            const p = $(this).find('td').eq(2).text();
            rows.push([u,p]);
        });
        if(rows.length<=1){ toastr.info('Nothing to download'); return; }
        const csv = rows.map(r => r.map(s => `"${(s||'').replace(/"/g,'""')}"`).join(',')).join('\r\n');
        downloadText('created_users.csv', csv);
    });

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

    function downloadText(filename, text){
        const blob = new Blob([text], {type: 'text/csv;charset=utf-8;'});
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href = url; a.download = filename;
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
});
</script>
@endsection
