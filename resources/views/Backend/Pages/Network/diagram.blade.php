@extends('Backend.Layout.App')
@section('title', 'Industrial Network Diagram')

{{-- ================= STYLES ================= --}}
@section('style')
<style>
  :root{
    --nd-bg: #f8f9fa;
    --nd-card: #ffffff;
    --nd-border: #e5e7eb;
    --nd-text: #111827;
    --nd-sub: #6b7280;
    --nd-accent: #2563eb;

    --nd-good: #10b981;
    --nd-warn: #f59e0b;
    --nd-bad:  #ef4444;
    --nd-mid:  #9aa4b2;
  }
  .nd-dark {
    --nd-bg: #0f1113;
    --nd-card: #14181c;
    --nd-border: #222830;
    --nd-text: #e5e7eb;
    --nd-sub: #9aa4b2;
    --nd-accent: #60a5fa;
    --nd-mid:  #5b6573;
  }

  .nd-page { background: var(--nd-bg); }
  .nd-title { color: var(--nd-text); }
  .nd-sub { color: var(--nd-sub); }
  .nd-card {
    background: var(--nd-card);
    border: 1px solid var(--nd-border);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.06);
  }
  .nd-border { border-color: var(--nd-border)!important; }
  .nd-btn { border-radius: 10px!important; }

  /* Top KPIs */
  .kpi {
    display:grid; grid-template-columns:auto 1fr; grid-gap:.5rem; align-items:center;
  }
  .kpi-figure {
    width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center;
    background: rgba(37,99,235,.08);
    color: var(--nd-accent); font-size:18px;
  }
  .kpi .h5 { margin:0; color: var(--nd-text); }
  .kpi small { color: var(--nd-sub); }

  /* Grid layout */
  .nd-grid {
    display: grid;
    grid-template-columns: 300px 1fr 340px;
    gap: 1rem;
  }
  @media (max-width: 1199.98px) { .nd-grid { grid-template-columns: 260px 1fr 300px; } }
  @media (max-width: 991.98px) { .nd-grid { grid-template-columns: 1fr; } }

  /* Toolbar */
  .nd-toolbar {
    position: sticky; top: 0; z-index: 8;
    background: var(--nd-card);
    border: 1px solid var(--nd-border);
    border-radius: 12px;
    padding: .5rem;
  }
  .nd-toolbar .btn { margin-right:.35rem; margin-bottom:.35rem; }
  .nd-toolbar .btn:last-child { margin-right:0; }

  /* Diagram panes */
  #industrialDiagram {
    width: 100%;
    height: 72vh;
    min-height: 520px;
    border-radius: 12px;
    border: 1px dashed var(--nd-border);
    background: var(--nd-card);
  }
  #paletteDiv, #overviewDiv {
    width: 100%;
    border-radius: 10px;
    border: 1px dashed var(--nd-border);
    background: var(--nd-card);
  }
  #paletteDiv { height: 260px; }
  #overviewDiv { height: 160px; }

  /* Legend dots */
  .legend-dot{ display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:6px; vertical-align:middle; }
  .status-up   { background:var(--nd-good); }
  .status-warn { background:var(--nd-warn); }
  .status-down { background:var(--nd-bad); }

  /* Inputs */
  .form-control, .custom-select {
    border-radius: 10px;
    border-color: var(--nd-border);
    background: var(--nd-card);
    color: var(--nd-text);
  }
  .form-control:focus, .custom-select:focus {
    border-color: var(--nd-accent);
    box-shadow: 0 0 0 .15rem rgba(37,99,235,.15);
  }

  /* Mini tables */
  .table.nd-table { color: var(--nd-text); }
  .table.nd-table th, .table.nd-table td { border-color: var(--nd-border); }
  .progress { background:#e5e7eb22; height:8px; border-radius:999px; }
  .progress-bar { background: var(--nd-accent); }

  /* JSON modal text */
  .nd-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
    font-size: 12px;
    border-radius: 10px;
  }

  /* Small badges */
  .badge-soft {
    border:1px solid var(--nd-border);
    background: transparent;
    color: var(--nd-text);
    border-radius: 999px;
    padding:.25rem .5rem;
  }
</style>
@endsection

{{-- ================= CONTENT ================= --}}
@section('content')
<div class="container-fluid nd-page py-3">

  {{-- Header --}}
  <div class="d-flex align-items-center mb-3">
    <div class="mr-2" style="font-size:28px;">📡</div>
    <div>
      <h4 class="mb-0 nd-title">Industrial Network Diagram</h4>
      <small class="nd-sub">Observium-inspired topology designer & export</small>
    </div>
    <div class="ml-auto d-flex align-items-center">
      <span class="badge badge-soft mr-2"><span class="legend-dot status-up"></span>Up</span>
      <span class="badge badge-soft mr-2"><span class="legend-dot status-warn"></span>Degraded</span>
      <span class="badge badge-soft mr-3"><span class="legend-dot status-down"></span>Down</span>
      <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="ndDarkToggle">
        <label class="custom-control-label" for="ndDarkToggle">Dark</label>
      </div>
    </div>
  </div>

  {{-- Top KPIs --}}
  <div class="row">
    <div class="col-md-3 mb-2">
      <div class="nd-card p-3 kpi">
        <div class="kpi-figure"><i class="fas fa-server"></i></div>
        <div>
          <div class="h5" id="kpiDevices">0 Devices</div>
          <small class="nd-sub"><span class="text-success" id="kpiUp">0 up</span> · <span class="text-warning" id="kpiWarn">0 warn</span> · <span class="text-danger" id="kpiDown">0 down</span></small>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="nd-card p-3 kpi">
        <div class="kpi-figure"><i class="fas fa-project-diagram"></i></div>
        <div>
          <div class="h5" id="kpiLinks">0 Links</div>
          <small class="nd-sub">Avg util: <span id="kpiAvgUtil">0%</span></small>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="nd-card p-3 kpi">
        <div class="kpi-figure"><i class="fas fa-tachometer-alt"></i></div>
        <div>
          <div class="h5" id="kpiLatency">— ms</div>
          <small class="nd-sub">Avg device latency</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="nd-card p-3 kpi">
        <div class="kpi-figure"><i class="fas fa-cloud-upload-alt"></i></div>
        <div>
          <div class="h5" id="kpiTraffic">—</div>
          <small class="nd-sub">Total provisioned capacity</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Toolbar --}}
  <div class="nd-toolbar mb-3">
    <div class="d-flex flex-wrap align-items-center">
      <div class="btn-group mr-2 mb-1">
        <button class="btn btn-sm btn-primary nd-btn" id="btnNew"><i class="fas fa-file mr-1"></i>New</button>
        <button class="btn btn-sm btn-outline-primary nd-btn" id="btnOpen"><i class="fas fa-folder-open mr-1"></i>Open</button>
        <button class="btn btn-sm btn-outline-primary nd-btn" id="btnSave"><i class="fas fa-save mr-1"></i>Save</button>
      </div>
      <div class="btn-group mr-2 mb-1">
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnPng"><i class="far fa-image mr-1"></i>PNG</button>
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnSvg"><i class="far fa-file-code mr-1"></i>SVG</button>
      </div>
      <div class="btn-group mr-2 mb-1">
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnZoomIn"><i class="fas fa-search-plus"></i></button>
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnZoomOut"><i class="fas fa-search-minus"></i></button>
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnResetZoom"><i class="fas fa-compress-arrows-alt"></i></button>
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnFit"><i class="fas fa-expand"></i></button>
      </div>
      <div class="btn-group mr-2 mb-1">
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnLayout"><i class="fas fa-project-diagram mr-1"></i>Auto Layout</button>
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnSnap"><i class="fas fa-border-all mr-1"></i>Snap Grid</button>
        <button class="btn btn-sm btn-outline-secondary nd-btn" id="btnTemplates"><i class="fas fa-magic mr-1"></i>Quick Templates</button>
      </div>
      <div class="ml-auto d-flex align-items-center mb-1" style="min-width:260px; max-width:440px;">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend">
            <span class="input-group-text nd-border"><i class="fas fa-search"></i></span>
          </div>
          <input type="text" class="form-control" id="searchInput" placeholder="Search (name/IP/type)">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary nd-btn" id="btnClearSearch">Clear</button>
          </div>
        </div>
      </div>
      <input type="file" id="fileInput" accept="application/json" class="d-none">
    </div>
  </div>

  {{-- Main grid --}}
  <div class="nd-grid">
    {{-- Left: Palette + Overview + Panels --}}
    <div class="nd-card p-3">
      <div class="d-flex align-items-center mb-2">
        <h6 class="mb-0 nd-title">Device Palette</h6>
        <small class="ml-2 nd-sub">Drag to canvas</small>
      </div>
      <div id="paletteDiv" class="mb-3"></div>

      <div class="d-flex align-items-center mb-2">
        <h6 class="mb-0 nd-title">Overview</h6>
        <small class="ml-2 nd-sub">Mini-map</small>
      </div>
      <div id="overviewDiv" class="mb-3"></div>

      <div class="mb-3">
        <h6 class="nd-title mb-2">Legend</h6>
        <div class="nd-sub">
          <div class="mb-1"><span class="legend-dot status-up"></span>Up</div>
          <div class="mb-1"><span class="legend-dot status-warn"></span>Degraded</div>
          <div class="mb-1"><span class="legend-dot status-down"></span>Down</div>
        </div>
      </div>

      <div class="mb-3">
        <h6 class="nd-title mb-2">Top Interfaces</h6>
        <table class="table table-sm nd-table">
          <thead>
            <tr class="nd-sub"><th>Port</th><th>Util</th><th>Cap</th></tr>
          </thead>
          <tbody id="tblPorts">
            {{-- filled by JS --}}
          </tbody>
        </table>
      </div>

      <div>
        <h6 class="nd-title mb-2">Recent Alerts</h6>
        <div class="small" id="alertList">
          {{-- filled by JS --}}
        </div>
      </div>
    </div>

    {{-- Center: Diagram --}}
    <div class="nd-card p-2">
      <div id="industrialDiagram"></div>
    </div>

    {{-- Right: Inspector --}}
    <div class="nd-card p-3">
      <div class="d-flex align-items-center mb-2">
        <h6 class="mb-0 nd-title">Inspector</h6>
        <small class="ml-2 nd-sub">Edit selected node/link</small>
      </div>

      <form id="inspectorForm">
        <div class="form-group mb-2">
          <label class="nd-sub mb-1">Name</label>
          <input type="text" class="form-control form-control-sm" name="name" placeholder="e.g., Core Router">
        </div>
        <div class="form-group mb-2">
          <label class="nd-sub mb-1">IP / Host</label>
          <input type="text" class="form-control form-control-sm" name="ip" placeholder="10.10.0.1">
        </div>
        <div class="form-group mb-2">
          <label class="nd-sub mb-1">Device Type</label>
          <select name="device" class="custom-select custom-select-sm">
            <option value="router">Router</option>
            <option value="switch">Switch</option>
            <option value="server">Server</option>
            <option value="ap">Access Point</option>
            <option value="pop">POP</option>
            <option value="olt">OLT</option>
            <option value="onu">ONU</option>
            <option value="client">Client</option>
          </select>
        </div>
        <div class="form-group mb-2">
          <label class="nd-sub mb-1">Status</label>
          <select name="status" class="custom-select custom-select-sm">
            <option value="up">Up</option>
            <option value="warn">Degraded</option>
            <option value="down">Down</option>
          </select>
        </div>
        <div class="form-group mb-2">
          <label class="nd-sub mb-1">Latency (ms)</label>
          <input type="number" class="form-control form-control-sm" name="lat" placeholder="e.g., 4.7">
        </div>
        <div class="form-group mb-3">
          <label class="nd-sub mb-1">Notes</label>
          <textarea name="notes" rows="3" class="form-control" placeholder="Maintenance window, rack location, etc."></textarea>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary nd-btn btn-block" id="btnApplyNode">
          <i class="fas fa-check mr-1"></i>Apply Node Changes
        </button>
      </form>

      <hr class="nd-border">

      <form id="linkForm">
        <div class="d-flex align-items-center mb-2">
          <h6 class="mb-0 nd-title">Link Inspector</h6>
          <small class="ml-2 nd-sub">Capacity & utilization</small>
        </div>
        <div class="form-group mb-2">
          <label class="nd-sub mb-1">Capacity</label>
          <select name="cap" class="custom-select custom-select-sm">
            <option>100M</option>
            <option>1G</option>
            <option>10G</option>
            <option>40G</option>
            <option>100G</option>
          </select>
        </div>
        <div class="form-group mb-2">
          <label class="nd-sub mb-1">Utilization (%)</label>
          <input type="number" class="form-control form-control-sm" name="util" min="0" max="100" step="1">
        </div>
        <div class="form-group mb-3">
          <label class="nd-sub mb-1">Label</label>
          <input type="text" class="form-control form-control-sm" name="label" placeholder="e.g., Trunk-01">
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary nd-btn btn-block" id="btnApplyLink">
          <i class="fas fa-check mr-1"></i>Apply Link Changes
        </button>
      </form>

      <hr class="nd-border">

      <div class="nd-sub small">
        <div class="mb-1"><strong>Tip:</strong> Double-click node text to edit inline.</div>
        <div>Shift = multi-select · Right-click = context menu</div>
      </div>
    </div>
  </div>
</div>

{{-- Save JSON Modal --}}
<div class="modal fade" id="saveJsonModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content nd-card">
      <div class="modal-header">
        <h6 class="modal-title nd-title mb-0"><i class="fas fa-save mr-1"></i>Export JSON</h6>
        <button type="button" class="close nd-sub" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <textarea id="jsonOutput" class="form-control nd-code" rows="16" readonly></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-sm btn-outline-secondary nd-btn" data-dismiss="modal">Close</button>
        <button class="btn btn-sm btn-primary nd-btn" id="btnCopyJson"><i class="far fa-copy mr-1"></i>Copy</button>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- ================= SCRIPTS ================= --}}
@section('script')
<script src="https://unpkg.com/gojs/release/go.js"></script>
<script>
(function(){
  const $ = go.GraphObject.make;

  // ---------- THEME ----------
  const rootPage  = document.querySelector('.nd-page') || document.body;
  const darkToggle = document.getElementById('ndDarkToggle');
  let dark = false;
  function applyTheme() {
    if (dark) rootPage.classList.add('nd-dark'); else rootPage.classList.remove('nd-dark');
    myDiagram.background = dark ? '#0f1113' : '#ffffff';
    myPalette.background  = dark ? '#14181c' : '#ffffff';
    myOverview.background = dark ? '#14181c' : '#ffffff';
    myDiagram.requestUpdate();
  }
  if (darkToggle) darkToggle.addEventListener('change', () => { dark = !!darkToggle.checked; applyTheme(); });

  // ---------- COLORS / ICONS ----------
  const deviceColor = {
    router: '#3b82f6',   // blue
    switch: '#06b6d4',   // cyan
    server: '#8b5cf6',   // violet
    ap:     '#10b981',   // green
    pop:    '#f59e0b',   // amber
    olt:    '#ef4444',   // red
    onu:    '#22c55e',   // lime
    client: '#94a3b8'    // slate
  };
  const statusStroke = { up:'#10b981', warn:'#f59e0b', down:'#ef4444' };
  const emojiByType  = { router:'🛜', switch:'🖧', server:'🖥️', ap:'📶', pop:'📡', olt:'🧩', onu:'🔌', client:'👤' };

  // ---------- DIAGRAM ----------
  const myDiagram = $(go.Diagram, 'industrialDiagram', {
    'undoManager.isEnabled': true,
    'linkingTool.isEnabled': true,
    'relinkingTool.isEnabled': true,
    'draggingTool.dragsLink': true,
    'draggingTool.dragsTree': true,
    initialContentAlignment: go.Spot.Center,
    layout: $(go.ForceDirectedLayout, { defaultSpringLength: 60, defaultElectricalCharge: 100 })
  });

  // Node Template (ContextMenu must be set as property, not child)
  myDiagram.nodeTemplate =
    $(go.Node, 'Auto',
      {
        resizable:true,
        cursor:'pointer',
        contextMenu: $('ContextMenu',
          $('ContextMenuButton',
            $(go.TextBlock, 'Ping'),
            { click: (e, obj) => openTool(obj.part.adornedPart.data, 'ping') }),
          $('ContextMenuButton',
            $(go.TextBlock, 'SSH'),
            { click: (e, obj) => openTool(obj.part.adornedPart.data, 'ssh') }),
          $('ContextMenuButton',
            $(go.TextBlock, 'Open Web UI'),
            { click: (e, obj) => openTool(obj.part.adornedPart.data, 'http') })
        )
      },
      new go.Binding('location', 'loc', go.Point.parse).makeTwoWay(go.Point.stringify),
      $(go.Shape, 'RoundedRectangle',
        {
          strokeWidth:2, portId:'', fromLinkable:true, toLinkable:true,
          fromSpot: go.Spot.AllSides, toSpot: go.Spot.AllSides
        },
        new go.Binding('fill', 'device', d => deviceColor[d] || '#9ca3af'),
        new go.Binding('stroke', 'status', s => statusStroke[s] || '#9ca3af')
      ),
      $(go.Panel, 'Table', { padding: new go.Margin(8,10,8,10) },
        // Icon
        $(go.TextBlock,
          { row:0, column:0, font:'18px/1.2 system-ui', margin: new go.Margin(0,6,0,0) },
          new go.Binding('text', 'device', d => emojiByType[d] || '🔧')
        ),
        // Title
        $(go.TextBlock,
          { row:0, column:1, editable:true, font:'600 13px/1.2 system-ui' },
          new go.Binding('text', 'name').makeTwoWay(),
          new go.Binding('stroke', '', () => dark ? '#e5e7eb' : '#111827')
        ),
        // Sub (IP)
        $(go.TextBlock,
          { row:1, column:1, margin: new go.Margin(2,0,0,0), font:'12px system-ui' },
          new go.Binding('text', 'ip', ip => ip ? String(ip) : ''),
          new go.Binding('stroke', '', () => dark ? '#9aa4b2' : '#4b5563')
        ),
        // Status dot
        $(go.Shape, 'Circle',
          { row:0, column:2, desiredSize: new go.Size(10,10), margin: new go.Margin(0,0,0,8) },
          new go.Binding('fill', 'status', s => statusStroke[s] || '#9ca3af'),
          new go.Binding('stroke', '', () => dark ? '#0f1113' : '#ffffff')
        )
      )
    );

  // Link Template with label (capacity/util)
  myDiagram.linkTemplate =
    $(go.Link,
      {
        routing: go.Link.AvoidsNodes, corner: 8, toShortLength: 3,
        relinkableFrom:true, relinkableTo:true
      },
      $(go.Shape, { strokeWidth: 2 }, new go.Binding('stroke', '', getLinkStroke)),
      $(go.Shape, { toArrow: 'Standard', stroke: null }, new go.Binding('fill', '', getLinkStroke)),
      $(go.Panel, 'Auto',
        $(go.Shape, 'RoundedRectangle',
          { fill:'rgba(17,24,39,.85)', stroke:null, visible:true }
        ),
        $(go.TextBlock,
          { margin: new go.Margin(3,6,3,6), stroke:'#fff', font:'12px system-ui' },
          new go.Binding('text', '', d => (d.label || '') + (d.cap ? (' · ' + d.cap) : '') + (validPct(d.util) ? (' · ' + d.util + '%') : ''))
        ),
        new go.Binding('visible', 'label', l => !!l || true)
      )
    );

  function validPct(v){ return typeof v === 'number' && !isNaN(v); }
  function getLinkStroke(d){
    const util = d && typeof d.util === 'number' ? d.util : 0;
    if (util >= 80) return '#ef4444';
    if (util >= 60) return '#f59e0b';
    return '#9aa4b2';
  }

  // Palette
  const myPalette = $(go.Palette, 'paletteDiv', {
    nodeTemplate: myDiagram.nodeTemplate,
    contentAlignment: go.Spot.TopLeft
  });
  myPalette.model = new go.GraphLinksModel([
    { device:'router', name:'Router', status:'up' },
    { device:'switch', name:'Switch', status:'up' },
    { device:'server', name:'Server', status:'up' },
    { device:'ap',     name:'Access Point', status:'up' },
    { device:'pop',    name:'POP', status:'up' },
    { device:'olt',    name:'OLT', status:'up' },
    { device:'onu',    name:'ONU', status:'up' },
    { device:'client', name:'Client', status:'up' },
  ]);

  // Overview
  const myOverview = $(go.Overview, 'overviewDiv', { observed: myDiagram, contentAlignment: go.Spot.Center });

  // Initial model (mock)
  myDiagram.model = new go.GraphLinksModel(
    [
      { key: 1, device:'router', name:'Core Router', ip:'10.0.0.1', status:'up',   lat: 2.1, loc:'0 0' },
      { key: 2, device:'switch', name:'Dist Switch', ip:'10.0.1.1', status:'up',   lat: 3.7, loc:'160 0' },
      { key: 3, device:'server', name:'Billing VM',  ip:'10.0.2.10',status:'warn', lat: 5.8, loc:'320 0' },
      { key: 4, device:'ap',     name:'NOC AP',      ip:'10.0.3.5', status:'up',   lat: 4.2, loc:'160 100' },
      { key: 5, device:'pop',    name:'POP-1',       ip:'10.1.0.1', status:'down', lat: 0.0, loc:'-160 0' }
    ],
    [
      { from:1, to:2, cap:'10G', util:72, label:'Trunk-01' },
      { from:2, to:3, cap:'1G',  util:38, label:'Access-Srv' },
      { from:2, to:4, cap:'1G',  util:64, label:'Access-AP' },
      { from:1, to:5, cap:'10G', util:86, label:'Upstream' }
    ]
  );

  // ---------- INSPECTOR ----------
  let selectedNode = null, selectedLink = null;

  myDiagram.addDiagramListener('ChangedSelection', () => {
    const it = myDiagram.selection.first();
    selectedNode = (it && it instanceof go.Node) ? it : null;
    selectedLink = (it && it instanceof go.Link) ? it : null;
    hydrateInspector();
  });

  function hydrateInspector(){
    const f  = document.getElementById('inspectorForm') || { elements:{} };
    const lf = document.getElementById('linkForm') || { elements:{} };

    // Node
    const dn = selectedNode ? selectedNode.data : {};
    if (f.name)   f.name.value   = dn.name || '';
    if (f.ip)     f.ip.value     = dn.ip || '';
    if (f.device) f.device.value = dn.device || 'router';
    if (f.status) f.status.value = dn.status || 'up';
    if (f.lat)    f.lat.value    = dn.lat != null ? dn.lat : '';
    if (f.notes)  f.notes.value  = dn.notes || '';

    // Link
    const dl = selectedLink ? selectedLink.data : {};
    if (lf.cap)   lf.cap.value   = dl.cap || '1G';
    if (lf.util)  lf.util.value  = (dl.util != null ? dl.util : '');
    if (lf.label) lf.label.value = dl.label || '';
  }

  const btnApplyNode = document.getElementById('btnApplyNode');
  if (btnApplyNode) btnApplyNode.addEventListener('click', () => {
    if (!selectedNode) return;
    const f = document.getElementById('inspectorForm');
    myDiagram.model.startTransaction('node-inspector');
    myDiagram.model.setDataProperty(selectedNode.data, 'name',   f.name.value);
    myDiagram.model.setDataProperty(selectedNode.data, 'ip',     f.ip.value);
    myDiagram.model.setDataProperty(selectedNode.data, 'device', f.device.value);
    myDiagram.model.setDataProperty(selectedNode.data, 'status', f.status.value);
    myDiagram.model.setDataProperty(selectedNode.data, 'lat',    f.lat.value ? parseFloat(f.lat.value) : null);
    myDiagram.model.setDataProperty(selectedNode.data, 'notes',  f.notes ? f.notes.value : '');
    myDiagram.model.commitTransaction('node-inspector');
    refreshKPIs();
  });

  const btnApplyLink = document.getElementById('btnApplyLink');
  if (btnApplyLink) btnApplyLink.addEventListener('click', () => {
    if (!selectedLink) return;
    const lf = document.getElementById('linkForm');
    myDiagram.model.startTransaction('link-inspector');
    myDiagram.model.setDataProperty(selectedLink.data, 'cap',   lf.cap.value);
    myDiagram.model.setDataProperty(selectedLink.data, 'util',  lf.util.value ? parseFloat(lf.util.value) : null);
    myDiagram.model.setDataProperty(selectedLink.data, 'label', lf.label.value);
    myDiagram.model.commitTransaction('link-inspector');
    refreshKPIs();
  });

  // ---------- TOOLBAR ----------
  const btnNew  = document.getElementById('btnNew');
  const btnSave = document.getElementById('btnSave');
  const btnOpen = document.getElementById('btnOpen');
  const fileInput = document.getElementById('fileInput');
  const btnCopyJson = document.getElementById('btnCopyJson');

  if (btnNew) btnNew.addEventListener('click', () => {
    if (!confirm('Start a new diagram? Unsaved changes will be lost.')) return;
    myDiagram.model = new go.GraphLinksModel([], []);
    refreshKPIs();
  });

  if (btnSave) btnSave.addEventListener('click', () => {
    const json = myDiagram.model.toJson();
    const ta = document.getElementById('jsonOutput');
    if (ta) {
      ta.value = JSON.stringify(JSON.parse(json), null, 2);
      if (window.jQuery && $('#saveJsonModal').modal) $('#saveJsonModal').modal('show');
      else alert('Copy your JSON from console'); // fallback
    } else {
      console.log(json);
      alert('JSON printed to console');
    }
  });

  if (btnCopyJson) btnCopyJson.addEventListener('click', async () => {
    const ta = document.getElementById('jsonOutput');
    if (!ta) return;
    ta.select(); ta.setSelectionRange(0, ta.value.length);
    try { await navigator.clipboard.writeText(ta.value); } catch(e){}
  });

  if (btnOpen && fileInput) btnOpen.addEventListener('click', () => fileInput.click());
  if (fileInput) fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = () => {
      try {
        const obj = JSON.parse(reader.result);
        myDiagram.model = go.Model.fromJson(obj);
        refreshKPIs();
      } catch(err) { alert('Invalid JSON'); }
    };
    reader.readAsText(file);
    e.target.value = '';
  });

  // Export images
  const btnPng = document.getElementById('btnPng');
  const btnSvg = document.getElementById('btnSvg');

  if (btnPng) btnPng.addEventListener('click', () => {
    const png = myDiagram.makeImageData({ background: dark ? '#0f1113' : '#ffffff', scale: 1, type: 'image/png' });
    downloadDataUrl(png, 'network.png');
  });
  if (btnSvg) btnSvg.addEventListener('click', () => {
    const svg = myDiagram.makeSvg({ scale: 1, background: dark ? '#0f1113' : '#ffffff' });
    const blob = new Blob([svg.outerHTML], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    downloadUrl(url, 'network.svg');
    setTimeout(() => URL.revokeObjectURL(url), 2000);
  });
  function downloadDataUrl(dataUrl, filename){
    const a = document.createElement('a'); a.href = dataUrl; a.download = filename; a.click();
  }
  function downloadUrl(url, filename){
    const a = document.createElement('a'); a.href = url; a.download = filename; a.click();
  }

  // Zoom & layout
  const btnZoomIn  = document.getElementById('btnZoomIn');
  const btnZoomOut = document.getElementById('btnZoomOut');
  const btnResetZoom = document.getElementById('btnResetZoom');
  const btnFit     = document.getElementById('btnFit');
  const btnLayout  = document.getElementById('btnLayout');

  if (btnZoomIn)  btnZoomIn.addEventListener('click', () => myDiagram.commandHandler.increaseZoom());
  if (btnZoomOut) btnZoomOut.addEventListener('click', () => myDiagram.commandHandler.decreaseZoom());
  if (btnResetZoom) btnResetZoom.addEventListener('click', () => myDiagram.commandHandler.zoomToFit());
  if (btnFit) btnFit.addEventListener('click', () => myDiagram.commandHandler.zoomToFit());

  if (btnLayout) btnLayout.addEventListener('click', () => {
    myDiagram.startTransaction('layout');
    myDiagram.layout = $(go.LayeredDigraphLayout, { direction: 0, layerSpacing: 40, columnSpacing: 24 });
    myDiagram.commitTransaction('layout');
    setTimeout(() => {
      myDiagram.layout = $(go.ForceDirectedLayout, { defaultSpringLength: 60, defaultElectricalCharge: 100 });
    }, 0);
  });

  // Snap grid
  const btnSnap = document.getElementById('btnSnap');
  myDiagram.grid = $(go.Panel, 'Grid',
    $(go.Shape, 'LineH', { stroke: 'rgba(100,116,139,.25)', strokeWidth: 0.5 }),
    $(go.Shape, 'LineV', { stroke: 'rgba(100,116,139,.25)', strokeWidth: 0.5 })
  );
  myDiagram.grid.visible = false;
  let snapOn = false;
  if (btnSnap) btnSnap.addEventListener('click', () => {
    snapOn = !snapOn;
    myDiagram.grid.visible = snapOn;
    myDiagram.toolManager.draggingTool.isGridSnapEnabled = snapOn;
  });

  // Quick Templates
  const btnTemplates = document.getElementById('btnTemplates');
  if (btnTemplates) btnTemplates.addEventListener('click', () => {
    const ans = prompt('Template: star / ring / pop?','star');
    if (!ans) return;
    if (ans.toLowerCase() === 'star') addStarTemplate();
    else if (ans.toLowerCase() === 'ring') addRingTemplate();
    else if (ans.toLowerCase() === 'pop') addPopTemplate();
  });

  function addStarTemplate(){
    const m = myDiagram.model; m.startTransaction('tpl-star');
    const hub = m.addNodeData({ device:'switch', name:'Agg-SW', ip:'10.10.0.254', status:'up', lat:2.9, loc:'0 -120' });
    const c1  = m.addNodeData({ device:'router', name:'Edge-R1', ip:'10.10.0.1', status:'up', lat:3.1, loc:'-160 40' });
    const c2  = m.addNodeData({ device:'router', name:'Edge-R2', ip:'10.10.0.2', status:'up', lat:3.3, loc:'0 60' });
    const c3  = m.addNodeData({ device:'router', name:'Edge-R3', ip:'10.10.0.3', status:'warn', lat:4.8, loc:'160 40' });
    m.addLinkData({ from:m.getKeyForNodeData(hub), to:m.getKeyForNodeData(c1), cap:'10G', util:55, label:'Core-1' });
    m.addLinkData({ from:m.getKeyForNodeData(hub), to:m.getKeyForNodeData(c2), cap:'10G', util:61, label:'Core-2' });
    m.addLinkData({ from:m.getKeyForNodeData(hub), to:m.getKeyForNodeData(c3), cap:'10G', util:79, label:'Core-3' });
    m.commitTransaction('tpl-star'); refreshKPIs();
  }
  function addRingTemplate(){
    const m = myDiagram.model; m.startTransaction('tpl-ring');
    const n1 = m.addNodeData({ device:'pop', name:'POP-A', ip:'10.20.0.1', status:'up',   lat:7.2, loc:'-160 -20' });
    const n2 = m.addNodeData({ device:'pop', name:'POP-B', ip:'10.20.0.2', status:'up',   lat:7.8, loc:'0 -80' });
    const n3 = m.addNodeData({ device:'pop', name:'POP-C', ip:'10.20.0.3', status:'warn', lat:10.2,loc:'160 -20' });
    const n4 = m.addNodeData({ device:'pop', name:'POP-D', ip:'10.20.0.4', status:'up',   lat:7.5, loc:'0 60' });
    const k1 = m.getKeyForNodeData(n1), k2 = m.getKeyForNodeData(n2), k3 = m.getKeyForNodeData(n3), k4 = m.getKeyForNodeData(n4);
    m.addLinkData({ from:k1, to:k2, cap:'10G', util:42, label:'Ring-A' });
    m.addLinkData({ from:k2, to:k3, cap:'10G', util:68, label:'Ring-B' });
    m.addLinkData({ from:k3, to:k4, cap:'10G', util:83, label:'Ring-C' });
    m.addLinkData({ from:k4, to:k1, cap:'10G', util:37, label:'Ring-D' });
    m.commitTransaction('tpl-ring'); refreshKPIs();
  }
  function addPopTemplate(){
    const m = myDiagram.model; m.startTransaction('tpl-pop');
    const o = m.addNodeData({ device:'olt', name:'OLT-1', ip:'10.30.0.10', status:'up', lat: 3.9, loc:'-80 120' });
    const s = m.addNodeData({ device:'switch', name:'Access-SW', ip:'10.30.0.2', status:'up', lat: 2.7, loc:'80 120' });
    const u1= m.addNodeData({ device:'onu', name:'ONU-A', ip:'10.30.1.1', status:'up', lat: 5.1, loc:'-140 200' });
    const u2= m.addNodeData({ device:'onu', name:'ONU-B', ip:'10.30.1.2', status:'down', lat: 0.0, loc:'20 200' });
    const u3= m.addNodeData({ device:'client', name:'Client-C', ip:'10.30.1.50', status:'up', lat: 11.4, loc:'180 200' });
    m.addLinkData({ from:m.getKeyForNodeData(o), to:m.getKeyForNodeData(s), cap:'10G', util:21, label:'OLT-Uplink' });
    m.addLinkData({ from:m.getKeyForNodeData(o), to:m.getKeyForNodeData(u1), cap:'1G', util:12, label:'PON-1' });
    m.addLinkData({ from:m.getKeyForNodeData(o), to:m.getKeyForNodeData(u2), cap:'1G', util:0,  label:'PON-2' });
    m.addLinkData({ from:m.getKeyForNodeData(s), to:m.getKeyForNodeData(u3), cap:'1G', util:33, label:'Access' });
    m.commitTransaction('tpl-pop'); refreshKPIs();
  }

  // Search
  const searchInput = document.getElementById('searchInput');
  const btnClear = document.getElementById('btnClearSearch');
  function doSearch() {
    const q = (searchInput && searchInput.value || '').toLowerCase().trim();
    let first = null;
    myDiagram.clearHighlighteds();
    if (!q) return;
    myDiagram.nodes.each(n => {
      const d = n.data || {};
      const hay = [(d.name||''), (d.ip||''), (d.device||'')].join(' ').toLowerCase();
      if (hay.includes(q)) { n.isHighlighted = true; if (!first) first = n; }
    });
    if (first) myDiagram.centerRect(first.actualBounds);
  }
  if (searchInput) searchInput.addEventListener('input', doSearch);
  if (btnClear) btnClear.addEventListener('click', () => { if (searchInput) searchInput.value=''; myDiagram.clearHighlighteds(); });

  // Selection adornments
  const selAdornment = $(go.Shape, 'RoundedRectangle', { fill:null, stroke: '#2563eb', strokeWidth:2 });
  myDiagram.nodeTemplate.selectionAdornmentTemplate = $(go.Adornment, 'Auto', selAdornment, $(go.Placeholder, { margin:2 }));
  myDiagram.linkTemplate.selectionAdornmentTemplate = $(go.Adornment, 'Link',
    $(go.Shape, { isPanelMain: true, stroke:'#2563eb', strokeWidth:2 })
  );

  // Context menu actions (placeholder)
  function openTool(d, kind){
    const ip = d && d.ip ? d.ip : '';
    if (kind === 'ping') alert('Ping ' + (ip||d.name||'device'));
    if (kind === 'ssh')  window.open('ssh://' + (ip||''), '_self');
    if (kind === 'http') window.open('http://' + (ip||''), '_blank');
  }

  // ---------- KPI / SIDE PANELS ----------
  function refreshKPIs(){
    const el = id => document.getElementById(id);
    let up=0, warn=0, down=0, devices=0, links=0, utilSum=0, utilCnt=0, latSum=0, latCnt=0, capTotalGbps=0;

    myDiagram.nodes.each(n=>{
      devices++;
      const st = n.data.status;
      if (st==='up') up++; else if (st==='warn') warn++; else if (st==='down') down++;
      const lat = n.data.lat;
      if (typeof lat === 'number' && !isNaN(lat)) { latSum+=lat; latCnt++; }
    });

    myDiagram.links.each(l=>{
      links++;
      const u = l.data.util;
      if (typeof u === 'number' && !isNaN(u)) { utilSum+=u; utilCnt++; }
      const cap = (l.data.cap||'').toUpperCase();
      if (cap.endsWith('G')) capTotalGbps += parseFloat(cap) || 0;
      if (cap.endsWith('M')) capTotalGbps += (parseFloat(cap) || 0) / 1000.0;
    });

    if (el('kpiDevices')) el('kpiDevices').textContent = devices + ' Devices';
    if (el('kpiUp')) el('kpiUp').textContent = up + ' up';
    if (el('kpiWarn')) el('kpiWarn').textContent = warn + ' warn';
    if (el('kpiDown')) el('kpiDown').textContent = down + ' down';
    if (el('kpiLinks')) el('kpiLinks').textContent = links + ' Links';
    if (el('kpiAvgUtil')) el('kpiAvgUtil').textContent = (utilCnt? (utilSum/utilCnt).toFixed(0):0) + '%';
    if (el('kpiLatency')) el('kpiLatency').textContent = (latCnt? (latSum/latCnt).toFixed(1):'—') + (latCnt? ' ms':'');
    if (el('kpiTraffic')) el('kpiTraffic').textContent = capTotalGbps.toFixed(1) + ' Gbps';

    // Top interfaces table (by utilization)
    const tb = el('tblPorts');
    if (tb){
      const rows = [];
      myDiagram.links.each(l=>{
        const d = l.data;
        rows.push({
          name: d.label || ((l.fromNode?.data?.name || 'A') + ' → ' + (l.toNode?.data?.name || 'B')),
          util: typeof d.util==='number'? d.util : 0,
          cap:  d.cap || ''
        });
      });
      rows.sort((a,b)=>b.util-a.util);
      const top = rows.slice(0,6);
      tb.innerHTML = '';
      top.forEach(r=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="small">${escapeHtml(r.name||'Link')}</td>
          <td style="width:55%;">
            <div class="progress" title="${r.util}%">
              <div class="progress-bar" role="progressbar" style="width:${r.util}%"></div>
            </div>
          </td>
          <td class="small text-right">${r.cap}</td>
        `;
        tb.appendChild(tr);
      });
    }

    // Alerts mock (down + high util)
    const list = el('alertList');
    if (list){
      const alerts = [];
      myDiagram.nodes.each(n=>{
        if (n.data.status==='down') alerts.push({ level:'critical', text: n.data.name + ' is DOWN' });
        if (n.data.status==='warn') alerts.push({ level:'warning', text: n.data.name + ' degraded' });
      });
      myDiagram.links.each(l=>{
        if ((l.data.util||0) >= 80) alerts.push({ level:'warning', text: (l.data.label||'Link') + ' util ' + l.data.util + '%' });
      });
      list.innerHTML = alerts.length ? '' : '<span class="nd-sub">No recent alerts</span>';
      alerts.slice(0,8).forEach(a=>{
        const color = a.level==='critical' ? 'text-danger' : 'text-warning';
        const div = document.createElement('div');
        div.className = 'mb-1';
        div.innerHTML = `<i class="fas fa-exclamation-triangle ${color} mr-1"></i> <span>${escapeHtml(a.text)}</span>`;
        list.appendChild(div);
      });
    }
  }

  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

  // Initial hydrate
  applyTheme();
  refreshKPIs();

})();
</script>
@endsection
