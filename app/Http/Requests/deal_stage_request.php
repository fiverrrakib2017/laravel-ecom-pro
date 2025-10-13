<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class deal_stage_request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['bail','required','string','max:50','unique:deal_stages,name'],
            'is_won'  => ['string'],
            'is_lost' => ['string'],
        ];
    }
}
