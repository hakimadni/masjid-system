<?php

namespace App\Http\Controllers;

use App\Http\Requests\QurbanSavingsStoreRequest;
use App\Http\Resources\QurbanSavingResource;
use App\Models\Mosque;
use App\Models\QurbanSaving;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\QurbanSavingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QurbanSavingsController extends Controller
{
    public function __construct(
        private readonly QurbanSavingsService $savingsService,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function show(QurbanSaving $saving): InertiaResponse
    {
        $saving->load(['user', 'participants.animal']);

        return Inertia::render('QurbanSavings/Show', [
            'saving' => (new QurbanSavingResource($saving))->resolve(),
        ]);
    }

    public function index(): InertiaResponse
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $savings = QurbanSaving::where('mosque_id', $mosque->id)
            ->with(['user', 'participants'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Qurban/Index', [
            'savings' => QurbanSavingResource::collection($savings),
            'users' => User::query()->select('id', 'name')->orderBy('name')->get(),
            'summary' => [
                'total_accounts' => QurbanSaving::query()->count(),
                'total_balance' => (float) QurbanSaving::query()->sum('current_balance'),
                'completed_accounts' => QurbanSaving::query()->where('status', 'completed')->count(),
                'active_accounts' => QurbanSaving::query()->where('status', 'active')->count(),
                'avg_progress' => (float) DB::table('qurban_savings')
                    ->selectRaw('AVG(CASE WHEN target_amount > 0 THEN CASE WHEN (current_balance * 100.0 / target_amount) > 100 THEN 100 ELSE (current_balance * 100.0 / target_amount) END ELSE 0 END) as progress')
                    ->value('progress'),
            ],
        ]);
    }

    public function store(QurbanSavingsStoreRequest $request): RedirectResponse
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();
        
        $data = $request->validated();
        $data['mosque_id'] = $mosque->id;
        
        $saving = QurbanSaving::query()->create($data);
        $this->savingsService->refreshEligibility($saving);
        $this->auditLogService->log('qurban_saving.created', $saving, null, $saving->toArray());

        return back()->with('success', 'Tabungan qurban berhasil dibuat.');
    }

    public function update(Request $request, QurbanSaving $saving): RedirectResponse
    {
        $validated = $request->validate([
            'target_amount' => ['required', 'numeric', 'min:100000'],
            'status' => ['nullable', 'in:active,completed,cancelled'],
        ]);

        $before = $saving->toArray();
        $saving->update($validated);
        $this->savingsService->refreshEligibility($saving);

        $this->auditLogService->log('qurban_saving.updated', $saving, $before, $saving->fresh()?->toArray());

        return back()->with('success', 'Tabungan qurban berhasil diperbarui.');
    }

    public function destroy(QurbanSaving $saving): RedirectResponse
    {
        $before = $saving->toArray();
        $saving->delete();

        $this->auditLogService->log('qurban_saving.deleted', $saving, $before, null);

        return back()->with('success', 'Tabungan qurban dihapus.');
    }

    public function topup(Request $request, QurbanSaving $saving): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->savingsService->topup($saving, (float) $validated['amount'], $validated['notes'] ?? null);

        return back()->with('success', 'Top up berhasil.');
    }

    public function withdraw(Request $request, QurbanSaving $saving): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->savingsService->withdraw($saving, (float) $validated['amount'], $validated['notes'] ?? null);

        return back()->with('success', 'Penarikan berhasil.');
    }
}
