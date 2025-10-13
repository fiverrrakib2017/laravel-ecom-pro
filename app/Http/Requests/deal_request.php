<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class deal_request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'               => 'required|string|max:150',
            'lead_id'             => 'nullable|integer|exists:leads,id',
            'client_id'           => 'nullable|integer|exists:clients,id',
            'stage_id'            => 'required|integer|exists:deal_stages,id',
            'amount'              => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date_format:Y-m-d',
            'user_id'             => 'nullable|integer|exists:admins,id',
        ];
    }
}
