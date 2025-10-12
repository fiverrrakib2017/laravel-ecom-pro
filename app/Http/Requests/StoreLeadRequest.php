<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
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
        ];
    }
}
