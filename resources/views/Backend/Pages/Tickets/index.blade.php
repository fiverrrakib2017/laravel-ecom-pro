@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        @php
                            $dashboardCards = [
                                [
                                    'id' => 1,
                                    'title' => 'Total Tickets',
                                    'value' => $tickets ?? 0,
                                    'bg' => 'indigo',
                                    'icon' => 'fas fa-ticket-alt',
                                    'url' => route('admin.tickets.index') ?? null,
                                ],
                                [
                                    'id' => 2,
                                    'title' => 'Pending Tickets',
                                    'value' => $ticket_pending ?? 0,
                                    'bg' => 'danger',
                                    'icon' => 'fas fa-exclamation-triangle',
                                    'url' => route('admin.tickets.index', ['status' => 'pending']) ?? null,
                                ],
                                [
                                    'id' => 3,
                                    'title' => 'Completed Tickets',
                                    'value' => $ticket_completed ?? 0,
                                    'bg' => 'success',
                                    'icon' => 'fas fa-check-circle',
                                    'url' => route('admin.tickets.index', ['status' => 'completed']) ?? null,
                                ],
                            ];
                        @endphp

                        @foreach ($dashboardCards as $i => $card)
                            <div class="col-xl-4 col-md-6 col-12 mb-3 card-item wow animate__animated animate__fadeInUp"
                                data-id="{{ $card['id'] }}" data-wow-delay=".{{ $i + 1 }}s">

                                @php $href = $card['url'] ?? 'javascript:void(0)'; @endphp
                                <a href="{{ $href }}" class="text-reset d-block">
                                    <div class="small-box bg-gradient-{{ $card['bg'] }} small-box-pro shadow-sm">
                                        <div class="inner">
                                            <p class="small-box-title mb-1">{{ $card['title'] }}</p>
                                            <h3 class="small-box-number" data-count="{{ (int) ($card['value'] ?? 0) }}">
                                                {{ number_format((int) ($card['value'] ?? 0)) }}
                                            </h3>
                                        </div>
                                        <div class="icon">
                                            <i class="{{ $card['icon'] }}"></i>
                                        </div>

                                        @if (!empty($card['url']))
                                            <div class="small-box-footer">
                                                View details <i class="fas fa-arrow-circle-right ml-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card-body" style="padding: 0.25rem !important;">
                    <button data-toggle="modal" data-target="#ticketModal" type="button" class=" btn btn-primary mb-2"><i
                            class="mdi mdi-account-plus"></i>
                        Add New Ticket</button>


                    <div class="table-responsive" id="tableStyle">
                        @include('Backend.Component.Tickets.Tickets')
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('Backend.Modal.Tickets.ticket_modal')
    @include('Backend.Modal.delete_modal')


@endsection
