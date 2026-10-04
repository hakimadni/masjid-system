<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistributionStoreRequest;
use App\Http\Resources\DistributionResource;
use App\Models\Distribution;
use App\Models\Slaughtering;
use App\Services\AuditLogService;
use App\Services\DistributionService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DistributionController extends Controller
{
    public function __construct(
        private readonly DistributionService $distributionService,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function show(Distribution $distribution): InertiaResponse
    {
        $distribution->load(['animal', 'slaughtering.animal']);

        return Inertia::render('Distributions/Show', [
            'distribution' => (new DistributionResource($distribution))->resolve(),
            'slaughterings' => Slaughtering::query()
                ->with('animal:id,type,weight,supplier')
                ->select('id', 'animal_id', 'date', 'location', 'distribution_status')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function index(): InertiaResponse
    {
        $distributions = Distribution::query()
            ->with(['animal', 'slaughtering.animal'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Distributions/Index', [
            'distributions' => DistributionResource::collection($distributions),
            'slaughterings' => Slaughtering::query()
                ->with('animal:id,type,weight,supplier')
                ->select('id', 'animal_id', 'date', 'location', 'distribution_status')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(DistributionStoreRequest $request): RedirectResponse
    {
        $distribution = Distribution::query()->create($request->validated());
        $this->auditLogService->log('distribution.created', $distribution, null, $distribution->toArray());

        return back()->with('success', 'Distribusi dibuat.');
    }

    public function update(DistributionStoreRequest $request, Distribution $distribution): RedirectResponse
    {
        $before = $distribution->toArray();
        $distribution->update($request->validated());
        $this->auditLogService->log('distribution.updated', $distribution, $before, $distribution->fresh()?->toArray());

        return back()->with('success', 'Distribusi diperbarui.');
    }

    public function destroy(Distribution $distribution): RedirectResponse
    {
        $before = $distribution->toArray();
        $distribution->delete();
        $this->auditLogService->log('distribution.deleted', $distribution, $before, null);

        return back()->with('success', 'Distribusi dihapus.');
    }

    public function markDelivered(Distribution $distribution): RedirectResponse
    {
        $this->distributionService->markDelivered($distribution);

        return back()->with('success', 'Distribusi ditandai delivered.');
    }
}
