@extends('Backend.Layout.App')
@section('title','Dashboard | Admin Panel')
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
                    'icon' => 'fas fa-solid fa-ticket-alt',
                ],
                [
                    'id' => 2,
                    'title' => 'Pending Tickets',
                    'value' => $ticket_pending ?? 0,
                    'bg' => 'danger',
                    'icon' => 'fas fa-solid fa-exclamation-triangle',
                ],
                [
                    'id' => 3,
                    'title' => 'Completed Tickets',
                    'value' => $ticket_completed ?? 0,
                    'bg' => 'success',
                    'icon' => 'fas fa-solid fa-check-circle',
                ],

            ];
        @endphp
                    @foreach ($dashboardCards as $card)
                        <div class="col-lg-3 col-6 card-item wow animate__animated animate__fadeInUp" data-id="{{ $card['id'] }}"
                            data-wow-delay="0.{{ $card['id'] }}s">
                            <div class="small-box bg-{{ $card['bg'] }}">
                                <div class="inner">
                                    <h3>{{ $card['value'] }}</h3>
                                    <p>{{ $card['title'] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="{{ $card['icon'] }} fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-body" style="padding: 0.25rem !important;">
                    <button data-toggle="modal" data-target="#ticketModal" type="button" class=" btn btn-primary mb-2"><i class="mdi mdi-account-plus"></i>
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
