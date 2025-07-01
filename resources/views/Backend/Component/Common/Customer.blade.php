<option>---Select---</option>
@php

    if (!empty($branch_user_id)) {
        $cacheKey = 'sidebar_customers' . $branch_user_id;
        $customers = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($branch_user_id) {
            return \App\Models\Customer::where('pop_id', $branch_user_id)->latest()->get();
        });
    } else {
        $cacheKey = 'customers_all';
        $customers = Cache::remember($cacheKey, now()->addMinutes(10), function () {
            return \App\Models\Customer::latest()->get();
        });
    }

@endphp

{{-- Check if customers are not empty --}}

@if ($customers->isNotEmpty())
    @foreach ($customers as $item)
        @php
            $status_icon = $item->status == 'online' ? '🟢' : '🔴';
        @endphp

        <option value="{{ $item->id }}">{!! $status_icon !!} [{{ $item->id }}] - {{ $item->username }} ||
            {{ $item->fullname }}, ({{ $item->phone }})</option>
    @endforeach
@else
@endif
