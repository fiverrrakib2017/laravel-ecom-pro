@extends('Backend.Layout.App')
@section('title', 'Deal Stages List | Dashboard | Admin Panel')

@section('content')

    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-end align-items-center mb-3">
                <div class="btn-group">
                    <a href="{{ route('admin.customer.deal_stages.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Create New Deal Stages
                    </a>
                </div>
            </div>


            <div class="card shadow-sm">
                @include('Backend.Component.Common.card-header', [
                    'title' => 'Manage Deal Stages List',
                    'description' => 'Add and manage potential customer information.',
                    'icon' => '<i class="fas fa-tasks"></i>',
                ])

                <!-- Table -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped table-bordered mb-0">
                        <thead class="thead-light">
                            <tr class="text-nowrap">
                                <th style="width: 60px;">#</th>
                                <th>Name</th>
                                <th>Is Won</th>
                                <th>Is Lost</th>
                                <th style="width: 160px;" class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deal_stages as $stage)
                                <tr>
                                    <td>{{ $stage->id }}</td>
                                    <td>{{ $stage->name }}</td>
                                    <td>
                                        @if ($stage->is_won)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($stage->is_lost)
                                            <span class="badge badge-danger">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td class="text-right">

                                    <a href="{{ route('admin.customer.deal_stages.edit', $stage->id) }}" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-xs btn-danger btn-delete"
                                            data-id="{{ $stage->id }}"
                                            data-url="{{ route('admin.customer.deal_stages.delete', $stage->id) }}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No deal stages found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    @if ($deal_stages instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="text-muted">
                            <small>
                                Showing <strong>{{ $deal_stages->firstItem() }}–{{ $deal_stages->lastItem() }}</strong>
                                of <strong>{{ $deal_stages->total() }}</strong> item(s)
                            </small>
                        </div>
                        <div>
                            {{ $deal_stages->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="text-muted">
                            <small>Showing <strong>{{ $deal_stages->count() }}</strong> item(s)</small>
                        </div>
                    @endif
                </div>



            </div>
        </div>
    </div>

    <!-- Modal for showing lead details -->
    <div class="modal fade" id="leadDetailModal" tabindex="-1" role="dialog" aria-labelledby="leadDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadDetailModalLabel">Lead Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Dynamic Content Will Be Loaded Here -->
                    <div id="lead-details">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const name = $(this).data('name') || 'this profile';
                const url = $(this).data('url');

                if (window.Swal && Swal.fire) {
                    Swal.fire({
                        title: 'Are you sure?',
                        html: 'You are about to delete <b>' + name + '</b>.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'Cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            doDelete(url, id);
                        }
                    });
                } else {
                    if (confirm('Are you sure you want to delete "' + name + '"?')) {
                        doDelete(url, id);
                    }
                }
            });

            function doDelete(url, id) {
                let csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrf,
                        id: id
                    },
                    success: function(res) {
                        if (res && res.success) {
                            toastr.success(res.message || 'Deleted Successfully');
                            /*------remove row or reload-----*/
                            const $row = $('#row-' + id);
                            if ($row.length) {
                                $row.remove();
                            } else {
                                location.reload();
                            }
                        } else {
                            toastr.warning('Unexpected response.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Could not delete. Please try again.');
                    }
                });
            }

            $(document).on('click', '.btn-show', function() {
                var leadId = $(this).data('id');
                var leadName = $(this).data('name');
                var url = $(this).data('url');

                $('#lead-details').html('');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var details = `
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> ${response.data.full_name}</p>
                                    <p><strong>Phone:</strong> ${response.data.phone}</p>
                                    <p><strong>Email:</strong> ${response.data.email}</p>
                                    <p><strong>Status:</strong> ${response.data.status}</p>
                                    <p><strong>Priority:</strong> ${response.data.priority}</p>
                                    <p><strong>Interest Level:</strong> ${response.data.interest_level}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Service Interest:</strong> ${response.data.service_interest}</p>
                                    <p><strong>Feedback:</strong> ${response.data.feedback}</p>
                                    <p><strong>Lead Score:</strong> ${response.data.lead_score}</p>
                                    <p><strong>Follow-Up Required:</strong> ${response.data.follow_up_required ? 'Yes' : 'No'}</p>
                                    <p><strong>Follow-Up Count:</strong> ${response.data.follow_up_count}</p>
                                </div>
                            </div>
                            <p><strong>Internal Notes:</strong> ${response.data.internal_notes}</p>
                            <p><strong>First Contacted At:</strong> ${response.data.first_contacted_at}</p>
                            <p><strong>Last Contacted At:</strong> ${response.data.last_contacted_at}</p>
                            <p><strong>Campaign Source:</strong> ${response.data.campaign_source}</p>
                            <p><strong>Estimated Close Date:</strong> ${response.data.estimated_close_date}</p>
                        `;
                            $('#lead-details').html(details);
                            $('#leadDetailModal').modal('show');
                        } else {
                            $('#lead-details').html('<p>No details found for this lead.</p>');
                            $('#leadDetailModal').modal('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#lead-details').html(
                            '<p>Error loading lead details. Please try again.</p>');
                        $('#leadDetailModal').modal('show');
                    }
                });
            });

        });
    </script>
@endsection
