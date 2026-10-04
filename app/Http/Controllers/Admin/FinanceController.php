<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFinanceStoreRequest;
use App\Models\FinanceAccount;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'entry_type' => ['nullable', 'in:income,expense'],
            'status' => ['nullable', 'in:draft,pending,approved,rejected'],
            'category_id' => ['nullable', 'integer'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = $this->financeQuery($mosqueId);

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('reference_no', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        if ($filters['entry_type'] ?? null) {
            $query->where('entry_type', $filters['entry_type']);
        }

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['category_id'] ?? null) {
            $query->where('finance_category_id', $filters['category_id']);
        }

        if ($filters['start_date'] ?? null) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }

        if ($filters['end_date'] ?? null) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }

        $entries = $query->latest('transaction_date')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Finance/Index', [
            'filters' => $filters,
            'financeEntries' => $entries->through(fn (FinanceTransaction $transaction): array => $this->serializeTransaction($transaction)),
            'summary' => $this->summaryPayload($mosqueId),
            'accounts' => $this->accountsPayload($mosqueId),
            'categories' => $this->categoriesPayload($mosqueId),
        ]);
    }

    public function pending(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;

        $entries = $this->financeQuery($mosqueId)
            ->whereIn('status', ['draft', 'pending'])
            ->latest('transaction_date')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Finance/Pending', [
            'financeEntries' => $entries->through(fn (FinanceTransaction $transaction): array => $this->serializeTransaction($transaction)),
            'summary' => [
                'draft_total' => (int) FinanceTransaction::query()->where('mosque_id', $mosqueId)->where('status', 'draft')->count(),
                'pending_total' => (int) FinanceTransaction::query()->where('mosque_id', $mosqueId)->where('status', 'pending')->count(),
                'approval_amount_total' => (float) FinanceTransaction::query()->where('mosque_id', $mosqueId)->whereIn('status', ['draft', 'pending'])->sum('amount'),
            ],
        ]);
    }

    public function show(Request $request, FinanceTransaction $financeTransaction): Response
    {
        $transaction = $this->scopedTransaction($request, $financeTransaction)->load(['category:id,name', 'account:id,name,account_type', 'creator:id,name', 'approver:id,name']);

        return Inertia::render('Admin/Finance/Show', [
            'transaction' => [
                'id' => $transaction->id,
                'reference_no' => $transaction->reference_no,
                'title' => $transaction->title,
                'entry_type' => $transaction->entry_type,
                'status' => $transaction->status,
                'amount' => (float) $transaction->amount,
                'payment_method' => $transaction->payment_method,
                'transaction_date' => optional($transaction->transaction_date)->toDateString(),
                'notes' => $transaction->notes,
                'rejected_reason' => $transaction->rejected_reason,
                'category' => $transaction->category?->name,
                'account' => $transaction->account?->name,
                'account_type' => $transaction->account?->account_type,
                'creator' => $transaction->creator?->name,
                'approver' => $transaction->approver?->name,
                'approved_at' => optional($transaction->approved_at)?->format('Y-m-d H:i'),
                'can_edit' => in_array($transaction->status, ['draft', 'pending'], true),
                'can_delete' => $transaction->status !== 'approved',
            ],
        ]);
    }

    public function edit(Request $request, FinanceTransaction $financeTransaction): Response
    {
        $transaction = $this->scopedTransaction($request, $financeTransaction);
        $this->ensureMutable($transaction);

        return Inertia::render('Admin/Finance/Edit', [
            'transaction' => [
                'id' => $transaction->id,
                'title' => $transaction->title,
                'entry_type' => $transaction->entry_type,
                'finance_account_id' => $transaction->finance_account_id,
                'finance_category_id' => $transaction->finance_category_id,
                'amount' => (float) $transaction->amount,
                'payment_method' => $transaction->payment_method,
                'status' => $transaction->status,
                'transaction_date' => optional($transaction->transaction_date)->toDateString(),
                'notes' => $transaction->notes,
                'reference_no' => $transaction->reference_no,
            ],
            'accounts' => $this->accountsPayload($request->user()->mosque_id),
            'categories' => $this->categoriesPayload($request->user()->mosque_id),
        ]);
    }

    public function store(AdminFinanceStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

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
            'created_by' => $user->id,
            'approved_by' => $validated['status'] === 'approved' ? $user->id : null,
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
        ]);

        $this->auditLogService->log('finance.created', $transaction, null, $transaction->toArray());

        return back()->with('success', "Transaksi {$transaction->reference_no} berhasil disimpan.");
    }

    public function update(AdminFinanceStoreRequest $request, FinanceTransaction $financeTransaction): RedirectResponse
    {
        $transaction = $this->scopedTransaction($request, $financeTransaction);
        $this->ensureMutable($transaction);

        $validated = $request->validated();
        $before = $transaction->toArray();

        $updateData = [
            'finance_account_id' => $validated['finance_account_id'] ?? null,
            'finance_category_id' => $validated['finance_category_id'] ?? null,
            'entry_type' => $validated['entry_type'],
            'title' => $validated['title'],
            'transaction_date' => $validated['transaction_date'],
            'amount' => $validated['amount'],
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($validated['status'] === 'approved') {
            $updateData['approved_by'] = $request->user()->id;
            $updateData['approved_at'] = now();
            $updateData['rejected_reason'] = null;
        } elseif ($validated['status'] === 'rejected') {
            $updateData['approved_by'] = null;
            $updateData['approved_at'] = null;
        } else {
            $updateData['approved_by'] = null;
            $updateData['approved_at'] = null;
        }

        $transaction->update($updateData);

        $this->auditLogService->log('finance.updated', $transaction, $before, $transaction->fresh()?->toArray());

        return redirect()->route('finance.show', $transaction)->with('success', 'Transaksi keuangan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, FinanceTransaction $financeTransaction): RedirectResponse
    {
        $transaction = $this->scopedTransaction($request, $financeTransaction);

        $validated = $request->validate([
            'status' => ['required', 'in:draft,pending,approved,rejected'],
            'rejected_reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        abort_if(
            ($validated['status'] === 'approved' && ! $user->hasPermissionTo('finance.approve')) ||
            ($validated['status'] === 'rejected' && ! $user->hasPermissionTo('finance.reject')),
            403,
            'Anda tidak memiliki izin untuk mengubah status ini.'
        );

        $updateData = [
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'approved') {
            $updateData['approved_by'] = $request->user()->id;
            $updateData['approved_at'] = now();
            $updateData['rejected_reason'] = null;
        } elseif ($validated['status'] === 'rejected') {
            $updateData['rejected_reason'] = $validated['rejected_reason'];
            $updateData['approved_by'] = null;
            $updateData['approved_at'] = null;
        } else {
            $updateData['approved_by'] = null;
            $updateData['approved_at'] = null;
        }

        $before = $transaction->toArray();
        $transaction->update($updateData);

        $this->auditLogService->log('finance.status-updated', $transaction, $before, $transaction->fresh()?->toArray());

        return back()->with('success', 'Status transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, FinanceTransaction $financeTransaction): RedirectResponse
    {
        $transaction = $this->scopedTransaction($request, $financeTransaction);

        if ($transaction->status === 'approved') {
            return back()->with('error', 'Transaksi approved tidak boleh dihapus.');
        }

        $before = $transaction->toArray();
        $referenceNo = $transaction->reference_no;
        $transaction->delete();

        $this->auditLogService->log('finance.deleted', $transaction, $before, null);

        return redirect()->route('finance.index')->with('success', "Transaksi {$referenceNo} berhasil dihapus.");
    }

    private function financeQuery(int $mosqueId)
    {
        return FinanceTransaction::query()
            ->with(['category:id,name', 'account:id,name'])
            ->where('mosque_id', $mosqueId);
    }

    private function accountsPayload(int $mosqueId)
    {
        return FinanceAccount::query()
            ->where('mosque_id', $mosqueId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'account_type']);
    }

    private function categoriesPayload(int $mosqueId)
    {
        return FinanceCategory::query()
            ->where('mosque_id', $mosqueId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'entry_type']);
    }

    private function summaryPayload(int $mosqueId): array
    {
        return [
            'income_total' => (float) FinanceTransaction::query()
                ->where('mosque_id', $mosqueId)
                ->where('entry_type', 'income')
                ->where('status', 'approved')
                ->sum('amount'),
            'expense_total' => (float) FinanceTransaction::query()
                ->where('mosque_id', $mosqueId)
                ->where('entry_type', 'expense')
                ->where('status', 'approved')
                ->sum('amount'),
            'pending_total' => (int) FinanceTransaction::query()
                ->where('mosque_id', $mosqueId)
                ->whereIn('status', ['draft', 'pending'])
                ->count(),
            'records_total' => (int) FinanceTransaction::query()
                ->where('mosque_id', $mosqueId)
                ->count(),
        ];
    }

    private function serializeTransaction(FinanceTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'title' => $transaction->title,
            'reference_no' => $transaction->reference_no,
            'amount' => (float) $transaction->amount,
            'entry_type' => $transaction->entry_type,
            'status' => $transaction->status,
            'payment_method' => $transaction->payment_method,
            'transaction_date' => optional($transaction->transaction_date)->toDateString(),
            'notes' => $transaction->notes,
            'rejected_reason' => $transaction->rejected_reason,
            'category' => $transaction->category?->name ?? '-',
            'account' => $transaction->account?->name ?? '-',
        ];
    }

    private function scopedTransaction(Request $request, FinanceTransaction $financeTransaction): FinanceTransaction
    {
        abort_unless($financeTransaction->mosque_id === $request->user()->mosque_id, 404);

        return $financeTransaction;
    }

    private function ensureMutable(FinanceTransaction $transaction): void
    {
        abort_unless(in_array($transaction->status, ['draft', 'pending'], true), 403, 'Only draft/pending transactions can be edited.');
    }

    private function makeReference(string $prefix): string
    {
        return sprintf('%s-%s-%s', $prefix, now()->format('YmdHis'), strtoupper(str()->random(4)));
    }
}
