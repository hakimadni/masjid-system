<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminDonationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user || ! $user->hasPermissionTo('donation.create')) {
            return false;
        }

        $status = (string) $this->input('status');

        if ($status === 'confirmed' && ! $user->hasPermissionTo('donation.confirm')) {
            return false;
        }

        if ($status === 'rejected' && ! $user->hasPermissionTo('donation.reject')) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'donor_name' => ['required', 'string', 'max:150'],
            'donation_category_id' => ['nullable', 'integer', 'exists:donation_categories,id'],
            'campaign' => ['nullable', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'in:cash,transfer,qris,other'],
            'status' => ['required', 'in:draft,pending,confirmed,rejected'],
            'donation_date' => ['required', 'date'],
            'is_anonymous' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
