@extends('Backend.Layout.App')
@section('title','Voucher Batches | Admin Panel')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h3 mb-0">Voucher Batches</h1>
        <small class="text-muted">Manage generated batches</small>
      </div>
      <div class="btn-group">
        <a href="{{ route('admin.hotspot.vouchers.batch.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Generate Batch</a>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header">
        <form method="GET" action="{{ route('admin.hotspot.vouchers.batch.index') }}" class="form-row">
          <div class="form-group col-md-3 mb-2">
            <label class="mb-1">Router</label>
            <select name="router_id" id="router_id" class="form-control">
              <option value="">All</option>
              @foreach($routers as $r)
                <option value="{{ $r->id }}" {{ request('router_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-3 mb-2">
            <label class="mb-1">Profile</label>
            <select name="hotspot_profile_id" id="hotspot_profile_id" class="form-control" {{ request('router_id')?'':'disabled' }}>
              <option value="">All</option>
              @foreach($profiles as $p)
                <option value="{{ $p->id }}" {{ request('hotspot_profile_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-2 mb-2">
            <label class="mb-1">Status</label>
            <select name="status" class="form-control">
              <option value="">All</option>
              @foreach(['draft','generated','pushed','archived'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-3 mb-2">
            <label class="mb-1">Search</label>
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Name / Prefix…" value="{{ request('q') }}">
              <div class="input-group-append">
                <button class="btn btn-secondary"><i class="fas fa-search"></i></button>
              </div>
            </div>
          </div>
          <div class="form-group col-md-1 mb-2 d-flex align-items-end">
            <button class="btn btn-dark w-100"><i class="fas fa-filter"></i></button>
          </div>
        </form>
      </div>

      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped table-bordered mb-0">
          <thead class="thead-light">
            <tr class="text-nowrap">
              <th style="width:60px;">#</th>
              <th>Name</th>
              <th>Router</th>
              <th>Profile</th>
              <th>Qty</th>
              <th>Prefix</th>
              <th>U/P Len</th>
              <th>Validity</th>
              <th>Batch Expiry</th>
              <th>Price</th>
              <th>Status</th>
              <th style="width:200px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($batches as $i => $b)
              <tr>
                <td class="text-muted">{{ $batches->firstItem() + $i }}</td>
                <td class="font-weight-bold">{{ $b->name }}</td>
                <td>{{ optional($b->router)->name ?? 'Router #'.$b->router_id }}</td>
                <td>{{ optional($b->profile)->name ?? '—' }}</td>
                <td>{{ $b->qty }}</td>
                <td>{{ $b->code_prefix ?? '—' }}</td>
                <td>{{ $b->username_length }}/{{ $b->password_length }}</td>
                <td>{{ $b->validity_days_override ? ($b->validity_days_override.' d') : '—' }}</td>
                <td>{{ $b->expires_at ? $b->expires_at->format('Y-m-d H:i') : '—' }}</td>
                <td>৳ {{ number_format(($b->price_minor ?? 0), 2) }}</td>
                <td><span class="badge badge-{{ $b->status==='generated'?'success':($b->status==='draft'?'secondary':'info') }}">{{ ucfirst($b->status) }}</span></td>
                <td class="text-nowrap">
                  <a href="{{ route('admin.hotspot.vouchers.print',['batch_id'=>$b->id]) }}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Print">
                    <i class="fas fa-print"></i>
                  </a>
                  <a href="{{ route('admin.hotspot.vouchers.export',['batch_id'=>$b->id]) }}" class="btn btn-xs btn-success" data-toggle="tooltip" title="Export CSV">
                    <i class="fas fa-file-csv"></i>
                  </a>
                  <a href="{{ route('admin.hotspot.vouchers.sales',['batch_id'=>$b->id]) }}" class="btn btn-xs btn-info" data-toggle="tooltip" title="Sales/Activated">
                    <i class="fas fa-chart-line"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="12" class="text-center text-muted p-4">No batches found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex justify-content-between align-items-center">
        @if($batches instanceof \Illuminate\Pagination\LengthAwarePaginator)
          <small class="text-muted">Showing <strong>{{ $batches->firstItem() }}–{{ $batches->lastItem() }}</strong> of <strong>{{ $batches->total() }}</strong></small>
          {{ $batches->appends(request()->query())->links() }}
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
$(function(){
  $('[data-toggle="tooltip"]').tooltip();
  // auto-refresh profiles when router filter changes
  $('#router_id').on('change', function(){ $(this).closest('form')[0].submit(); });
});
</script>
@endsection
