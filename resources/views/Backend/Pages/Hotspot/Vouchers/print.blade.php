@extends('Backend.Layout.App')
@section('title','Print Vouchers | Admin Panel')

@section('style')
<style>
@media print { 
  .no-print{ display:none !important; } 
  body{ background:#fff !important; font-family: Arial, sans-serif; color: #333; } 
}

.cardlike {
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 15px;
  margin: 10px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-gap: 20px;
}

.title {
  font-weight: bold;
  font-size: 1.1rem;
  color: #0056b3;
}

.mono {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 1rem;
  color: #555;
}

.small {
  font-size: 0.85rem;
  color: #777;
}

.cardlike .footer {
  margin-top: 10px;
  font-size: 0.9rem;
  color: #333;
}

.cardlike hr {
  margin: 1rem 0;
  border-top: 1px solid #ddd;
}

.cardlike .status {
  color: #28a745;
}

.no-print {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.no-print .btn {
  margin-left: 10px;
}

@media (max-width: 768px) {
  .grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 480px) {
  .grid {
    grid-template-columns: 1fr;
  }
}
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="no-print mb-3">
      <div>
        <h1 class="h4 mb-1">{{ __('Print: ') }} {{ $batch->name }}</h1>
        <small class="text-muted">{{ __('Router: ') }} {{ $batch->router->name ?? ('#'.$batch->router_id) }} | 
          {{ __('Profile: ') }} {{ $batch->profile->name ?? ('#'.$batch->hotspot_profile_id) }} | 
          {{ __('Qty: ') }} {{ $batch->qty }}
        </small>
      </div>
      <div>
        <a href="{{ route('admin.hotspot.vouchers.batch.index') }}" class="btn btn-secondary"><i class="fas fa-list"></i> {{ __('All Batches') }}</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> {{ __('Print') }}</button>
      </div>
    </div>

    <div class="grid">
      @foreach($vouchers as $v)
        @php
            // Check if $v->meta is a JSON string or an array
            $meta = is_string($v->meta) ? json_decode($v->meta, true) : $v->meta;
            $pw = $meta['password_plain_preview'] ?? '******';
        @endphp

        <div class="cardlike">
          <div class="title">{{ __('Wi-Fi Voucher') }}</div>
          <div class="small">{{ $batch->name }}</div>
          <hr>
          <div class="mono">{{ __('User: ') }} <strong>{{ $v->username }}</strong></div>
          <div class="mono">{{ __('Pass: ') }} <strong>{{ $pw }}</strong></div>
          @if($batch->expires_at)
            <div class="small">{{ __('Batch Expires: ') }} {{ $batch->expires_at->format('Y-m-d H:i') }}</div>
          @endif
          <hr>
          <div class="small">{{ __('Profile: ') }} {{ $batch->profile->name ?? '' }}</div>
          <div class="small">{{ __('Router: ') }} {{ $batch->router->name ?? '' }}</div>
          <div class="small {{ $v->status === 'active' ? 'status' : '' }}">{{ __('Status: ') }} {{ ucfirst($v->status) }}</div>
          <div class="footer">
            <small>{{ __('Printed on: ') }} {{ now()->format('Y-m-d H:i:s') }}</small>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
