@php
  // $stage  => DealStage model instance (new or existing)
  // $mode   => 'create' | 'edit'
@endphp

@csrf
@if($mode === 'edit')
  @method('POST')
@endif

{{-- Name --}}
<div class="form-group">
  <label for="name" class="font-weight-semibold">
    Stage Name <span class="text-danger">*</span>
  </label>
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text"><i class="fas fa-tag"></i></span>
    </div>
    <input
      type="text"
      name="name"
      id="name"
      class="form-control @error('name') is-invalid @enderror"
      placeholder="e.g., New, Qualified, Proposal, Won, Lost"
      maxlength="50"
      value="{{ old('name', $stage->name ?? '') }}"
      required
      autocomplete="off"
    >
  </div>
  <small class="form-text text-muted">Keep it short and consistent (max 50 chars).</small>
</div>

{{-- Status Toggles --}}
<div class="form-row">
  <div class="form-group col-md-6">
    <div class="custom-control custom-switch" data-toggle="tooltip" title="Mark this stage as a winning terminal stage">
      <input
        type="checkbox"
        name="is_won"
        class="custom-control-input"
        id="is_won"
        {{ old('is_won', $stage->is_won ?? '') ? 'checked' : '' }}>
      <label class="custom-control-label" for="is_won">Is Won</label>
    </div>
    <small class="text-muted">Closes the deal as <span class="badge badge-success">Won</span>.</small>
  </div>

  <div class="form-group col-md-6">
    <div class="custom-control custom-switch" data-toggle="tooltip" title="Mark this stage as a losing terminal stage">
      <input
        type="checkbox"
        name="is_lost"
        class="custom-control-input"
        id="is_lost"
        {{ old('is_lost', $stage->is_lost ?? '') ? 'checked' : '' }}>
      <label class="custom-control-label" for="is_lost">Is Lost</label>
    </div>
    <small class="text-muted">Closes the deal as <span class="badge badge-danger">Lost</span>.</small>

  </div>
</div>

<div class="alert alert-info py-2 px-3">
  <i class="fas fa-info-circle mr-1"></i>
  A stage cannot be both <strong>Won</strong> and <strong>Lost</strong> at the same time.
</div>

{{-- Actions --}}
<div class="d-flex justify-content-between align-items-center mt-4">
  <a href="{{ url()->previous() }}" class="btn btn-light border">
    <i class="fas fa-arrow-left mr-1"></i> Back
  </a>
  <div>
    <button type="reset" class="btn btn-outline-secondary mr-2">
      <i class="fas fa-undo-alt mr-1"></i> Reset
    </button>
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save mr-1"></i>
      {{ $mode === 'edit' ? 'Update Stage' : 'Save Stage' }}
    </button>
  </div>
</div>
