@extends('Backend.Layout.App')
@section('title', 'Lead Edit | Dashboard | Admin Panel')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @include('Backend.Component.Common.card-header', [
                    'title' => 'Edit Lead',
                    'description' => 'Update and manage potential customer information.',
                    'icon' => '<i class="fas fa-user-plus"></i>',
                ])
                <div class="card-body">
                    <form action="{{ route('admin.customer.lead.update', $lead->id) }}" id="leadForm" method="POST">
                        @csrf
                        @include ('Backend.Pages.Customer.Lead._form')
                        <button type="submit" class="btn btn-success">Update Lead</button>
                        <button type="button" class="btn btn-danger" onclick="history.back();">Back</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script type="text/javascript">
        handle_submit_form("#leadForm");
    </script>
@endsection
