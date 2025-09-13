<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\EquipmentType;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('equipment.create'); }

    public function rules(): array
    {
        return [
            'user_id'       => ['nullable','exists:users,id'],
            'type'          => ['required','in:'.implode(',', EquipmentType::options())],
            'name'          => ['required','string','max:255'],
            'serial_number' => ['nullable','string','max:255'],
            'price'         => ['required','numeric','min:0'],
            'paid_amount'   => ['nullable','numeric','min:0'],
            'currency'      => ['required','string','max:8'],
            'notes'         => ['nullable','string','max:2000'],
        ];
    }
}
