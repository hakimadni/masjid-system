<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SlaughteringStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'animal_id' => ['required', 'exists:animals,id'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'cut_time' => ['nullable', 'date'],
            'meat_total_kg' => ['nullable', 'numeric', 'min:0'],
            'distribution_status' => ['nullable', 'in:pending,in_progress,completed'],
        ];
    }
}
