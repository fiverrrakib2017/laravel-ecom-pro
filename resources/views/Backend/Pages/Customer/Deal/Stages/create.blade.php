@extends('Backend.Layout.App')
@section('title', 'Deal Stage Create | Dashboard | Admin Panel')

@section('content')
<div class="row">
  <div class="col-md-10 mx-auto">
    <div class="card shadow-sm">
      {{-- Page Header --}}
      @include('Backend.Component.Common.card-header', [
          'title' => 'Create New Deal Stage',
          'description' => 'Create and manage sales pipeline stages (e.g., New, Qualified, Proposal, Won, Lost).',
          'icon' => '<i class="fas fa-layer-group"></i>',
      ])

      <div class="card-body">

        <form method="POST" action="{{ route('admin.customer.deal_stages.store') }}" id="formData" novalidate>
          @csrf
            @include('Backend.Pages.Customer.Deal.Stages._form', ['stage' => $stage, 'mode' => 'create'])
        </form>
      </div>

      <div class="card-footer text-muted small">
        <i class="far fa-lightbulb mr-1"></i>
        Tip: Use <em>Won</em> / <em>Lost</em> only on terminal stages. Others (New, Qualified, Proposal) should keep both off.
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
  <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip();
    });
    document.getElementById('is_won').addEventListener('change', function() {
      if (this.checked) document.getElementById('is_lost').checked = false;
    });
    document.getElementById('is_lost').addEventListener('change', function() {
      if (this.checked) document.getElementById('is_won').checked = false;
    });

    handle_submit_form("#formData");
  </script>
@endsection
