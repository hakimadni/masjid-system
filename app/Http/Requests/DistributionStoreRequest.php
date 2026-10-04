<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DistributionStoreRequest extends FormRequest
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
            'animal_id' => ['nullable', 'exists:animals,id'],
            'slaughtering_id' => ['nullable', 'exists:slaughterings,id'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_type' => ['required', 'in:mustahik,penerima'],
            'package_count' => ['required', 'integer', 'min:1'],
            'status' => ['nullable', 'in:pending,delivered'],
        ];
    }
}
