@extends('Backend.Layout.App')
@section('title', 'Lead List | Dashboard | Admin Panel')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div class="btn-group">
                <a href="{{ route('admin.customer.lead.create') }}" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Create New Lead
                </a>
            </div>
        </div>


        <!-- Filters -->
        <div class="card shadow-sm">
            @include('Backend.Component.Common.card-header', [
                    'title' => 'Manage Lead List',
                    'description' => 'Add and manage potential customer information.',
                    'icon' => '<i class="fas fa-tasks"></i>',
                ])
            <div class="card-header">
                <form method="GET" action="" class="form-row">


                    <div class="form-group col-md-4 mb-2">
                        <label for="q" class="mb-1">Search</label>
                        <div class="input-group">
                            <input type="text" id="q" name="q" value="{{ request('q') }}"
                                   placeholder="Search anything" class="form-control">
                            <div class="input-group-append">
                                <button class="btn btn-success"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="mb-1">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="new" {{ request('status')==='new'?'selected':'' }}>New</option>
                            <option value="contacted" {{ request('status')==='contacted'?'selected':'' }}>Contacted</option>
                            <option value="qualified" {{ request('status')==='qualified'?'selected':'' }}>Qualified</option>
                            <option value="unqualified" {{ request('status')==='unqualified'?'selected':'' }}>Unqualified</option>
                            <option value="converted" {{ request('status')==='converted'?'selected':'' }}>Converted</option>
                            <option value="lost" {{ request('status')==='lost'?'selected':'' }}>Lost</option>
                        </select>
                    </div>

                    <div class="form-group col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="thead-light">
                        <tr class="text-nowrap">
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>Priority</th>
                            <th>Interest Level</th>
                            <th>Lead Score</th>
                            <th>Follow-Up Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads  as $i => $p)
                            <tr>
                                <td class="text-muted">{{ ($p->id) }}</td>
                                <td>{{ $p->full_name ?? '' }}</td>
                                <td class="font-weight-bold">{{ $p->phone }}</td>
                                <td >{{ $p->email ?? '' }}</td>
                                <td>
                                    @switch($p->status)
                                        @case('new')
                                            <span class="badge badge-primary"><i class="fas fa-circle mr-1"></i>New</span>
                                            @break
                                        @case('contacted')
                                            <span class="badge badge-info"><i class="fas fa-phone-alt mr-1"></i>Contacted</span>
                                            @break
                                        @case('qualified')
                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Qualified</span>
                                            @break
                                        @case('unqualified')
                                            <span class="badge badge-warning"><i class="fas fa-times-circle mr-1"></i>Unqualified</span>
                                            @break
                                        @case('converted')
                                            <span class="badge badge-success"><i class="fas fa-handshake mr-1"></i>Converted</span>
                                            @break
                                        @case('lost')
                                            <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Lost</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary"><i class="fas fa-pause-circle mr-1"></i>Inactive</span>
                                    @endswitch
                                </td>
                                <td>
                                    @switch($p->source)
                                        @case('facebook')
                                            <span class="badge badge-primary"><i class="fab fa-facebook mr-1"></i>Facebook</span>
                                            @break
                                        @case('referral')
                                            <span class="badge badge-success"><i class="fas fa-share-alt mr-1"></i>Referral</span>
                                            @break
                                        @case('walk_in')
                                            <span class="badge badge-info"><i class="fas fa-walking mr-1"></i>Walk-In</span>
                                            @break
                                        @case('website')
                                            <span class="badge badge-secondary"><i class="fas fa-laptop mr-1"></i>Website</span>
                                            @break
                                        @case('phone_call')
                                            <span class="badge badge-warning"><i class="fas fa-phone-alt mr-1"></i>Phone Call</span>
                                            @break
                                        @case('other')
                                            <span class="badge badge-dark"><i class="fas fa-ellipsis-h mr-1"></i>Other</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary"><i class="fas fa-question-circle mr-1"></i>Unknown</span>
                                    @endswitch
                                </td>

                                <td>
                                    @switch($p->priority)
                                        @case('high')
                                            <span class="badge badge-danger"><i class="fas fa-exclamation-circle mr-1"></i>High</span>
                                            @break
                                        @case('medium')
                                            <span class="badge badge-warning"><i class="fas fa-exclamation-circle mr-1"></i>Medium</span>
                                            @break
                                        @case('low')
                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Low</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary"><i class="fas fa-question-circle mr-1"></i>Unknown</span>
                                    @endswitch
                                </td>

                                <td>
                                    @switch($p->interest_level)
                                        @case('high')
                                            <span class="badge badge-danger"><i class="fas fa-fire mr-1"></i>High</span>
                                            @break
                                        @case('medium')
                                            <span class="badge badge-warning"><i class="fas fa-exclamation-circle mr-1"></i>Medium</span>
                                            @break
                                        @case('low')
                                            <span class="badge badge-success"><i class="fas fa-thumbs-up mr-1"></i>Low</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary"><i class="fas fa-question-circle mr-1"></i>Unknown</span>
                                    @endswitch
                                </td>

                                <td>{{ $p->lead_score }}</td>
                                <td>{{ $p->follow_up_count }}</td>

                                <td class="text-nowrap" id="row-actions-{{ $p->id }}">


                                    <button type="button"
                                            class="btn btn-xs btn-success btn-show"
                                            data-id="{{ $p->id }}"
                                            data-name="{{ $p->full_name }}"
                                            data-url="{{ route('admin.hotspot.profile.destroy', $p->id) }}"
                                            title="Show Details">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <a href="{{ route('admin.hotspot.profile.edit', $p->id) }}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-xs btn-danger btn-delete"
                                            data-id="{{ $p->id }}"
                                            data-name="{{ $p->name }}"
                                            data-url="{{ route('admin.customer.lead.delete', $p->id) }}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted p-4">
                                    No profiles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                @if($leads instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="text-muted">
                        <small>
                            Showing <strong>{{ $leads->firstItem() }}–{{ $leads->lastItem() }}</strong>
                            of <strong>{{ $leads->total() }}</strong> item(s)
                        </small>
                    </div>
                    <div>
                        {{ $leads->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @else
                    <div class="text-muted">
                        <small>Showing <strong>{{ $leads->count() }}</strong> item(s)</small>
                    </div>
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
        $(document).on('click', '.btn-delete', function(){
            const id   = $(this).data('id');
            const name = $(this).data('name') || 'this profile';
            const url  = $(this).data('url');

            if (window.Swal && Swal.fire) {
                Swal.fire({
                    title: 'Are you sure?',
                    html: 'You are about to delete <b>'+ name +'</b>.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then(function(result){
                    if(result.isConfirmed){
                        doDelete(url, id);
                    }
                });
            } else {
                if (confirm('Are you sure you want to delete "'+ name +'"?')) {
                    doDelete(url, id);
                }
            }
        });

    function doDelete(url, id){
        let csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: { _token: csrf , id:id},
            success: function(res){
                if(res && res.success){
                    toastr.success(res.message || 'Deleted Successfully');
                    /*------remove row or reload-----*/
                    const $row = $('#row-'+id);
                    if($row.length){ $row.remove(); } else { location.reload(); }
                } else {
                    toastr.warning('Unexpected response.');
                }
            },
            error: function(xhr){
                toastr.error('Could not delete. Please try again.');
            }
        });
    }

    });
</script>
@endsection
