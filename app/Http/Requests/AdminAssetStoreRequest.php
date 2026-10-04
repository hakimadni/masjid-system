<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAssetStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'in:baik,perlu_perbaikan,rusak'],
            'status' => ['required', 'in:active,maintenance,archived'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
