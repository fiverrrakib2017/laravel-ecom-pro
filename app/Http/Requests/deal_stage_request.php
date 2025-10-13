<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store_deal_stage_request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'    => $this->name ? trim($this->name) : null,
            'is_won'  => $this->boolean('is_won'),
            'is_lost' => $this->boolean('is_lost'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'    => ['bail','required','string','max:50','unique:deal_stages,name'],
            'is_won'  => ['sometimes','boolean'],
            'is_lost' => ['sometimes','boolean'],
        ];
    }

   
    public function after(): array
    {
        return [
            function () {
                if ($this->is_won && $this->is_lost) {
                    $this->errors()->add('is_lost', 'একটি স্টেজ একই সাথে Won এবং Lost হতে পারে না।');
                }
            }
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'স্টেজের নাম দিন।',
            'name.unique'   => 'এই নামের স্টেজ আগেই আছে।',
            'name.max'      => 'স্টেজের নাম সর্বোচ্চ ৫০ অক্ষর হতে পারে।',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'    => 'স্টেজের নাম',
            'is_won'  => 'Won স্ট্যাটাস',
            'is_lost' => 'Lost স্ট্যাটাস',
        ];
    }
}
