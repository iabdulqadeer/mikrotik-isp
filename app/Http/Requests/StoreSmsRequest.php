<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmsRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'send_to_all' => ['nullable','boolean'],
            'user_id'     => ['nullable','exists:users,id'],
            'message'     => ['required','string','max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => __('Selected user not found.'),
        ];
    }
}
