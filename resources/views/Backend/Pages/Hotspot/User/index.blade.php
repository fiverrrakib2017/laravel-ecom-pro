@extends('Backend.Layout.App')
@section('title', 'Hotspot Users | Admin Panel')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="">
            <!-- Card Header -->
            @include('Backend.Component.Common.card-header', [
                'title' => 'Hotspot Users',
                'description' => 'All users with status, usage & expiry details',
                'icon' => '<i class="fas fa-wifi"></i>',
                'button' => '<button type="button" onclick="window.location=\''.route('admin.hotspot.user.create').'\'" class="btn btn-header">
                    <i class="fas fa-user-plus"></i> Add User
                </button>'

            ])

        </div>
        

        <div class="card shadow-sm">
            <div class="card-header">
                <form method="GET" action="{{ route('admin.hotspot.user.index') }}" class="form-row">
                    <!-- Router -->
                    <div class="form-group col-md-3 mb-2">
                        <label for="router_id" class="mb-1">Router</label>
                        <select name="router_id" id="router_id" class="form-control">
                            <option value="">All</option>
                            @foreach($routers as $router)
                                <option value="{{ $router->id }}" {{ request('router_id')==$router->id?'selected':'' }}>
                                    {{ $router->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Profile (depends on router) -->
                    <div class="form-group col-md-3 mb-2">
                        <label for="hotspot_profile_id" class="mb-1">Profile</label>
                        <select name="hotspot_profile_id" id="hotspot_profile_id" class="form-control" {{ request('router_id') ? '' : 'disabled' }}>
                            <option value="">All</option>
                            @foreach($profiles as $pf)
                                <option value="{{ $pf->id }}" {{ request('hotspot_profile_id')==$pf->id?'selected':'' }}>
                                    {{ $pf->name }} ({{ $pf->mikrotik_profile }})
                                </option>
                            @endforeach
                        </select>
                        @if(!request('router_id'))
                            <small class="text-muted">Select a router to filter profiles</small>
                        @endif
                    </div>

                    <!-- Status -->
                    <div class="form-group col-md-2 mb-2">
                        <label class="mb-1">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            @foreach(['active','disabled','expired','blocked'] as $s)
                                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="form-group col-md-3 mb-2">
                        <label for="q" class="mb-1">Search</label>
                        <div class="input-group">
                            <input type="text" id="q" name="q" value="{{ request('q') }}"
                                   placeholder="Username / MAC / Comment…" class="form-control">
                            <div class="input-group-append">
                                <button class="btn btn-secondary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-1 mb-2 d-flex align-items-center">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </form>

            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="thead-light">
                        <tr class="text-nowrap">
                            <th style="width:60px;">#</th>
                            <th>Router</th>
                            <th>Profile</th>
                            <th>Username</th>
                            <th>MAC Lock</th>
                            <th>Status</th>
                            <th>Expires</th>
                            <th>Last Seen</th>
                            <th>Usage</th>
                            <th>Uptime</th>
                            <th>Comment</th>
                            <th style="width:140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $u)
                            <tr id="row-{{ $u->id }}">
                                <td class="text-muted">{{ $users->firstItem() + $i }}</td>
                                <td>{{ optional($u->router)->name ?? 'Router #'.$u->router_id }}</td>
                                <td>
                                    @if($u->profile)
                                        <span class="font-weight-bold">{{ $u->profile->name }}</span>
                                        <br><code>{{ $u->profile->mikrotik_profile }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $u->username }}</td>
                                <td>{{ $u->mac_lock ?? '—' }}</td>
                                <td>
                                    @php
                                        $badge = [
                                            'active'   => 'success',
                                            'disabled' => 'secondary',
                                            'expired'  => 'warning',
                                            'blocked'  => 'danger',
                                        ][$u->status] ?? 'light';
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">{{ ucfirst($u->status) }}</span>
                                </td>
                                <td>
                                    @if($u->expires_at)
                                        {{ $u->expires_at->format('Y-m-d H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($u->last_seen_at)
                                        {{ $u->last_seen_at->diffForHumans() }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="small">
                                    @php
                                        $upMB = $u->upload_bytes   ? number_format($u->upload_bytes/1048576, 2)   : '0.00';
                                        $dlMB = $u->download_bytes ? number_format($u->download_bytes/1048576, 2) : '0.00';
                                    @endphp
                                    ↑ {{ $upMB }} MB<br>↓ {{ $dlMB }} MB
                                </td>
                                <td class="small">
                                    @php
                                        $secs = (int)($u->uptime_seconds ?? 0);
                                        $d = intdiv($secs, 86400);
                                        $h = intdiv($secs % 86400, 3600);
                                        $m = intdiv($secs % 3600, 60);
                                    @endphp
                                    {{ $d }}d {{ $h }}h {{ $m }}m
                                </td>
                                <td class="text-truncate" style="max-width: 220px;">
                                    {{ $u->comment ?? '—' }}
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.hotspot.user.edit', $u->id) }}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-xs btn-danger btn-delete"
                                        data-id="{{ $u->id }}"
                                        data-name="{{ $u->username }}"
                                        data-url="{{ route('admin.hotspot.user.destroy', $u->id) }}"
                                        title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>

                               
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted p-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <small class="text-muted">
                        Showing <strong>{{ $users->firstItem() }}–{{ $users->lastItem() }}</strong>
                        of <strong>{{ $users->total() }}</strong> item(s)
                    </small>
                    {{ $users->appends(request()->query())->links() }}
                @else
                    <small class="text-muted">Showing <strong>{{ $users->count() }}</strong> item(s)</small>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('#router_id').on('change', function(){
        $(this).closest('form')[0].submit();
    });

    

    $(document).on('click', '.btn-delete', function(){
        const id   = $(this).data('id');
        const name = $(this).data('name') || 'this user';
        const url  = $(this).data('url');

        if (window.Swal && Swal.fire) {
            Swal.fire({
                title: 'Are you sure?',
                html: 'Delete <b>'+ name +'</b>? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function(result){
                if(result.isConfirmed){ doDelete(url, id); }
            });
        } else if (confirm('Delete "'+name+'"?')) {
            doDelete(url, id);
        }
    });

    function doDelete(url, id){
        const csrf = '{{ csrf_token() }}';
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {_method:'DELETE', _token: csrf},
            success: function(res){
                if(res && res.success){
                    toastr.success(res.message || 'Deleted Successfully');
                    const $row = $('#row-'+id);
                    if($row.length){ $row.remove(); } else { location.reload(); }
                }else{
                    toastr.warning('Unexpected response.');
                }
            },
            error: function(){
                toastr.error('Could not delete. Please try again.');
            }
        });
    }

});
</script>
@endsection
