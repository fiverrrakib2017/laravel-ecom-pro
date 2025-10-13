@csrf
@if (($mode ?? 'create') === 'edit')
    @method('POST')
@endif

<div class="form-group">
    <label for="title" class="font-weight-semibold">Title <span class="text-danger">*</span></label>
    <input type="text" name="title" id="title" class="form-control"
        maxlength="150" placeholder='e.g., "Fiber Internet Package Lead"' value="{{ old('title', $deal->title ?? '') }}"
        required>

</div>

<div class="form-row">
    @php
        use App\Models\Lead;

        $deal = $deal ?? new \App\Models\Deal();

        $leads = Lead::orderBy('full_name')->pluck('full_name', 'id');
    @endphp

    <div class="form-group col-md-6">
        <label for="lead_id">Lead</label>
        <select name="lead_id" id="lead_id" class="form-control select2 ">
            <option value="">— Select Lead (optional) —</option>
            @foreach ($leads as $id => $name)
                <option value="{{ $id }}"
                    {{ (string) old('lead_id', $deal->lead_id) === (string) $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="form-group col-md-6">
        <label for="client_id">Client</label>
        <select name="client_id" id="client_id" class="form-control select2 @error('client_id') is-invalid @enderror">
            <option value="">— Select Client (optional) —</option>
            @php
                use App\Models\Client;

                $client = $client ?? new \App\Models\Client();

                $clients = Client::orderBy('fullname')->pluck('fullname', 'id');
            @endphp
            @foreach ($clients as $id => $name)
                <option value="{{ $id }}"
                    {{ (string) old('client_id', $deal->client_id) === (string) $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>

    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="stage_id">Stage <span class="text-danger">*</span></label>
        <select name="stage_id" id="stage_id" class="form-control select2 @error('stage_id') is-invalid @enderror"
            required>
            <option value="">— Select Stage —</option>
            @php
                use App\Models\Deal_stage;

                $deal = $deal ?? new \App\Models\Deal();

                $deal_stages = Deal_stage::orderBy('name')->pluck('name', 'id');
            @endphp

            @foreach ($deal_stages as $id => $name)
                <option value="{{ $id }}"
                    {{ (string) old('stage_id', $deal->stage_id) === (string) $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>

    </div>

    <div class="form-group col-md-6">
        <label for="amount">Amount</label>
        <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
            </div>
            <input type="number" name="amount" id="amount"
                class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0"
                placeholder="0.00" value="{{ old('amount', $deal->amount ?? 0) }}">
        </div>

    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="expected_close_date">Expected Close Date</label>
        <input type="date" name="expected_close_date" id="expected_close_date"
            class="form-control @error('expected_close_date') is-invalid @enderror"
            value="{{ old('expected_close_date', optional($deal->expected_close_date ?? '')->format('Y-m-d')) }}">

    </div>

</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <button type="button" onclick="history.back();" class="btn btn-light border"><i class="fas fa-arrow-left mr-1"></i> Back</button>
    <div>
        <button type="reset" class="btn btn-outline-danger mr-2"><i class="fas fa-undo mr-1"></i> Reset</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> {{ ($mode ?? 'create') === 'edit' ? 'Update Deal' : 'Save Deal' }}
        </button>
    </div>
</div>
