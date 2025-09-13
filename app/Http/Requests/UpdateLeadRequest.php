<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads.update') ?? false;
    }

    public function rules(): array
    {
        // Same as store; we don’t allow owner_id to be mass assigned
        return (new StoreLeadRequest())->rules();
    }
}
