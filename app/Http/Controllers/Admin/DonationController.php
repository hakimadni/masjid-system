<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminDonationStoreRequest;
use App\Models\AuditLog;
use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\Donor;
use App\Models\FinanceAccount;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DonationController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:draft,pending,confirmed,rejected'],
            'method' => ['nullable', 'in:cash,transfer,qris,other'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = Donation::query()->with(['donor:id,name,phone', 'category:id,name'])->where('mosque_id', $mosqueId);

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('campaign', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%')
                    ->orWhereHas('donor', function ($donorQuery) use ($search): void {
                        $donorQuery->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['method'] ?? null) {
            $query->where('method', $filters['method']);
        }

        if ($filters['start_date'] ?? null) {
            $query->whereDate('donation_date', '>=', $filters['start_date']);
        }

        if ($filters['end_date'] ?? null) {
            $query->whereDate('donation_date', '<=', $filters['end_date']);
        }

        $donations = $query->latest('donation_date')->paginate(10)->withQueryString();

        $canSeeSensitive = $request->user()->hasPermissionTo('jamaah.sensitive.view');

        return Inertia::render('Admin/Donations/Index', [
            'filters' => $filters,
            'donations' => $donations->through(fn (Donation $donation): array => $this->serializeDonation($donation, $canSeeSensitive)),
            'summary' => [
                'collected_total' => (int) round(Donation::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'confirmed')
                    ->sum('amount')),
                'pending_total' => (int) Donation::query()
                    ->where('mosque_id', $mosqueId)
                    ->whereIn('status', ['draft', 'pending'])
                    ->count(),
                'donor_total' => (int) Donor::query()->where('mosque_id', $mosqueId)->count(),
                'records_total' => (int) Donation::query()->where('mosque_id', $mosqueId)->count(),
            ],
            'categories' => DonationCategory::query()
                ->where('mosque_id', $mosqueId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function show(Request $request, Donation $donation): Response
    {
        abort_unless($donation->mosque_id === $request->user()->mosque_id, 404);

        $donation->load(['donor', 'category', 'creator', 'confirmer']);

        $canSeeSensitive = $request->user()->hasPermissionTo('jamaah.sensitive.view');
        
        $donorVisible = $canSeeSensitive || ! $donation->is_anonymous;

        return Inertia::render('Admin/Donations/Show', [
            'donation' => [
                'id' => $donation->id,
                'donor_name' => $donorVisible ? ($donation->donor?->name ?? 'Donatur Manual') : $donation->donor?->maskedName(),
                'phone' => $donorVisible ? ($donation->donor?->phone ?? null) : ($donation->donor?->maskedPhone()),
                'campaign' => $donation->campaign,
                'category' => $donation->category?->name ?? '-',
                'amount' => (float) $donation->amount,
                'method' => $donation->method,
                'status' => $donation->status,
                'donation_date' => optional($donation->donation_date)->toDateString(),
                'is_anonymous' => $donation->is_anonymous,
                'notes' => $donation->notes,
                'has_finance_link' => $donation->finance_transaction_id !== null,
                'finance_transaction_id' => $donation->finance_transaction_id,
                'creator' => $donation->creator?->name,
                'confirmer' => $donation->confirmer?->name,
                'confirmed_at' => optional($donation->confirmed_at)?->format('Y-m-d H:i'),
                'can_confirm' => $donation->status !== 'confirmed',
                'can_reject' => $donation->status !== 'rejected',
            ],
            'canUpdateStatus' => $request->user()->hasPermissionTo('donation.confirm') || $request->user()->hasPermissionTo('donation.reject'),
        ]);
    }

    public function store(AdminDonationStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        DB::transaction(function () use ($validated, $user): void {
            $donor = $this->resolveOrCreateDonor($validated);

            $category = $validated['donation_category_id'] ? DonationCategory::query()->find($validated['donation_category_id']) : null;

            $donation = Donation::query()->create([
                'mosque_id' => $user->mosque_id,
                'donor_id' => ($validated['is_anonymous'] ?? false) ? null : $donor->id,
                'donation_category_id' => $validated['donation_category_id'] ?? null,
                'campaign' => $category?->name ?? $validated['campaign'] ?: 'Donasi Umum',
                'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
                'donation_date' => $validated['donation_date'],
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
                'confirmed_by' => $validated['status'] === 'confirmed' ? $user->id : null,
                'confirmed_at' => $validated['status'] === 'confirmed' ? now() : null,
            ]);

            $this->auditLogService->log('donation.created', $donation, null, $donation->toArray());

            if ($donation->status === 'confirmed') {
                $this->syncFinanceTransaction($donation, $user->id);
            }
        });

        return back()->with('success', 'Donasi manual berhasil dicatat.');
    }

    public function updateStatus(Request $request, Donation $donation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,pending,confirmed,rejected'],
        ]);

        $user = $request->user();
        abort_if(
            ($validated['status'] === 'confirmed' && ! $user->hasPermissionTo('donation.confirm')) ||
            ($validated['status'] === 'rejected' && ! $user->hasPermissionTo('donation.reject')),
            403,
            'Anda tidak memiliki izin untuk mengubah status ini.'
        );

        $previousStatus = $donation->status;
        $previousTransactionId = $donation->finance_transaction_id;

        $donation->update([
            'status' => $validated['status'],
            'confirmed_by' => $validated['status'] === 'confirmed' ? $request->user()->id : null,
            'confirmed_at' => $validated['status'] === 'confirmed' ? now() : null,
        ]);

        if ($validated['status'] === 'confirmed') {
            $this->syncFinanceTransaction($donation->fresh(), $request->user()->id);
        } elseif ($validated['status'] === 'rejected' && $previousTransactionId) {
            FinanceTransaction::where('id', $previousTransactionId)->delete();
            $donation->updateQuietly(['finance_transaction_id' => null]);
        }

        $this->auditLogService->log('donation.status-updated', $donation, $validated, $donation->fresh()?->toArray());

        return back()->with('success', 'Status donasi berhasil diperbarui.');
    }

    private function resolveOrCreateDonor(array $validated): Donor
    {
        if ($validated['donor_name'] ?? null) {
            $donor = Donor::query()->firstOrCreate(
                [
                    'mosque_id' => $this->mosqueId(),
                    'name' => $validated['donor_name'],
                ],
                [
                    'phone' => $validated['phone'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'notes' => null,
                ]
            );
        } else {
            $donor = Donor::query()->create([
                'mosque_id' => $this->mosqueId(),
                'name' => 'Hamba Allah',
            ]);
        }

        return $donor;
    }

    private function mosqueId(): int
    {
        return request()->user()?->mosque_id ?? 0;
    }

    private function serializeDonation(Donation $donation, bool $canSeeSensitive): array
    {
        $donorVisible = $canSeeSensitive || ! $donation->is_anonymous;

        return [
            'id' => $donation->id,
            'donor_name' => $donorVisible ? ($donation->donor?->name ?? 'Donatur Manual') : $donation->donor?->maskedName(),
            'phone' => $donorVisible ? ($donation->donor?->phone ?? null) : ($donation->donor?->maskedPhone()),
            'campaign' => $donation->campaign,
            'amount' => (int) round($donation->amount),
            'method' => $donation->method,
            'status' => $donation->status,
            'donation_date' => optional($donation->donation_date)->toDateString(),
            'is_anonymous' => $donation->is_anonymous,
            'notes' => $donation->notes,
            'has_finance_link' => $donation->finance_transaction_id !== null,
        ];
    }

    private function syncFinanceTransaction(Donation $donation, int $userId): void
    {
        $accountId = FinanceAccount::query()
            ->where('mosque_id', $donation->mosque_id)
            ->where('is_active', true)
            ->value('id');

        $categoryId = FinanceCategory::query()
            ->where('mosque_id', $donation->mosque_id)
            ->where('entry_type', 'income')
            ->value('id');

        $financeTransaction = FinanceTransaction::query()->updateOrCreate(
            ['id' => $donation->finance_transaction_id],
            [
                'mosque_id' => $donation->mosque_id,
                'finance_account_id' => $accountId,
                'finance_category_id' => $categoryId,
                'entry_type' => 'income',
                'title' => 'Donasi '.$donation->campaign,
                'transaction_date' => $donation->donation_date,
                'amount' => $donation->amount,
                'status' => 'approved',
                'payment_method' => $donation->method,
                'reference_no' => $donation->finance_transaction_id ? FinanceTransaction::find($donation->finance_transaction_id)?->reference_no : $this->makeReference('DON'),
                'notes' => $donation->notes,
                'created_by' => $donation->created_by ?? $userId,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]
        );

        if ($donation->finance_transaction_id !== $financeTransaction->id) {
            $donation->updateQuietly(['finance_transaction_id' => $financeTransaction->id]);
        }
    }

    private function makeReference(string $prefix): string
    {
        return sprintf('%s-%s-%s', $prefix, now()->format('YmdHis'), strtoupper(str()->random(4)));
    }
}
