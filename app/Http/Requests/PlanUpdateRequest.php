<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('plans.update') ?? false;
    }

    public function rules(): array
    {
        $ownerId = $this->user()->id;
        $planId  = $this->route('plan')?->id; // route model bound

        return [
            'name'           => [
                'required','string','max:255',
                Rule::unique('plans','name')
                    ->where(fn($q)=>$q->where('owner_id', $ownerId))
                    ->ignore($planId),
            ],
            'description'    => ['nullable','string','max:2000'],
            'price'          => ['required','numeric','min:0'],
            'billing_cycle'  => ['required','in:daily,weekly,monthly'],
            'active'         => ['nullable','boolean'],
            // add other plan fields here…
        ];
    }
}
