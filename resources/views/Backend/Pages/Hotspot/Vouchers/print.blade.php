@extends('Backend.Layout.App')
@section('title','Print Vouchers | Admin Panel')

@section('style')
<style>
@media print { .no-print{ display:none !important; } body{ background:#fff !important; } }
.cardlike{border:1px dashed #ccc;border-radius:10px;padding:10px;margin:6px}
.grid{display:grid;grid-template-columns:repeat(3,1fr);grid-gap:6px}
.title{font-weight:700}
.mono{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;}
.small{font-size:.85rem}
hr{margin:.4rem 0}
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="no-print mb-3 d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h4 mb-1">Print: {{ $batch->name }}</h1>
        <small class="text-muted">Router: {{ $batch->router->name ?? ('#'.$batch->router_id) }} |
          Profile: {{ $batch->profile->name ?? ('#'.$batch->hotspot_profile_id) }} |
          Qty: {{ $batch->qty }}</small>
      </div>
      <div>
        <a href="{{ route('admin.hotspot.vouchers.batch.index') }}" class="btn btn-secondary"><i class="fas fa-list"></i> All Batches</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
      </div>
    </div>

    <div class="grid">
      @foreach($vouchers as $v)
        @php
          $meta = json_decode($v->meta ?? '[]', true);
          $pw   = $meta['password_plain_preview'] ?? '******';
        @endphp
        <div class="cardlike">
          <div class="title">Wi-Fi Voucher</div>
          <div class="small">{{ $batch->name }}</div>
          <hr>
          <div class="mono">User: <strong>{{ $v->username }}</strong></div>
          <div class="mono">Pass: <strong>{{ $pw }}</strong></div>
          @if($batch->expires_at)
            <div class="small">Batch Expires: {{ $batch->expires_at->format('Y-m-d H:i') }}</div>
          @endif
          <hr>
          <div class="small">Profile: {{ $batch->profile->name ?? '' }}</div>
          <div class="small">Router: {{ $batch->router->name ?? '' }}</div>
          <div class="small">Status: {{ ucfirst($v->status) }}</div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
