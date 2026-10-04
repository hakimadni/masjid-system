<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VolunteerStoreRequest extends FormRequest
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
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'role_type' => ['required', 'in:penyembelih,administrasi,distribusi,dokumentasi'],
            'is_active' => ['nullable', 'boolean'],
            'availabilities' => ['nullable', 'array'],
            'availabilities.*.available_date' => ['required_with:availabilities', 'date'],
            'availabilities.*.start_time' => ['nullable', 'date_format:H:i'],
            'availabilities.*.end_time' => ['nullable', 'date_format:H:i'],
        ];
    }
}
