<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('create', \App\Models\Campaign::class); }

    public function rules(): array
    {
        $type = $this->input('type');

        $base = [
            'name'       => ['required','string','max:120'],
            'type'       => ['required','in:banner,image'],
            'start_date' => ['required','date'],
            'end_date'   => ['nullable','date','after_or_equal:start_date'],
        ];

        if ($type === 'banner') {
            $extra = ['banner_text' => ['required','string','max:255']];
        } else {
            $extra = [
                'image_size' => ['required','in:full,wide,square'],
                'image'      => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
            ];
        }

        return array_merge($base, $extra);
    }
}
