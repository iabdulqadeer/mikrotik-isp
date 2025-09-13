<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'            => ['required','string','max:255'],
            'email'           => ['nullable','email','max:255'],
            'phone'           => ['nullable','string','max:50'],
            'company'         => ['nullable','string','max:255'],
            'address'         => ['nullable','string','max:255'],
            'city'            => ['nullable','string','max:120'],
            'state'           => ['nullable','string','max:120'],
            'postal_code'     => ['nullable','string','max:30'],
            'country'         => ['nullable','string','max:120'],
            'source'          => ['nullable','string','max:120'],
            'status'          => ['required','in:new,contacted,qualified,won,lost'],
            'last_contact_at' => ['nullable','date'],
            'next_follow_up_at' => ['nullable','date','after_or_equal:today'],
            'notes'           => ['nullable','string'],
            // ❌ removed 'owner_id' from validation — we set it in controller
        ];
    }
}
