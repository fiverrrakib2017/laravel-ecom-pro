@extends('Backend.Layout.App')
@section('title', 'Hotspot Profile List | Admin Panel')

@section('content')
@php
    // ---------- Demo fallback (only if controller didn't pass real data) ----------
    $routers = $routers ?? collect([
        (object)['id'=>1,'name'=>'Core RB4011'],
        (object)['id'=>2,'name'=>'POP-1 CCR1009'],
        (object)['id'=>3,'name'=>'Branch-Tejgaon hEX S']
    ]);

    $profiles = $profiles ?? collect([
        (object)[
            'id'=>101,'router_id'=>1,'router'=>(object)['name'=>'Core RB4011'],
            'name'=>'Bronze 5 Mbps','mikrotik_profile'=>'HS_BRONZE_5M',
            'rate_limit'=>'5M/5M','shared_users'=>1,
            'idle_timeout'=>'5m','keepalive_timeout'=>'2m','session_timeout'=>'1d',
            'validity_days'=>1,'price_minor'=>50000,'is_active'=>true,'notes'=>null
        ],
        (object)[
            'id'=>102,'router_id'=>2,'router'=>(object)['name'=>'POP-1 CCR1009'],
            'name'=>'Silver 10 Mbps','mikrotik_profile'=>'HS_SILVER_10M',
            'rate_limit'=>'10M/10M','shared_users'=>2,
            'idle_timeout'=>null,'keepalive_timeout'=>'2m','session_timeout'=>null,
            'validity_days'=>7,'price_minor'=>99900,'is_active'=>true,'notes'=>'Weekend promo'
        ],
        (object)[
            'id'=>103,'router_id'=>3,'router'=>(object)['name'=>'Branch-Tejgaon hEX S'],
            'name'=>'Student Pack','mikrotik_profile'=>'HS_STUDENT_3M',
            'rate_limit'=>'3M/3M','shared_users'=>1,
            'idle_timeout'=>'10m','keepalive_timeout'=>null,'session_timeout'=>'12h',
            'validity_days'=>30,'price_minor'=>29900,'is_active'=>false,'notes'=>'Paused'
        ],
    ]);


@endphp

<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-0">Hotspot Profiles</h1>
                <small class="text-muted">Manage MikroTik hotspot plans & timeouts</small>

            </div>
            <div class="btn-group">
                <a href="{{route('admin.hotspot.profile.create')}}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Create Profile
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm">
            <div class="card-header">
                <form method="GET" action="{{ route('admin.hotspot.profile.index') }}" class="form-row">
                    <div class="form-group col-md-3 mb-2">
                        <label for="router_id" class="mb-1">Router</label>
                        <select name="router_id" id="router_id" class="form-control">
                            <option value="">All</option>
                            @foreach($routers as $router)
                                <option value="{{ $router->id }}" {{ request('router_id')==$router->id?'selected':'' }}>
                                    {{ $router->name ?? ('Router #'.$router->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4 mb-2">
                        <label for="q" class="mb-1">Search</label>
                        <div class="input-group">
                            <input type="text" id="q" name="q" value="{{ request('q') }}"
                                   placeholder="Search by name or MikroTik profile…" class="form-control">
                            <div class="input-group-append">
                                <button class="btn btn-success"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="mb-1">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status')==='1'?'selected':'' }}>Active</option>
                            <option value="0" {{ request('status')==='0'?'selected':'' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                    </div>
                </form>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3 mb-0">
                        {!! session('success') !!}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Table -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="thead-light">
                        <tr class="text-nowrap">
                            <th style="width:60px;">#</th>
                            <th>Router</th>
                            <th>Name</th>
                            <th>MikroTik Profile</th>
                            <th>Rate Limit</th>
                            <th>Shared</th>
                            <th>Timeouts</th>
                            <th>Validity</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th style="width:160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profiles as $i => $p)
                            <tr>
                                <td class="text-muted">{{ ($loop->iteration) }}</td>
                                <td>{{ optional($p->router)->name ?? 'Router #'.$p->router_id }}</td>
                                <td class="font-weight-bold">{{ $p->name }}</td>
                                <td><code>{{ $p->mikrotik_profile }}</code></td>
                                <td>{{ $p->rate_limit ?? '—' }}</td>
                                <td class="text-center">{{ $p->shared_users }}</td>
                                <td class="small">
                                    <span data-toggle="tooltip" title="Idle Timeout">Idle:</span> {{ $p->idle_timeout ?? '—' }} |
                                    <span data-toggle="tooltip" title="Keepalive Timeout">Keep:</span> {{ $p->keepalive_timeout ?? '—' }} |
                                    <span data-toggle="tooltip" title="Session Timeout">Sess:</span> {{ $p->session_timeout ?? '—' }}
                                </td>
                                <td>{{ $p->validity_days }} d</td>
                                <td>৳ {{ number_format(($p->price_minor ?? 0)/100, 2) }}</td>
                                <td>
                                    @if($p->is_active)
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Active</span>
                                    @else
                                        <span class="badge badge-secondary"><i class="fas fa-pause-circle mr-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-nowrap" id="row-actions-{{ $p->id }}">
                                    <a href="{{ route('admin.hotspot.profile.edit', $p->id) }}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-xs btn-danger btn-delete"
                                            data-id="{{ $p->id }}"
                                            data-name="{{ $p->name }}"
                                            data-url="{{ route('admin.hotspot.profile.destroy', $p->id) }}"
                                            title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted p-4">
                                    No profiles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer (Pagination placeholder) -->
            <div class="card-footer d-flex justify-content-between align-items-center">
            @if($profiles instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <small class="text-muted">
                    Showing <strong>{{ $profiles->firstItem() }}–{{ $profiles->lastItem() }}</strong>
                    of <strong>{{ $profiles->total() }}</strong> item(s)
                </small>
                {{ $profiles->appends(request()->query())->links() }}
            @else
                <small class="text-muted">
                    Showing <strong>{{ $profiles->count() }}</strong> item(s)
                </small>
            @endif
        </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        // Enable BS tooltips (AdminLTE ships with Bootstrap)
        $('[data-toggle="tooltip"]').tooltip();

        // Optional: auto-submit on status/router change
        // $('#router_id, select[name="status"]').on('change', function(){ $(this).closest('form')[0].submit(); });
    });
</script>
@endsection
