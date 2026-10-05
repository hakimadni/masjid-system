<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminFinanceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user || ! $user->hasPermissionTo('finance.create')) {
            return false;
        }

        $status = (string) $this->input('status');

        if ($status === 'approved' && ! $user->hasPermissionTo('finance.approve')) {
            return false;
        }

        if ($status === 'rejected' && ! $user->hasPermissionTo('finance.approve')) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'entry_type' => ['required', 'in:income,expense'],
            'finance_account_id' => ['nullable', 'integer', 'exists:finance_accounts,id'],
            'finance_category_id' => ['nullable', 'integer', 'exists:finance_categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,transfer,qris,other'],
            'status' => ['required', 'in:draft,pending,approved,rejected'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'attachment' => ['nullable', 'file', 'image', 'max:5120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->filled('finance_category_id') && $this->filled('entry_type')) {
                $category = \App\Models\FinanceCategory::find($this->finance_category_id);
                if ($category && $category->entry_type !== $this->entry_type) {
                    $validator->errors()->add(
                        'finance_category_id',
                        'Kategori yang dipilih tidak sesuai dengan jenis transaksi (pemasukan/pengeluaran).'
                    );
                }
            }
        });
    }
}
