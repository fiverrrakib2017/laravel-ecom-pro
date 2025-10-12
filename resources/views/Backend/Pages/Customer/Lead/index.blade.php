@extends('Backend.Layout.App')
@section('title', 'Lead List | Dashboard | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                @include('Backend.Component.Common.card-header', [
                    'title' => 'Manage Lead List',
                    'description' => 'Add and manage potential customer information.',
                    'icon' => '<i class="fas fa-tasks"></i>',
                ])
                <div class="card-body">


                </div>
            </div>

        </div>
    </div>



@endsection
