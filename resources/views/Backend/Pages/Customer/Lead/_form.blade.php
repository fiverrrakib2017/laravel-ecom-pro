
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" placeholder="Enter Full Name" class="form-control"value="{{ old('full_name', $lead->full_name ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label for="phone">Phone</label>
        <input type="text" name="phone" placeholder="Enter Phone" class="form-control"value="{{ old('phone', $lead->phone ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Enter Email" class="form-control" value="{{ old('email', $lead->email ?? '') }}">
    </div>
     {{-- Address --}}
    <div class="col-md-4 mb-3">
        <label for="address">Address</label>
        <textarea id="address" name="address" placeholder="Enter Address" class="form-control">{{ old('address', $lead->address ?? '') }}</textarea>
    </div>

    {{-- Source --}}
    <div class="col-md-4 mb-3">
        <label for="source">Source</label>
        <select name="source" class="form-control">
            <option value="">---Select---</option>
            @foreach(['facebook','referral','walk_in','website','phone_call','other'] as $source)
                <option value="{{ $source }}" {{ old('source', $lead->source ?? '') == $source ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $source)) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Status --}}
    <div class="col-md-4 mb-3">
        <label for="status">Status</label>
        <select name="status" class="form-control">
            <option value="">---Select---</option>
            @foreach(['new','contacted','qualified','unqualified','converted','lost'] as $status)
                <option value="{{ $status }}" {{ old('status', $lead->status ?? '') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
    </div>
     {{-- Priority --}}
    <div class="col-md-4 mb-3">
        <label for="priority">Priority</label>
        <select name="priority" class="form-control">
            <option value="">---Select---</option>
            @foreach(['high','medium','low'] as $priority)
                <option value="{{ $priority }}" {{ old('priority', $lead->priority ?? '') == $priority ? 'selected' : '' }}>
                    {{ ucfirst($priority) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Interest Level --}}
    <div class="col-md-4 mb-3">
        <label for="interest_level">Interest Level</label>
        <select name="interest_level" class="form-control">
            <option value="">---Select---</option>
            @foreach(['high','medium','low'] as $level)
                <option value="{{ $level }}" {{ old('interest_level', $lead->interest_level ?? '') == $level ? 'selected' : '' }}>
                    {{ ucfirst($level) }}
                </option>
            @endforeach
        </select>
    </div>
    {{-- Service Interest --}}
    <div class="col-md-4 mb-3">
        <label for="service_interest">Service Interest</label>
        <input type="text" id="service_interest" name="service_interest"
               placeholder="Enter Service Interest"
               class="form-control" value="{{ old('service_interest', $lead->service_interest ?? '') }}">
    </div>
     {{-- Feedback --}}
    <div class="col-md-4 mb-3">
        <label for="feedback">Feedback</label>
        <textarea id="feedback" name="feedback" placeholder="Enter Feedback" class="form-control">{{ old('feedback', $lead->feedback ?? '') }}</textarea>
    </div>
    {{-- Lead Score --}}
    <div class="col-md-4 mb-3">
        <label for="lead_score">Lead Score</label>
        <input type="number" name="lead_score" placeholder="Enter Lead Score" class="form-control"
               value="{{ old('lead_score', $lead->lead_score ?? 0) }}">
    </div>

    {{-- Estimated Close Date --}}
    <div class="col-md-4 mb-3">
        <label for="estimated_close_date">Estimated Close Date</label>
        <input type="date" name="estimated_close_date" class="form-control"
               value="{{ old('estimated_close_date', isset($lead->estimated_close_date) ? $lead->estimated_close_date->format('Y-m-d') : '') }}">
    </div>
     {{-- Follow Up Required --}}
    <div class="col-md-4 mb-3">
        <label for="follow_up_required">Follow Up Required</label>
        <select name="follow_up_required" class="form-control">
            <option value="0" {{ old('follow_up_required', $lead->follow_up_required ?? 0) == 0 ? 'selected' : '' }}>No</option>
            <option value="1" {{ old('follow_up_required', $lead->follow_up_required ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
        </select>
    </div>

    {{-- First Contacted At --}}
    <div class="col-md-4 mb-3">
        <label for="first_contacted_at">First Contacted At</label>
        <input type="datetime-local" name="first_contacted_at" class="form-control"
               value="{{ old('first_contacted_at', isset($lead->first_contacted_at) ? $lead->first_contacted_at->format('Y-m-d\TH:i') : '') }}">
    </div>

    {{-- Last Contacted At --}}
    <div class="col-md-4 mb-3">
        <label for="last_contacted_at">Last Contacted At</label>
        <input type="datetime-local" name="last_contacted_at" class="form-control"
               value="{{ old('last_contacted_at', isset($lead->last_contacted_at) ? $lead->last_contacted_at->format('Y-m-d\TH:i') : '') }}">
    </div>

    {{-- Campaign Source --}}
    <div class="col-md-4 mb-3">
        <label for="campaign_source">Campaign Source</label>
        <input type="text" name="campaign_source" placeholder="Enter Campaign Source" class="form-control"
               value="{{ old('campaign_source', $lead->campaign_source ?? '') }}">
    </div>
     {{-- Follow Up Count --}}
    <div class="col-md-4 mb-3">
        <label for="follow_up_count">Follow Up Count</label>
        <input type="number" name="follow_up_count" placeholder="Enter Follow Up Count" class="form-control"
               value="{{ old('follow_up_count', $lead->follow_up_count ?? 0) }}">
    </div>

    {{-- Internal Notes --}}
    <div class="col-md-4 mb-3">
        <label for="internal_notes">Internal Notes</label>
        <textarea name="internal_notes" placeholder="Enter Internal Notes" class="form-control">{{ old('internal_notes', $lead->internal_notes ?? '') }}</textarea>
    </div>
</div>
