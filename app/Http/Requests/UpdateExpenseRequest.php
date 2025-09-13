<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('expenses.update'); }

    public function rules(): array
    {
        return [
            'type'           => ['required','string','max:100'],
            'amount'         => ['required','numeric','min:0'],
            'spent_at'       => ['required','date'],
            'payment_method' => ['required','string','max:100'],
            'receipt'        => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:5120'],
            'description'    => ['nullable','string','max:5000'],
        ];
    }
}
