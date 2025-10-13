@extends('Backend.Layout.App')
@section('title', 'Deal Edit | Dashboard | Admin Panel')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @include('Backend.Component.Common.card-header', [
                    'title' => 'Update Deal',
                    'description' => 'Add and manage potential customer information.',
                    'icon' => '<i class="fas fa-user-plus"></i>',
                ])
                <div class="card-body">
                    <form action="{{ route('admin.customer.deals.update',$deal->id ?? '') }}" id="dealForm" method="POST">
                        @csrf
                        @include ('Backend.Pages.Customer.Deal._form',['deal' => $deal, 'mode' => 'edit'])

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script type="text/javascript">
        handle_submit_form("#dealForm");
    </script>
@endsection
