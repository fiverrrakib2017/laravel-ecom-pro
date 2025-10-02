@extends('Backend.Layout.App')
@section('title','Dashboard | SMS Management | Admin Panel')

{{-- ========== STYLES ========== --}}
@section('style')
<style>
/* ---------- Layout polish ---------- */
.action-bar {
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem;
}
.action-right { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
.action-right .input-group.input-group-sm { width: 260px; }
@media (max-width: 767.98px){
  .action-bar { flex-direction: column; align-items: stretch; }
  .action-right { flex-direction: column; align-items: stretch; }
  .action-right .input-group.input-group-sm { width: 100%; }
  .btn-toolbar-fluid > * { width: 100%; }
}

/* Placeholder button row responsive */
.placeholder-row { display: flex; flex-wrap: wrap; gap: .4rem; }
.placeholder-row .btn { margin: 0; }

/* ---------- iPhone modal mock ---------- */
.iphone-wrap{ display:inline-block; }
.iphone-frame{
  width: 360px; max-width: 100%;
  border-radius:36px; background:#0f0f10;
  padding:12px 10px 16px; position:relative;
  box-shadow:0 12px 40px rgba(0,0,0,.25);
}
.iphone-frame.light{ background:#eaecef; }
.iphone-notch{
  width:155px; height:26px; background:#111;
  border-radius:0 0 18px 18px; position:absolute; top:0; left:50%; transform:translateX(-50%);
}
.iphone-frame.light .iphone-notch{ background:#cfd6dc; }
.iphone-screen{
  background:#0f1113; border-radius:28px; height:600px; overflow:hidden;
  border:1px solid rgba(255,255,255,.06);
}
.iphone-frame.light .iphone-screen{ background:#ffffff; border:1px solid rgba(0,0,0,.06); }
.ios-header{
  display:flex; justify-content:space-between; align-items:center;
  padding:14px; border-bottom:1px solid rgba(255,255,255,.08); color:#e8eaed; font-size:13px;
}
.iphone-frame.light .ios-header{ color:#1e2124; border-color:rgba(0,0,0,.08); }
.ios-sender{ font-weight:700; letter-spacing:.2px; }
.ios-chat{ height:calc(600px - 48px); overflow:auto; padding:16px; display:flex; flex-direction:column; gap:10px; }
.ios-bubble{ max-width:86%; padding:10px 12px; border-radius:16px; line-height:1.45; font-size:14px; white-space:pre-wrap; word-wrap:break-word; }
.incoming{ background:#1e2327; color:#eaecef; border-top-left-radius:6px; }
.iphone-frame.light .incoming{ background:#eef1f4; color:#0f1113; }

/* iPhone width breakpoints for tight screens */
@media (max-width: 575.98px){
  .iphone-frame{ width: 320px; }
  .ios-chat{ height: 520px; }
}
@media (max-width: 380px){
  .iphone-frame{ width: 290px; }
  .ios-chat{ height: 480px; }
}

/* Minor polish */
.badge { vertical-align: middle; }
.form-group label { font-weight: 600; }
</style>
@endsection

{{-- ========== CONTENT ========== --}}
@section('content')
<div class="row">
  <div class="col-lg-11 col-xl-10 mx-auto">
    <div class="card">
        <!-- Card Header -->
            @include('Backend.Component.Common.card-header', [
                'title' => 'SMS Configuration',
                'description' => 'All users with status, usage & expiry details',
                'icon' => '<i class="fas fa-sms"></i>',
            ])
      <div class="card-body">

        {{-- Tabs --}}
        <ul class="nav nav-tabs" id="smsTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="gateway-tab" data-toggle="tab" href="#gateway" role="tab">Gateway Settings</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="templates-tab" data-toggle="tab" href="#templates" role="tab">SMS Templates</a>
          </li>
        </ul>

        <div class="tab-content pt-3" id="smsTabContent">

          {{-- ===== TAB: Gateway Settings ===== --}}
          <div class="tab-pane fade show active" id="gateway" role="tabpanel" aria-labelledby="gateway-tab">
            <form action="{{ route('admin.sms.config.store') }}" method="POST" id="addSmsForm">
              @csrf
              <div class="form-group row">
                <label class="col-md-3 col-form-label">API URL</label>
                <div class="col-md-9">
                  <input type="text" name="api_url" class="form-control" placeholder="Enter Api Url" value="{{ $data->api_url ?? '' }}">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 col-form-label">API Key</label>
                <div class="col-md-9">
                  <input type="text" name="api_key" class="form-control" placeholder="Enter Api Key" value="{{ $data->api_key ?? '' }}">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Sender ID</label>
                <div class="col-md-9">
                  <input type="text" name="sender_id" class="form-control" placeholder="Enter Sender Id" value="{{ $data->sender_id ?? '' }}">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Country Code</label>
                <div class="col-md-9">
                  <select name="default_country_code" class="form-control">
                    @php
                      $selectedCountry = $data->default_country_code ?? '';
                      $countries = ["+88"=>"Bangladesh (+88)","+91"=>"India (+91)","+61"=>"Australia (+61)"];
                    @endphp
                    <option value="">---Select---</option>
                    @foreach($countries as $code=>$name)
                      <option value="{{ $code }}" {{ $selectedCountry==$code ? 'selected':'' }}>{{ $name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group mb-0">
                <button type="submit" class="btn btn-success btn-toolbar-fluid">Save Changes</button>
              </div>
            </form>
          </div>

          {{-- ===== TAB: SMS Templates ===== --}}
          <div class="tab-pane fade" id="templates" role="tabpanel" aria-labelledby="templates-tab">
            {{-- ---------- Template: Recharge Success ---------- --}}
            <form action="{{route('admin.sms.auto.template.store')}}" method="POST" id="tplRechargeForm" class="mb-4">
              @csrf
              <input type="hidden" name="key" value="recharge_success">

              <div class="card">
                <div class="card-header">
                  <div class="action-bar">
                    <div>
                      <strong>Recharge Success (Customer)</strong>
                      <span class="badge badge-primary ml-2">Key: recharge_success</span>
                    </div>
                    <div class="action-right">
                      <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Test Mobile (e.g. 017...)" id="test_mobile_recharge">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-outline-secondary" onclick="sendTest('recharge_success','#test_mobile_recharge','r')">Send Test</button>
                        </div>
                      </div>
                      <button type="button" class="btn btn-sm btn-dark"
                        onclick="openIphonePreview({ key:'recharge_success', textarea:'#tplRechargeForm textarea[name=body]', varsSelector:'.sms-var-r', sender:'{{ $data->sender_id ?? 'YourBrand' }}' })">
                          Preview on Phone
                      </button>
                      <button type="submit" class="btn btn-sm btn-primary">Save Template</button>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                    <div class="d-none">
                        <input type="hidden" class="sms-var-r" data-var="id"           value="CUST-1001">
                        <input type="hidden" class="sms-var-r" data-var="username"     value="demo_user">
                        <input type="hidden" class="sms-var-r" data-var="mobile"       value="01700000000">
                        <input type="hidden" class="sms-var-r" data-var="area"         value="Gulshan">
                        <input type="hidden" class="sms-var-r" data-var="package"      value="20Mbps">
                        <input type="hidden" class="sms-var-r" data-var="expire_date"  value="2025-10-31">
                        <input type="hidden" class="sms-var-r" data-var="due"          value="500">
                    </div>
                  <div class="form-group">
                    <label>Template Text</label>
                    <textarea name="body" rows="3" class="form-control"
                      placeholder="Enter Your Template Text">{{ $templates['recharge_success']->body ?? '' }}</textarea>
                  </div>



                  <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="placeholder-row">
                      @foreach (['{id}','{username}','{mobile}','{area}','{package}','{expire_date}','{due}'] as $ph)
                        <button type="button" class="btn btn-outline-primary btn-sm"
                          onclick="insertAtCursor('#tplRechargeForm textarea[name=body]','{{ $ph }}')">{{ $ph }}</button>
                      @endforeach
                    </div>
                    <div class="form-group form-check mb-0 mt-2 mt-md-0">
                      <input type="checkbox" name="is_active" id="recharge_active" class="form-check-input" {{ !empty($templates['recharge_success']->is_active) ? 'checked' : '' }}>
                      <label for="recharge_active" class="form-check-label">Active</label>
                    </div>
                  </div>
                </div>
              </div>
            </form>

            {{-- ---------- Template: POP / Reseller Recharge ---------- --}}
            <form action="{{route('admin.sms.auto.template.store')}}" method="POST" id="tplPopForm" class="mb-4">
              @csrf
              <input type="hidden" name="key" value="pop_recharge">

              <div class="card">
                <div class="card-header">
                  <div class="action-bar">
                    <div>
                      <strong>POP / Reseller Recharge</strong>
                      <span class="badge badge-primary ml-2">Key: pop_recharge</span>
                    </div>
                    <div class="action-right">
                      <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Test Mobile" id="test_mobile_pop">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-outline-secondary" onclick="sendTest('pop_recharge','#test_mobile_pop','p')">Send Test</button>
                        </div>
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-dark"
                        onclick="openIphonePreview({ key:'pop_recharge', textarea:'#tplPopForm textarea[name=body]', varsSelector:'.sms-var-p', sender:'{{ $data->sender_id ?? 'YourBrand' }}' })">
                         Preview on Phone
                      </button>
                      <button type="submit" class="btn btn-sm btn-primary">Save Template</button>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                    <div class="d-none">
                        <input type="hidden" class="sms-var-r" data-var="id"           value="CUST-1001">
                        <input type="hidden" class="sms-var-r" data-var="username"     value="demo_user">
                        <input type="hidden" class="sms-var-r" data-var="mobile"       value="01700000000">
                        <input type="hidden" class="sms-var-r" data-var="area"         value="Gulshan">
                        <input type="hidden" class="sms-var-r" data-var="package"      value="20Mbps">
                        <input type="hidden" class="sms-var-r" data-var="expire_date"  value="2025-10-31">
                        <input type="hidden" class="sms-var-r" data-var="due"          value="500">
                    </div>
                  <div class="form-group">
                    <label>Template Text</label>
                    <textarea name="body" rows="3" class="form-control"
                      placeholder="Enter Your Template Text">{{ $templates['pop_recharge']->body ?? '' }}</textarea>
                  </div>



                  <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="placeholder-row">
                       @foreach (['{id}','{username}','{mobile}','{area}','{package}','{expire_date}','{due}'] as $ph)
                        <button type="button" class="btn btn-outline-primary btn-sm"
                          onclick="insertAtCursor('#tplPopForm textarea[name=body]','{{ $ph }}')">{{ $ph }}</button>
                      @endforeach
                    </div>
                    <div class="form-group form-check mb-0 mt-2 mt-md-0">
                      <input type="checkbox" name="is_active" id="pop_active" class="form-check-input" {{ !empty($templates['pop_recharge']->is_active) ? 'checked' : '' }}>
                      <label for="pop_active" class="form-check-label">Active</label>
                    </div>
                  </div>
                </div>
              </div>
            </form>

            {{-- ---------- Template: Bill Due Reminder ---------- --}}
            <form action="{{route('admin.sms.auto.template.store')}}" method="POST" id="tplDueForm">
              @csrf
              <input type="hidden" name="key" value="bill_due_reminder">

              <div class="card">
                <div class="card-header">
                  <div class="action-bar">
                    <div>
                      <strong>Bill Due Reminder</strong>
                      <span class="badge badge-primary ml-2">Key: bill_due_reminder</span>
                    </div>
                    <div class="action-right">
                      <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Test Mobile" id="test_mobile_due">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-outline-secondary" onclick="sendTest('bill_due_reminder','#test_mobile_due','d')">Send Test</button>
                        </div>
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-dark"
                        onclick="openIphonePreview({ key:'bill_due_reminder', textarea:'#tplDueForm textarea[name=body]', varsSelector:'.sms-var-d', sender:'{{ $data->sender_id ?? 'YourBrand' }}' })">
                        Preview on Phone
                      </button>
                      <button type="submit" class="btn btn-sm btn-primary">Save Template</button>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                    <div class="d-none">
                        <input type="hidden" class="sms-var-r" data-var="id"           value="CUST-1001">
                        <input type="hidden" class="sms-var-r" data-var="username"     value="demo_user">
                        <input type="hidden" class="sms-var-r" data-var="mobile"       value="01700000000">
                        <input type="hidden" class="sms-var-r" data-var="area"         value="Gulshan">
                        <input type="hidden" class="sms-var-r" data-var="package"      value="20Mbps">
                        <input type="hidden" class="sms-var-r" data-var="expire_date"  value="2025-10-31">
                        <input type="hidden" class="sms-var-r" data-var="due"          value="500">
                    </div>
                  <div class="form-group">
                    <label>Template Text</label>
                    <textarea name="body" rows="3" class="form-control"
                      placeholder="Enter Your Template Text">{{ $templates['bill_due_reminder']->body ?? '' }}</textarea>
                  </div>



                  <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="placeholder-row">
                       @foreach (['{id}','{username}','{mobile}','{area}','{package}','{expire_date}','{due}'] as $ph)
                        <button type="button" class="btn btn-outline-primary btn-sm"
                          onclick="insertAtCursor('#tplDueForm textarea[name=body]','{{ $ph }}')">{{ $ph }}</button>
                      @endforeach
                    </div>
                    <div class="form-group form-check mb-0 mt-2 mt-md-0">
                      <input type="checkbox" name="is_active" id="due_active" class="form-check-input" {{ !empty($templates['bill_due_reminder']->is_active) ? 'checked' : '' }}>
                      <label for="due_active" class="form-check-label">Active</label>
                    </div>
                  </div>
                </div>
              </div>
            </form>

          </div>{{-- /templates --}}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== iPhone Preview Modal (one global modal) ===== --}}
<div class="modal fade" id="iphonePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content border-0">
      <div class="modal-header bg-dark text-white py-2">
        <h6 class="modal-title mb-0"><i class="fab fa-apple mr-1"></i> iPhone Preview</h6>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body text-center">
        <div class="iphone-wrap">
          <div class="iphone-frame">
            <div class="iphone-notch"></div>
            <div class="iphone-screen">
              <div class="ios-header">
                <div class="ios-sender" id="iosSender">YourBrand</div>
                <div class="ios-time" id="iosTime">12:00</div>
              </div>
              <div class="ios-chat">
                <div class="ios-bubble incoming" id="iosMessage">Preview text…</div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-3 small text-muted">
          <span id="iosChars">Chars: 0</span> •
          <span id="iosEnc">Encoding: GSM-7</span> •
          <span id="iosSegs">Segments: 0</span>
        </div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleIphoneTheme()">
          <i class="fas fa-adjust mr-1"></i>Light / Dark
        </button>
        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- ========== SCRIPTS ========== --}}
@section('script')
<script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
<script>
/* Insert placeholder at cursor */
function insertAtCursor(sel, text){
  const el=document.querySelector(sel); if(!el) return;
  const s=el.selectionStart??el.value.length, e=el.selectionEnd??el.value.length;
  el.value=el.value.slice(0,s)+text+el.value.slice(e);
  el.focus(); el.selectionStart=el.selectionEnd=s+text.length;
  el.dispatchEvent(new Event('input',{bubbles:true}));
}

/* GSM7 vs Unicode & segments */
const GSM7_REGEX=/^[\u0000-\u007F€£¥èéùìòÇØøÅåΔ_ΦΓΛΩΠΨΣΘΞ^\{\}\[~\]\|]*$/;
function isGsm7(str){ return GSM7_REGEX.test(str) && !/[^\x00-\x7F]/.test(str); }
function smsMeta(text){
  const gsm=isGsm7(text); const single=gsm?160:70, multi=gsm?153:67; const len=text.length;
  if(!len) return {len:0, enc:gsm?'GSM-7':'Unicode', segs:0};
  if(len<=single) return {len, enc:gsm?'GSM-7':'Unicode', segs:1};
  return {len, enc:gsm?'GSM-7':'Unicode', segs:Math.ceil(len/multi)};
}
function nowHM(){ const d=new Date(); return d.getHours().toString().padStart(2,'0')+':'+d.getMinutes().toString().padStart(2,'0'); }

/* Collect & replace tokens */
function collectVarsBySelector(sel){ const out={}; document.querySelectorAll(sel).forEach(i=>out[i.dataset.var]=i.value); return out; }
function fillTemplate(tpl, vars){ return tpl.replace(/\{(\w+)\}/g,(m,k)=> (k in vars?vars[k]:m)); }

/* iPhone Modal logic */
let iphoneDark=true;
function toggleIphoneTheme(){
  iphoneDark=!iphoneDark;
  const f=document.querySelector('.iphone-frame');
  if(f){ f.classList.toggle('light', !iphoneDark); }
}
function openIphonePreview({key,textarea,varsSelector,sender}){
  const tpl=document.querySelector(textarea)?.value||'';
  const vars=collectVarsBySelector(varsSelector);
  const text=(fillTemplate(tpl,vars).trim()||'Type your message…');
  document.getElementById('iosSender').textContent=sender||'YourBrand';
  document.getElementById('iosTime').textContent=nowHM();
  document.getElementById('iosMessage').textContent=text;
  const m=smsMeta(text);
  document.getElementById('iosChars').textContent=`Chars: ${m.len}`;
  document.getElementById('iosEnc').textContent=`Encoding: ${m.enc}`;
  document.getElementById('iosSegs').textContent=`Segments: ${m.segs}`;
  const f=document.querySelector('.iphone-frame'); if(f){ f.classList.toggle('light', !iphoneDark); }
  $('#iphonePreviewModal').modal('show');
}

/* Send Test (routes already exist) */
function collectScopeVars(scope){
  const map={r:'.sms-var-r', p:'.sms-var-p', d:'.sms-var-d'};
  const sel=map[scope]||'.sms-var-r'; const out={}; document.querySelectorAll(sel).forEach(i=>out[i.dataset.var]=i.value); return out;
}
function sendTest(key,inputSel,scopeKey){
  const mobile=$(inputSel).val(); if(!mobile) return alert('Enter a test mobile number');
  $.post("{{route('admin.sms.send_test_message')}}",{
    _token:"{{ csrf_token() }}", key, mobile, vars: collectScopeVars(scopeKey)
  }).done(res=>alert(res.message||'Sent'))
    .fail(xhr=>alert(xhr.responseJSON?.message||'Failed'));
}

/* Init submit Ajax (if helper exists) */
$(function(){
  if(typeof handle_submit_form==='function'){
    handle_submit_form('#addSmsForm');
    handle_submit_form('#tplRechargeForm');
    handle_submit_form('#tplPopForm');
    handle_submit_form('#tplDueForm');
  }
});
</script>
@endsection
