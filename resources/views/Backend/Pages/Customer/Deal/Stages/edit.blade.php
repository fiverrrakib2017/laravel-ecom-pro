@extends('Backend.Layout.App')
@section('title', 'Edit Deal Stage | Dashboard | Admin Panel')

@section('content')
<div class="row">
  <div class="col-md-10 mx-auto">
    <div class="card shadow-sm">
      @include('Backend.Component.Common.card-header', [
          'title' => 'Edit Deal Stage',
          'description' => 'Update the stage details and terminal status.',
          'icon' => '<i class="fas fa-layer-group"></i>',
      ])

      <div class="card-body">
        <form method="POST"
              action="{{ route('admin.customer.deal_stages.update', $stage->id) }}"
              id="formEdit"
              novalidate>
          @include('Backend.Pages.Customer.Deal.Stages._form', ['stage' => $stage, 'mode' => 'edit'])
        </form>
      </div>

      <div class="card-footer text-muted small">
        <i class="far fa-lightbulb mr-1"></i>
        Tip: Use <em>Won</em>/<em>Lost</em> only for terminal stages of the pipeline.
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
  <script>
    $(function () { $('[data-toggle="tooltip"]').tooltip(); });

    const won = document.getElementById('is_won');
    const lost = document.getElementById('is_lost');
    won.addEventListener('change', () => { if (won.checked) lost.checked = false; });
    lost.addEventListener('change', () => { if (lost.checked) won.checked = false; });

    handle_submit_form("#formEdit");
  </script>
@endsection
