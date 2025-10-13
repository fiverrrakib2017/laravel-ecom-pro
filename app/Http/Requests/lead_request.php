<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class lead_request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:120',
            'address' => 'nullable|string',
            'source' => 'required|in:facebook,referral,walk_in,website,phone_call,other',
            'status' => 'required|in:new,contacted,qualified,unqualified,converted,lost',
            'priority' => 'required|in:high,medium,low',
            'interest_level' => 'required|in:high,medium,low',
            'service_interest' => 'nullable|string|max:150',
            'feedback' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'estimated_close_date' => 'nullable|date',
            'first_contacted_at' => 'nullable|date',
            'last_contacted_at' => 'nullable|date',
            'campaign_source' => 'nullable|string|max:100',
            'follow_up_count' => 'nullable|integer',
            'internal_notes' => 'nullable|string',
        ];
    }
}
