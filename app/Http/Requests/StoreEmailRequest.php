<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'subject'   => ['required','string','max:255'],
            'message'   => ['required','string'],
            'to_email'  => ['nullable','string'], // allow blank if you’ll inject recipients elsewhere
            'cc'        => ['nullable','string'],
            'bcc'       => ['nullable','string'],
        ];
    }

    public function emailsArray(?string $csv): array
    {
        if (!$csv) return [];
        return collect(preg_split('/[\s,;]+/', $csv))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
