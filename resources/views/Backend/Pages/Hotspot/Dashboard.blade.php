@extends('Backend.Layout.App')
@section('title', 'Hotspot Dashboard | Admin Panel')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <!-- Left: Title -->
      <div class="col-12 col-lg mb-2 mb-lg-0">
        <div class="d-flex flex-column">
          <h3 class="m-0">Hotspot Dashboard</h3>
          <small class="text-muted">Operations overview • sales-ready demo</small>
        </div>
      </div>

      <!-- Right: Filters + Actions -->
      <div class="col-12 col-lg">
        <div class="form-row justify-content-lg-end">

          <!-- Router select -->
          <div class="col-12 col-sm-6 col-md-5 col-lg-4 mb-2 mb-md-0">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-network-wired"></i></span>
              </div>
              <select id="filter-router" class="form-control">
                <option selected value="all">All Routers</option>
                <option value="mirpur">POP Mirpur</option>
                <option value="dhanmondi">POP Dhanmondi</option>
                <option value="uttara">POP Uttara</option>
              </select>
            </div>
          </div>
          <!-- Buttons -->
          <div class="col-12 col-md-auto mb-2 mb-md-0">
            <a href="#" class="btn btn-primary btn-block">
              <i class="fas fa-ticket-alt mr-1"></i> Generate Vouchers
            </a>
          </div>
          <div class="col-12 col-md-auto">
            <a href="#" id="btn-sync" class="btn btn-outline-secondary btn-block">
              <i class="fas fa-sync-alt mr-1"></i> Sync
            </a>
          </div>
        </div>
      </div>
    </div><!-- /.row -->
  </div>
</div>


<section class="content">
<div class="container-fluid">

  {{-- ======== KPIs ======== --}}
  <div class="row">
    <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="kpi-active">0</h3>
          <p>Today Active Users</p>
          <span class="badge badge-light" id="kpi-active-trend">+0%</span>
        </div>
        <div class="icon"><i class="fas fa-user-friends"></i></div>
        <a href="#" class="small-box-footer">More info</a>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
        <div class="inner">
          <h3 id="kpi-online">0</h3>
          <p>Online Now</p>
          <span class="badge badge-light" id="kpi-online-trend">+0%</span>
        </div>
        <div class="icon"><i class="fas fa-wifi"></i></div>
        <a href="#" class="small-box-footer">Active sessions</a>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3 id="kpi-usage">0</h3>
          <p>Usage Today (GB)</p>
          <span class="badge badge-light" id="kpi-usage-trend">+0%</span>
        </div>
        <div class="icon"><i class="fas fa-download"></i></div>
        <a href="#" class="small-box-footer">Traffic details</a>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3 id="kpi-voucher">0</h3>
          <p>New Vouchers</p>
          <span class="badge badge-light" id="kpi-voucher-trend">+0%</span>
        </div>
        <div class="icon"><i class="fas fa-ticket-alt"></i></div>
        <a href="#" class="small-box-footer">Batches today</a>
      </div>
    </div>
  </div>

  {{-- ======== Row: Tabbed Charts + Voucher Donut ======== --}}
  <div class="row mb-3">
    <div class="col-lg-6">
      <div class="card card-outline card-primary">
        <div class="card-header p-2 d-flex align-items-center justify-content-between">
          <ul class="nav nav-pills nav-pills-soft" id="chartTabs">
            <li class="nav-item"><a class="nav-link active" href="#tab-logins" data-toggle="tab"><i class="fas fa-sign-in-alt mr-1"></i>Logins</a></li>
            <li class="nav-item"><a class="nav-link" href="#tab-usage" data-toggle="tab"><i class="fas fa-chart-line mr-1"></i>Usage</a></li>
            <li class="nav-item"><a class="nav-link" href="#tab-vouchers" data-toggle="tab"><i class="fas fa-ticket-alt mr-1"></i>Vouchers</a></li>
          </ul>
          <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary active" data-range="7">7d</button>
            <button class="btn btn-outline-secondary" data-range="30">30d</button>
            <button class="btn btn-outline-secondary" data-range="90">90d</button>
          </div>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <div class="chart tab-pane active" id="tab-logins" style="position: relative; height:320px;">
              <canvas id="chartLogins"></canvas>
            </div>
            <div class="chart tab-pane" id="tab-usage" style="position: relative; height:320px;">
              <canvas id="chartUsage"></canvas>
            </div>
            <div class="chart tab-pane" id="tab-vouchers" style="position: relative; height:320px;">
              <canvas id="chartVoucherArea"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.col -->

    <div class="col-lg-6">
      <div class="card card-outline card-secondary h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h3 class="card-title mb-0">Voucher Status</h3>
          <div class="card-tools">
            <span class="badge badge-info" id="badge-voucher-trend">+6% vs yesterday</span>
          </div>
        </div>
        <div class="card-body">
          <div style="position:relative; height:320px;">
            <canvas id="chartVoucherDonut"></canvas>
          </div>
          <ul class="list-unstyled mt-3 mb-0 small">
            <li class="d-flex justify-content-between align-items-center">
              <span><span class="legend-dot" style="--c:#3b82f6;"></span>New</span><b id="vs-new">0</b>
            </li>
            <li class="d-flex justify-content-between align-items-center">
              <span><span class="legend-dot" style="--c:#22c55e;"></span>Sold</span><b id="vs-sold">0</b>
            </li>
            <li class="d-flex justify-content-between align-items-center">
              <span><span class="legend-dot" style="--c:#f59e0b;"></span>Activated</span><b id="vs-activated">0</b>
            </li>
            <li class="d-flex justify-content-between align-items-center">
              <span><span class="legend-dot" style="--c:#ef4444;"></span>Expired</span><b id="vs-expired">0</b>
            </li>
          </ul>
        </div>
      </div>
    </div><!-- /.col -->
  </div>

  {{-- ======== Row: Top Clients + Routers Health ======== --}}
  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-users mr-1"></i>Top Active Clients (Today)</h3>
          <div class="card-tools">
            <a href="#" class="btn btn-tool" id="btn-export"><i class="fas fa-file-csv"></i></a>
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="thead-light">
                <tr>
                  <th>User</th><th>Profile</th><th>IP</th><th>Uptime</th><th>DL</th><th>UL</th><th></th>
                </tr>
              </thead>
              <tbody id="top-users-body"><!-- filled by JS --></tbody>
            </table>
          </div>
        </div>
      </div>
    </div><!-- /.col -->

    <div class="col-lg-5">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-network-wired mr-1"></i>Routers Health</h3>
        </div>
        <div class="card-body" id="routers-health">
          <!-- filled by JS -->
        </div>
      </div>
    </div><!-- /.col -->
  </div>

  {{-- ======== Row: Popular Profiles + Ops Timeline (MODERN) ======== --}}
  <div class="row">
    <div class="col-lg-5">
      <div class="card card-outline card-info">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-signal mr-1"></i>Popular Profiles (Today)</h3>
        </div>
        <div class="card-body" id="popular-profiles"><!-- filled by JS --></div>
      </div>
    </div><!-- /.col -->

    <div class="col-lg-7">
      <div class="card card-outline card-warning">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h3 class="card-title mb-0"><i class="fas fa-stream mr-1"></i>Ops Timeline</h3>
          <div class="card-tools"><span class="badge badge-warning">Live</span></div>
        </div>
        <div class="card-body p-0">
          <div class="tl-modern" id="ops-timeline"><!-- JS renders --></div>
        </div>
      </div>
    </div><!-- /.col -->
  </div>

</div>
</section>

{{-- ======== Minimal styles for this dashboard ======== --}}
<style>
  .nav-pills-soft .nav-link{
    border-radius:10px; margin-right:.35rem; border:1px solid #e5e7eb; color:#374151;
    padding:.35rem .7rem; transition:all .15s ease;
  }
  .nav-pills-soft .nav-link.active{
    background:#eef2ff; color:#1f3a8a; border-color:#c7d2fe;
    box-shadow:0 2px 10px rgba(59,130,246,.12);
  }
  .legend-dot{
    width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:8px;background:var(--c,#ddd);
    box-shadow:0 0 0 3px rgba(0,0,0,.04) inset;
  }
  .progress.progress-xxs { height:6px; }
  #chartLogins,#chartUsage,#chartVoucherArea,#chartVoucherDonut{ width:100%!important; height:100%!important; }

  /* ===== Modern Timeline ===== */
  .tl-modern{ position:relative; padding:26px 18px; }
  @media (min-width: 992px){
    .tl-modern{ display:grid; grid-template-columns: 1fr 1fr; gap:28px 24px; }
    .tl-modern::before{
      content:""; position:absolute; top:0; bottom:0; left:50%;
      width:2px; background:linear-gradient(to bottom, transparent, rgba(0,0,0,.14), transparent);
      transform:translateX(-1px);
    }
  }
  .tl-date{ grid-column: 1 / -1; text-align:center; margin:2px 0 14px; }
  .tl-date .chip{
    display:inline-block; padding:6px 12px; border-radius:999px; font-weight:700; font-size:.78rem;
    background:#fff3cd; color:#7a5b00; border:1px solid #ffe69c;
  }
  .tl-item{ position:relative; margin-bottom:20px; }
  @media (min-width: 992px){
    .tl-item.left  { grid-column: 1 / 2; justify-self: end; padding-right:38px; }
    .tl-item.right { grid-column: 2 / 3; justify-self: start; padding-left:38px; }
  }
  @media (max-width: 991.98px){
    .tl-item{ padding-left:28px; }
    .tl-modern::before{
      content:""; position:absolute; top:0; bottom:0; left:7px; width:2px;
      background:linear-gradient(to bottom, transparent, rgba(0,0,0,.12), transparent);
    }
  }
  .tl-dot{
    position:absolute; top:18px; width:18px; height:18px; border-radius:50%;
    background:#fff; border:3px solid #f59e0b; box-shadow:0 0 0 4px rgba(245,158,11,.15);
  }
  @media (min-width: 992px){
    .tl-item.left  .tl-dot{ right:-9px; }
    .tl-item.right .tl-dot{ left:-9px; }
  }
  @media (max-width: 991.98px){
    .tl-item .tl-dot{ left:-2px; }
  }
  .tl-card{
    background:#ffffff; border:1px solid #edf2f7; border-radius:14px;
    box-shadow:0 6px 22px rgba(16,24,40,.06); padding:14px 16px;
    transition:transform .15s ease;
  }
  .tl-card:hover{ transform:translateY(-1px); }
  .tl-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
  .tl-title{ margin:0; font-weight:700; font-size:0.98rem; color:#1f2937; }
  .tl-time{ font-size:.8rem; color:#6b7280; white-space:nowrap; }
  .tl-meta{ display:flex; gap:8px; align-items:center; margin:6px 0 10px; }
  .tl-badge{ font-size:.72rem; padding:3px 8px; border-radius:999px; font-weight:700; border:1px solid transparent; }
  .tl-badge.ok{ background:#e8fff1; color:#1f9254; border-color:#bbf7d0; }
  .tl-badge.warn{ background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
  .tl-badge.alert{ background:#ffecec; color:#c23b3b; border-color:#fecaca; }
  .tl-badge.sync{ background:#eef2ff; color:#4338ca; border-color:#c7d2fe; }
  .tl-body{ color:#374151; font-size:.92rem; }
  .tl-actions{ margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; }
  .tl-actions .btn-xs{ padding:3px 8px; border-radius:8px; font-size:.75rem; }
  body.dark-mode .tl-card{ background:#1f2937; border-color:#374151; }
  body.dark-mode .tl-title{ color:#e5e7eb; }
  body.dark-mode .tl-time{ color:#9ca3af; }
  body.dark-mode .tl-body{ color:#d1d5db; }
  body.dark-mode .tl-modern::before{ background:linear-gradient(to bottom, transparent, rgba(255,255,255,.18), transparent); }
</style>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* ===========================
   Dashboard Script (Demo-ready)
   Later: swap getDemoState()/demoTimeline with jQuery AJAX
   =========================== */

let cLogins, cUsage, cVoucherArea, cVoucherDonut;

// donut center text plugin
const pluginCenterText = {
  id:'centerText',
  afterDraw(chart){
    const ds = chart.config.data.datasets[0];
    if(!ds || !ds.data || !ds.data.length) return;
    const total = ds.data.reduce((a,b)=>a+b,0);
    const ctx = chart.ctx, p = chart.getDatasetMeta(0).data[0];
    if(!p) return;
    ctx.save();
    ctx.font = '600 16px system-ui, -apple-system, Segoe UI, Roboto';
    ctx.fillStyle = '#6b7280';
    ctx.textAlign='center'; ctx.textBaseline='middle';
    ctx.fillText('Total', p.x, p.y - 10);
    ctx.font = '700 20px system-ui, -apple-system, Segoe UI, Roboto';
    ctx.fillStyle = '#111827';
    ctx.fillText(total.toString(), p.x, p.y + 12);
    ctx.restore();
  }
};

// ===== Helpers
function lastNDates(n){
  const out=[], now=new Date();
  for(let i=n-1;i>=0;i--){ const d=new Date(now); d.setDate(now.getDate()-i);
    out.push(d.toLocaleDateString('en-GB',{day:'2-digit',month:'short'})); }
  return out;
}
function gradient(ctx, c1, c2){ const g = ctx.createLinearGradient(0,0,0,320); g.addColorStop(0,c1); g.addColorStop(1,c2); return g; }
function escapeHtml(s){ return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

// ===== Hydrate (call once or after AJAX)
function hydrate(data){
  updateKPIs(data.kpis);
  buildOrUpdateCharts(data);
  renderTopUsers(data.topUsers);
  renderRoutersHealth(data.routersHealth);
  renderPopularProfiles(data.popularProfiles);
  renderTimelineModern(data.timeline);
  document.getElementById('badge-voucher-trend').innerText = data.vouchersTrend || '+0% vs yesterday';
}

// ===== KPIs
function updateKPIs(k){
  const set = (id,val) => document.getElementById(id).innerText = val;
  set('kpi-active', k.active); set('kpi-online', k.online);
  set('kpi-usage', (+k.usage_gb).toFixed(1)); set('kpi-voucher', k.new_vouchers);
  set('kpi-active-trend', k.trendActive); set('kpi-online-trend', k.trendOnline);
  set('kpi-usage-trend', k.trendUsage); set('kpi-voucher-trend', k.trendVouchers);
  document.getElementById('vs-new').innerText       = k.statusNew;
  document.getElementById('vs-sold').innerText      = k.statusSold;
  document.getElementById('vs-activated').innerText = k.statusActivated;
  document.getElementById('vs-expired').innerText   = k.statusExpired;
}

// ===== Charts
function buildOrUpdateCharts(d){
  // Logins
  const ctxL = document.getElementById('chartLogins').getContext('2d');
  if(!cLogins){
    cLogins = new Chart(ctxL, {
      type:'line',
      data:{ labels:d.logins.labels, datasets:[{
        label:'Logins', data:d.logins.data, fill:true,
        backgroundColor: gradient(ctxL,'rgba(59,130,246,.35)','rgba(59,130,246,.05)'),
        borderColor:'#3b82f6', borderWidth:2, pointRadius:2.5, tension:.35
      }]},
      options:{ maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, grid:{ borderDash:[3,3] } }, x:{ grid:{ display:false } } }, plugins:{ legend:{ display:false } } }
    });
  } else { cLogins.data.labels=d.logins.labels; cLogins.data.datasets[0].data=d.logins.data; cLogins.update(); }

  // Usage
  const ctxU = document.getElementById('chartUsage').getContext('2d');
  if(!cUsage){
    cUsage = new Chart(ctxU, {
      type:'bar',
      data:{ labels:d.usage.labels, datasets:[{ label:'GB', data:d.usage.data, borderColor:'#60a5fa', backgroundColor:'rgba(96,165,250,.35)', borderWidth:1 }]},
      options:{ maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, grid:{ borderDash:[3,3] } }, x:{ grid:{ display:false } } }, plugins:{ legend:{ display:false } } }
    });
  } else { cUsage.data.labels=d.usage.labels; cUsage.data.datasets[0].data=d.usage.data; cUsage.update(); }

  // Voucher Activated (area)
  const ctxA = document.getElementById('chartVoucherArea').getContext('2d');
  if(!cVoucherArea){
    cVoucherArea = new Chart(ctxA, {
      type:'line',
      data:{ labels:d.vouchersActivated.labels, datasets:[{
        label:'Activated', data:d.vouchersActivated.data, fill:true,
        backgroundColor: gradient(ctxA,'rgba(245,158,11,.35)','rgba(245,158,11,.05)'),
        borderColor:'#f59e0b', borderWidth:2, pointRadius:2.5, tension:.35
      }]},
      options:{ maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, grid:{ borderDash:[3,3] } }, x:{ grid:{ display:false } } }, plugins:{ legend:{ display:false } } }
    });
  } else { cVoucherArea.data.labels=d.vouchersActivated.labels; cVoucherArea.data.datasets[0].data=d.vouchersActivated.data; cVoucherArea.update(); }

  // Voucher Status donut
  const ctxD = document.getElementById('chartVoucherDonut').getContext('2d');
  if(!cVoucherDonut){
    cVoucherDonut = new Chart(ctxD, {
      type:'doughnut',
      data:{ labels:['New','Sold','Activated','Expired'],
        datasets:[{ data:[d.kpis.statusNew, d.kpis.statusSold, d.kpis.statusActivated, d.kpis.statusExpired],
          backgroundColor:['#3b82f6','#22c55e','#f59e0b','#ef4444'] }] },
      options:{ maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } }, cutout:'68%' },
      plugins:[pluginCenterText]
    });
  } else { cVoucherDonut.data.datasets[0].data=[d.kpis.statusNew,d.kpis.statusSold,d.kpis.statusActivated,d.kpis.statusExpired]; cVoucherDonut.update(); }
}

// ===== Tables & Cards
function renderTopUsers(rows){
  const tbody = document.getElementById('top-users-body');
  tbody.innerHTML = rows.map(r => `
    <tr>
      <td><i class="far fa-user-circle mr-1 text-muted"></i>${escapeHtml(r.user)}</td>
      <td>${escapeHtml(r.profile)}</td>
      <td><code>${escapeHtml(r.ip)}</code></td>
      <td>${escapeHtml(r.uptime)}</td>
      <td>${escapeHtml(r.dl)}</td>
      <td>${escapeHtml(r.ul)}</td>
      <td><a class="btn btn-xs btn-outline-primary" href="#">Manage</a></td>
    </tr>`).join('');
}
function renderRoutersHealth(items){
  const box = document.getElementById('routers-health');
  box.innerHTML = items.map(it => `
    <div class="mb-3">
      <div class="d-flex justify-content-between"><span>${escapeHtml(it.name)}</span><span class="badge badge-${it.badgeClass}">${escapeHtml(it.status)}</span></div>
      <div class="progress progress-xxs mt-1"><div class="progress-bar bg-${it.progressClass}" style="width:${it.ram}%"></div></div>
      <small class="text-muted">CPU ${it.cpu}% • RAM ${it.ram}% • Latency ${it.latency}ms</small>
    </div>`).join('');
}
function renderPopularProfiles(list){
  const box = document.getElementById('popular-profiles');
  box.innerHTML = list.map(p => `
    <div class="info-box mb-3">
      <span class="info-box-icon ${p.bg}"><i class="${p.icon}"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">${escapeHtml(p.title)}</span>
        <span class="info-box-number">${escapeHtml(p.total)} total</span>
        <div class="progress"><div class="progress-bar ${p.progressClass}" style="width:${p.percent}%"></div></div>
        <span class="progress-description">${escapeHtml(p.caption)}</span>
      </div>
    </div>`).join('');
}

/* ===== Modern Timeline Renderer =====
 * item: {dateLabel?, side?, time, title, body, type:'ok'|'warn'|'alert'|'sync', icon, actions:[{text,href}], meta }
 */
function renderTimelineModern(items){
  const host = document.getElementById('ops-timeline');
  if(!host) return;

  const groups = [];
  let currentLabel = null;
  items.forEach((it, idx) => {
    const label = it.dateLabel || (idx===0 ? 'Today' : null);
    if(label && label !== currentLabel){ groups.push({ type:'label', label }); currentLabel = label; }
    groups.push({ type:'item', ...it });
  });

  host.innerHTML = groups.map((g, i) => {
    if(g.type === 'label'){
      return `<div class="tl-date"><span class="chip">${escapeHtml(g.label)}</span></div>`;
    }
    const side = g.side || ((i % 2) ? 'left' : 'right');
    const badgeClass = ({ok:'ok', warn:'warn', alert:'alert', sync:'sync'})[g.type] || 'ok';
    const iconHtml = g.icon ? `<i class="${g.icon} mr-1"></i>` : '';
    const actions = (g.actions||[]).map(a => `<a href="${a.href||'#'}" class="btn btn-outline-secondary btn-xs">${escapeHtml(a.text||'Open')}</a>`).join('');
    return `
      <div class="tl-item ${side}">
        <span class="tl-dot"></span>
        <div class="tl-card">
          <div class="tl-head">
            <h6 class="tl-title">${iconHtml}${escapeHtml(g.title||'Untitled')}</h6>
            <div class="tl-time"><i class="far fa-clock mr-1"></i>${escapeHtml(g.time||'--:--')}</div>
          </div>
          <div class="tl-meta">
            ${g.type ? `<span class="tl-badge ${badgeClass}">${({ok:'Success',warn:'Warning',alert:'Alert',sync:'Sync'})[g.type]||'Info'}</span>` : ''}
            ${g.meta ? `<span class="text-muted small">${escapeHtml(g.meta)}</span>` : ''}
          </div>
          <div class="tl-body">${escapeHtml(g.body||'')}</div>
          ${actions ? `<div class="tl-actions">${actions}</div>` : ''}
        </div>
      </div>`;
  }).join('');
}

// ===== Tab resize fix
$('a[data-toggle="tab"]').on('shown.bs.tab', function () {
  cLogins && cLogins.resize(); cUsage && cUsage.resize(); cVoucherArea && cVoucherDonut && cVoucherDonut.resize();
});

// ===== Range buttons (demo 7/30/90)
$('#chartTabs').closest('.card').find('[data-range]').on('click', function(){
  $(this).siblings().removeClass('active'); $(this).addClass('active');
  hydrate(getDemoState(Number(this.dataset.range)||7)); // later: call AJAX with ?days=
});

// ===== Sync (demo)
document.getElementById('btn-sync').addEventListener('click', function(){
  this.classList.add('disabled');
  setTimeout(()=>{ hydrate(getDemoState()); this.classList.remove('disabled'); }, 600);
});

/* ================= Demo Data ================= */
function getDemoState(days=7){
  const labels = lastNDates(days);
  const logins = Array.from({length:days}, (_,i)=> 110 + Math.round( (i+1) * (Math.random()*6+8) ));
  const activated = Array.from({length:days}, (_,i)=> 14 + Math.round( (i+1) * (Math.random()*1.6+1.4) ));
  const usageLabels = ['1H-200MB','1D-2GB','7D-10GB','30D-60GB'];
  const usageData = [6.5,22.3,38.1,25.5].map(v=> (v*(0.9+Math.random()*0.2)).toFixed(1));
  const k = {
    active: logins[days-1],
    online: 40 + Math.round(Math.random()*30),
    usage_gb: 80 + Math.random()*40,
    new_vouchers: 150 + Math.round(Math.random()*60),
    trendActive:`+${Math.round(Math.random()*15)}%`,
    trendOnline:`+${Math.round(Math.random()*10)}%`,
    trendUsage:`+${Math.round(Math.random()*12)}%`,
    trendVouchers:`+${Math.round(Math.random()*8)}%`,
    statusNew: 220, statusSold: 160, statusActivated: 132, statusExpired: 24
  };
  return {
    kpis: k,
    vouchersTrend: '+6% vs yesterday',
    logins: { labels, data: logins },
    usage: { labels: usageLabels, data: usageData },
    vouchersActivated: { labels, data: activated },
    topUsers: [
      {user:'sadia_93', profile:'7D-10GB', ip:'10.5.2.11', uptime:'2h 14m', dl:'3.2 GB', ul:'0.4 GB'},
      {user:'rahim',    profile:'30D-60GB', ip:'10.5.2.57', uptime:'1h 05m', dl:'1.1 GB', ul:'0.2 GB'},
      {user:'guest-8Z', profile:'1D-2GB',   ip:'10.5.2.77', uptime:'0h 47m', dl:'0.8 GB', ul:'0.1 GB'},
      {user:'anisul',   profile:'7D-10GB',  ip:'10.5.2.21', uptime:'3h 01m', dl:'5.6 GB', ul:'0.7 GB'},
      {user:'guest-KQ', profile:'1H-200MB', ip:'10.5.2.103',uptime:'0h 18m', dl:'120 MB', ul:'20 MB'},
    ],
    routersHealth: [
      {name:'POP Mirpur', status:'OK',        badgeClass:'success', progressClass:'success', cpu:34, ram:68, latency:7},
      {name:'POP Dhanmondi', status:'Degraded', badgeClass:'warning', progressClass:'warning', cpu:71, ram:82, latency:16},
      {name:'POP Uttara', status:'Alert',     badgeClass:'danger',  progressClass:'danger', cpu:88, ram:93, latency:35},
    ],
    popularProfiles: [
      {title:'1H • 200MB', total:'6.5 GB', percent:32, caption:'32% of small-pack usage', bg:'bg-info', icon:'fas fa-bolt', progressClass:'bg-info'},
      {title:'1D • 2GB',   total:'22.3 GB', percent:54, caption:'54% of daily usage',     bg:'bg-success', icon:'fas fa-clock', progressClass:'bg-success'},
      {title:'7D • 10GB',  total:'38.1 GB', percent:73, caption:'Top moving package',    bg:'bg-primary', icon:'fas fa-calendar-week', progressClass:'bg-primary'},
    ],
    timeline: [
      { dateLabel:'Today', side:'left',  time:'09:20', title:'Batch #231 pushed to POP Mirpur', body:'180 vouchers created, 180 users added in /ip hotspot user', type:'ok',   icon:'fas fa-ticket-alt',            actions:[{text:'View batch', href:'#'}],                    meta:'Mirpur' },
      {                  side:'right', time:'10:05', title:'Router warning',                      body:'Uttara latency spiked to 35ms • CPU 88%',                 type:'alert',icon:'fas fa-exclamation-triangle', actions:[{text:'Open router', href:'#'},{text:'Ack', href:'#'}], meta:'Uttara' },
      {                  side:'left',  time:'10:30', title:'Auto-sync complete',                  body:'Active sessions pulled • dashboard updated',             type:'sync', icon:'fas fa-sync-alt',             actions:[{text:'View logs', href:'#'}],                     meta:'System' },
      {                  side:'right', time:'11:10', title:'Voucher sales',                        body:'Reseller-07 sold 42 vouchers (1D-2GB)',                  type:'ok',   icon:'fas fa-hand-holding-usd',      actions:[{text:'Sales report', href:'#'}],                   meta:'Reseller-07' },
    ]
  };
}

/* ========== INITIALIZE (demo) ========== */
hydrate(getDemoState());

/* ========== LATER: jQuery AJAX ENABLE ==========
   👉 তোমার এপিআই রেডি হলে এই ব্লক আনকমেন্ট করে ব্যবহার করো:

function loadDashboard(params){
  // 1) Metrics
  $.getJSON('/api/hotspot/metrics', params)
   .done(function(data){ hydrate(data); })
   .fail(function(){ hydrate(getDemoState()); });

  // 2) Timeline আলাদা এন্ডপয়েন্ট হলে:
  $.getJSON('/api/hotspot/timeline', params)
   .done(function(items){
     renderTimelineModern(items);
   });
}

// Example: range/button change হলে
$('[data-range]').on('click', function(){
  var days = Number(this.dataset.range)||7;
  loadDashboard({ days: days, router: $('#filter-router').val() });
});
$('#filter-router').on('change', function(){
  loadDashboard({ days: 7, router: this.value });
});
================================================== */
</script>
@endsection
