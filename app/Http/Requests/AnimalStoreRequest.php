<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AnimalStoreRequest extends FormRequest
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
            'type' => ['required', 'in:sapi,kambing'],
            'weight' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:1'],
            'supplier' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'slaughter_type' => ['required', 'in:onsite,penjagalan'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'vendor_cost' => ['nullable', 'numeric', 'min:0'],
            'pickup_schedule' => ['nullable', 'date'],
            'status' => ['nullable', 'in:available,assigned,slaughtered,distributed'],
        ];
    }
}
