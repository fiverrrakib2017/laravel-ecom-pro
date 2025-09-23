@extends('Backend.Layout.App')
@section('title','Sold / Activated | Admin Panel')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h3 mb-0">Sold / Activated</h1>
        <small class="text-muted">Vouchers currently marked as Sold/Activated</small>
      </div>
      <div class="btn-group">
        <a href="{{ route('admin.hotspot.vouchers.batch.index') }}" class="btn btn-default"><i class="fas fa-boxes"></i> Batches</a>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header">
        <form method="GET" action="{{ route('admin.hotspot.vouchers.sales') }}" class="form-row">
          <div class="form-group col-md-3 mb-2">
            <label class="mb-1">Router</label>
            <select name="router_id" class="form-control">
              <option value="">All</option>
              @foreach($routers as $r)
                <option value="{{ $r->id }}" {{ request('router_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-3 mb-2">
            <label class="mb-1">Batch</label>
            <select name="batch_id" class="form-control">
              <option value="">All</option>
              @foreach($batches as $b)
                <option value="{{ $b->id }}" {{ request('batch_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-2 mb-2">
            <label class="mb-1">Status</label>
            <select name="status" class="form-control">
              <option value="">Any</option>
              @foreach(['sold','activated'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-3 mb-2">
            <label class="mb-1">Search</label>
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Username…" value="{{ request('q') }}">
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
              <th>Batch</th>
              <th>Username</th>
              <th>Status</th>
              <th>Activated At</th>
              <th>Expires At</th>
            </tr>
          </thead>
          <tbody>
            @forelse($vouchers as $i => $v)
              <tr>
                <td class="text-muted">{{ $vouchers->firstItem() + $i }}</td>
                <td>{{ optional($v->batch)->name ?? '—' }}</td>
                <td class="font-weight-bold">{{ $v->username }}</td>
                <td><span class="badge badge-{{ $v->status==='activated'?'success':'secondary' }}">{{ ucfirst($v->status) }}</span></td>
                <td>{{ $v->activated_at ? $v->activated_at->format('Y-m-d H:i') : '—' }}</td>
                <td>{{ $v->expires_at ? $v->expires_at->format('Y-m-d H:i') : '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted p-4">No items found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex justify-content-between align-items-center">
        @if($vouchers instanceof \Illuminate\Pagination\LengthAwarePaginator)
          <small class="text-muted">Showing <strong>{{ $vouchers->firstItem() }}–{{ $vouchers->lastItem() }}</strong> of <strong>{{ $vouchers->total() }}</strong></small>
          {{ $vouchers->appends(request()->query())->links() }}
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
