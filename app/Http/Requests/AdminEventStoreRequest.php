<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminEventStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'location' => ['nullable', 'string', 'max:150'],
            'pic_name' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:draft,published,completed,archived'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
