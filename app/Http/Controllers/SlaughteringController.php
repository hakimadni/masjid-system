<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlaughteringStoreRequest;
use App\Http\Resources\SlaughteringResource;
use App\Models\Animal;
use App\Models\Slaughtering;
use App\Services\AuditLogService;
use App\Services\SlaughteringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SlaughteringController extends Controller
{
    public function __construct(
        private readonly SlaughteringService $slaughteringService,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function show(Slaughtering $slaughtering): InertiaResponse
    {
        $slaughtering->load(['animal.participants', 'volunteers']);

        return Inertia::render('Slaughterings/Show', [
            'slaughtering' => (new SlaughteringResource($slaughtering))->resolve(),
            'animals' => Animal::query()
                ->select('id', 'type', 'weight', 'supplier', 'status', 'price')
                ->whereIn('status', ['available', 'assigned', 'slaughtered'])
                ->withCount('participants')
                ->orderBy('id')
                ->get(),
            'volunteers' => \App\Models\Volunteer::query()
                ->select('id', 'name', 'phone', 'role_type')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function index(): InertiaResponse
    {
        $slaughterings = Slaughtering::query()
            ->with(['animal.participants'])
            ->withCount('volunteers')
            ->latest()
            ->paginate(15);

        return Inertia::render('Slaughterings/Index', [
            'slaughterings' => SlaughteringResource::collection($slaughterings),
            'animals' => Animal::query()
                ->select('id', 'type', 'weight', 'supplier', 'status', 'price')
                ->whereIn('status', ['available', 'assigned', 'slaughtered'])
                ->withCount('participants')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(SlaughteringStoreRequest $request): RedirectResponse
    {
        $slaughtering = Slaughtering::query()->create($request->validated());
        $this->auditLogService->log('slaughtering.created', $slaughtering, null, $slaughtering->toArray());

        return back()->with('success', 'Data penyembelihan dibuat.');
    }

    public function update(SlaughteringStoreRequest $request, Slaughtering $slaughtering): RedirectResponse
    {
        $before = $slaughtering->toArray();
        $slaughtering->update($request->validated());
        $this->auditLogService->log('slaughtering.updated', $slaughtering, $before, $slaughtering->fresh()?->toArray());

        return back()->with('success', 'Data penyembelihan diperbarui.');
    }

    public function destroy(Slaughtering $slaughtering): RedirectResponse
    {
        $before = $slaughtering->toArray();
        $slaughtering->delete();
        $this->auditLogService->log('slaughtering.deleted', $slaughtering, $before, null);

        return back()->with('success', 'Data penyembelihan dihapus.');
    }

    public function assignVolunteers(Request $request, Slaughtering $slaughtering): RedirectResponse
    {
        $validated = $request->validate([
            'volunteer_ids' => ['required', 'array', 'min:1'],
            'volunteer_ids.*' => ['required', 'exists:volunteers,id'],
        ]);

        $this->slaughteringService->assignVolunteers($slaughtering, $validated['volunteer_ids']);

        return back()->with('success', 'Relawan berhasil ditugaskan.');
    }

    public function markCut(Request $request, Slaughtering $slaughtering): RedirectResponse
    {
        $validated = $request->validate([
            'cut_time' => ['nullable', 'date'],
            'meat_total_kg' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->slaughteringService->markCut(
            $slaughtering,
            $validated['cut_time'] ?? null,
            isset($validated['meat_total_kg']) ? (float) $validated['meat_total_kg'] : null,
        );

        return back()->with('success', 'Penyembelihan berhasil diperbarui.');
    }
}
