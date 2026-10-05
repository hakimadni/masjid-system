with open('/Users/user/Work/masjid-system/app/Http/Requests/AdminFinanceStoreRequest.php', 'r') as f:
    content = f.read()

content = content.replace(
    "'notes' => ['nullable', 'string', 'max:500'],",
    "'notes' => ['nullable', 'string', 'max:500'],\n            'attachment' => ['nullable', 'file', 'image', 'max:5120'],"
)

with open('/Users/user/Work/masjid-system/app/Http/Requests/AdminFinanceStoreRequest.php', 'w') as f:
    f.write(content)

with open('/Users/user/Work/masjid-system/app/Http/Controllers/Admin/FinanceController.php', 'r') as f:
    content = f.read()

# Add Storage facade
if 'use Illuminate\\Support\\Facades\\Storage;' not in content:
    content = content.replace('use Illuminate\\Http\\Request;', 'use Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Storage;')

# Update store method
old_store_create = """$transaction = FinanceTransaction::query()->create([
            'mosque_id' => $user->mosque_id,
            'finance_account_id' => $validated['finance_account_id'] ?? null,
            'finance_category_id' => $validated['finance_category_id'] ?? null,
            'entry_type' => $validated['entry_type'],
            'title' => $validated['title'],
            'transaction_date' => $validated['transaction_date'],
            'amount' => $validated['amount'],
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'reference_no' => $this->makeReference('FIN'),
            'notes' => $validated['notes'] ?? null,
            'created_by' => $user->id,
            'approved_by' => $validated['status'] === 'approved' ? $user->id : null,
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
        ]);"""

new_store_create = """$attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('finance_proofs', 'public');
        }

        $transaction = FinanceTransaction::query()->create([
            'mosque_id' => $user->mosque_id,
            'finance_account_id' => $validated['finance_account_id'] ?? null,
            'finance_category_id' => $validated['finance_category_id'] ?? null,
            'entry_type' => $validated['entry_type'],
            'title' => $validated['title'],
            'transaction_date' => $validated['transaction_date'],
            'amount' => $validated['amount'],
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'reference_no' => $this->makeReference('FIN'),
            'notes' => $validated['notes'] ?? null,
            'attachment' => $attachmentPath,
            'created_by' => $user->id,
            'approved_by' => $validated['status'] === 'approved' ? $user->id : null,
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
        ]);"""
content = content.replace(old_store_create, new_store_create)

# Update serializeTransaction to include attachment_url
old_serialize = """'category' => $transaction->category?->name ?? '-',
            'account' => $transaction->account?->name ?? '-',
        ];"""
new_serialize = """'category' => $transaction->category?->name ?? '-',
            'account' => $transaction->account?->name ?? '-',
            'attachment_url' => $transaction->attachment_url,
        ];"""
content = content.replace(old_serialize, new_serialize)

with open('/Users/user/Work/masjid-system/app/Http/Controllers/Admin/FinanceController.php', 'w') as f:
    f.write(content)
