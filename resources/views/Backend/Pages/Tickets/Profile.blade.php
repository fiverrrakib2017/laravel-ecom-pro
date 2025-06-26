@extends('Backend.Layout.App')
@section('title', 'Dashboard | Ticket Profile | Admin Panel')
@section('style')

@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Ticket Profile - #TID-{{$data->id}}</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h5><strong>Customer Information</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>{{$data->customer->fullname ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{$data->customer->phone ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>POP/Branch</th>
                                    <td>{{$data->pop->name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Area</th>
                                    <td>{{$data->area->name ?? ''}}</td>
                                </tr>
                            </table>

                            <h5 class="mt-4"><strong>Ticket Details</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Complain Type</th>
                                    <td>{{$data->complain_type->name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Assigned To</th>
                                    <td>Technician - {{$data->assign->name ?? ''}}</td>
                                </tr>

                                <tr>
                                    <th>Priority</th>
                                    <td>
                                        @php
                                            $priorityLabel = '';
                                            $badgeColor = '';

                                            switch ($data->priority_id) {
                                                case 1:
                                                    $priorityLabel = 'Low';
                                                    $badgeColor = 'badge-secondary';
                                                    break;
                                                case 2:
                                                    $priorityLabel = 'Normal';
                                                    $badgeColor = 'badge-info';
                                                    break;
                                                case 3:
                                                    $priorityLabel = 'Standard';
                                                    $badgeColor = 'badge-primary';
                                                    break;
                                                case 4:
                                                    $priorityLabel = 'Medium';
                                                    $badgeColor = 'badge-warning';
                                                    break;
                                                case 5:
                                                    $priorityLabel = 'High';
                                                    $badgeColor = 'badge-danger';
                                                    break;
                                                case 6:
                                                    $priorityLabel = 'Very High';
                                                    $badgeColor = 'badge-dark';
                                                    break;
                                                default:
                                                    $priorityLabel = 'Unknown';
                                                    $badgeColor = 'badge-light';
                                                    break;
                                            }
                                        @endphp

                                        <span class="badge {{ $badgeColor }}">{{ $priorityLabel }}</span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($data->status==0)
                                            <span class="badge badge-warning">Open</span>
                                        @else
                                            <span class="badge badge-success">Completed</span>
                                        @endif

                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h5><strong>Problem Description</strong></h5>
                            <div class="callout callout-danger">
                               {{$data->note ?? 'N/A'}}
                            </div>

                            <h5 class="mt-4"><strong>Activity Timeline</strong></h5>

                            <div class="timeline">
                                <!-- timeline time label -->
                                <div class="time-label">
                                  <span class="bg-red">{{ \Carbon\Carbon::parse($data->created_at)->format('d M Y') }}</span>

                                </div>
                                <!-- /.timeline-label -->

                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-ticket-alt bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                        <h3 class="timeline-header">Ticket Created</h3>
                                        <div class="timeline-body">
                                            Customer submitted a ticket for {{$data->complain_type->name ?? ''}}.
                                        </div>
                                    </div>
                                </div>

                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-user-check bg-warning"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                        <h3 class="timeline-header">Assigned to {{$data->assign->name ?? ''}}</h3>
                                        <div class="timeline-body">
                                            Ticket assigned to technician <b>{{$data->assign->name ?? ''}}</b> for investigation.
                                        </div>
                                    </div>
                                </div>

                                <!-- timeline item -->
                                @foreach($ticekts_details as $item)
                                    @if($item->status=='visit')
                                    <div>
                                        <i class="fas fa-map-marker-alt bg-info"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                            <h3 class="timeline-header">Visited Customer Location</h3>
                                            <div class="timeline-body">
                                                Technician <b>{{$data->assign->name ?? ''}}</b> visited the customer premises for issue inspection.
                                            </div>
                                        </div>
                                    </div>
                                    @elseif($item->status=='process')
                                    <div>
                                        <i class="fas fa-tools bg-primary"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                            <h3 class="timeline-header">Work in Progress</h3>
                                            <div class="timeline-body">
                                                Technician <b>{{$data->assign->name ?? ''}}</b> is currently working on resolving the issue.
                                            </div>
                                        </div>
                                    </div>
                                    @elseif($item->status=='completed')
                                    <div>
                                        <i class="fas fa-check-circle bg-success"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($data->created_at)->format('h:i A') }}</span>
                                            <h3 class="timeline-header">Ticket Completed</h3>
                                            <div class="timeline-body">
                                                The ticket has been successfully resolved and closed.
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach

                            </div>


                            <div class="mt-4">
                                <a href="#" class="btn btn-success"><i class="fas fa-check-circle"></i> Close
                                    Ticket</a>
                                <button type="button" onclick="history.back()" class="btn btn-danger"><i class="fas fa-arrow-left"></i> Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
