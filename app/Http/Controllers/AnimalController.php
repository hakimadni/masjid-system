<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalStoreRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use App\Services\AnimalService;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AnimalController extends Controller
{
    public function __construct(
        private readonly AnimalService $animalService,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function show(Animal $animal): InertiaResponse
    {
        $animal->load(['participants', 'slaughtering']);

        return Inertia::render('Animals/Show', [
            'animal' => (new AnimalResource($animal))->resolve(),
        ]);
    }

    public function index(): InertiaResponse
    {
        $animals = Animal::query()
            ->withCount('participants')
            ->with(['participants', 'slaughtering'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Animals/Index', [
            'animals' => AnimalResource::collection($animals),
        ]);
    }

    public function store(AnimalStoreRequest $request): RedirectResponse
    {
        $animal = $this->animalService->create($request->validated());
        $this->auditLogService->log('animal.created', $animal, null, $animal->toArray());

        return back()->with('success', 'Hewan berhasil dibuat.');
    }

    public function update(AnimalStoreRequest $request, Animal $animal): RedirectResponse
    {
        $before = $animal->toArray();
        $animal = $this->animalService->update($animal, $request->validated());
        $this->auditLogService->log('animal.updated', $animal, $before, $animal->toArray());

        return back()->with('success', 'Hewan berhasil diperbarui.');
    }

    public function destroy(Animal $animal): RedirectResponse
    {
        $before = $animal->toArray();
        $animal->delete();
        $this->auditLogService->log('animal.deleted', $animal, $before, null);

        return back()->with('success', 'Hewan dihapus.');
    }
}
