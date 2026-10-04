<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipantStoreRequest;
use App\Http\Resources\ParticipantResource;
use App\Models\Animal;
use App\Models\Participant;
use App\Models\QurbanSaving;
use App\Services\AuditLogService;
use App\Services\ParticipantGroupingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ParticipantController extends Controller
{
    public function __construct(
        private readonly ParticipantGroupingService $groupingService,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function show(Participant $participant): InertiaResponse
    {
        $participant->load(['animal', 'qurbanSaving']);

        return Inertia::render('Participants/Show', [
            'participant' => (new ParticipantResource($participant))->resolve(),
            'animals' => Animal::query()->select('id', 'type', 'weight', 'supplier')->orderBy('id')->get(),
            'savings' => QurbanSaving::query()
                ->select('id', 'user_id', 'current_balance', 'status')
                ->with('user:id,name')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function index(): InertiaResponse
    {
        $participants = Participant::query()
            ->with(['animal', 'qurbanSaving'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Participants/Index', [
            'participants' => ParticipantResource::collection($participants),
            'animals' => Animal::query()->select('id', 'type', 'weight', 'supplier')->orderBy('id')->get(),
            'savings' => QurbanSaving::query()
                ->select('id', 'user_id', 'current_balance', 'status')
                ->with('user:id,name')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(ParticipantStoreRequest $request): RedirectResponse
    {
        $participant = $this->groupingService->addParticipant($request->validated());
        $this->auditLogService->log('participant.created', $participant, null, $participant->toArray());

        return back()->with('success', 'Peserta berhasil dibuat.');
    }

    public function update(ParticipantStoreRequest $request, Participant $participant): RedirectResponse
    {
        $before = $participant->toArray();
        $participant->update($request->validated());
        $this->auditLogService->log('participant.updated', $participant, $before, $participant->fresh()?->toArray());

        return back()->with('success', 'Peserta berhasil diperbarui.');
    }

    public function destroy(Participant $participant): RedirectResponse
    {
        $before = $participant->toArray();
        $participant->delete();
        $this->auditLogService->log('participant.deleted', $participant, $before, null);

        return back()->with('success', 'Peserta dihapus.');
    }

    public function autoGroup(): RedirectResponse
    {
        $count = $this->groupingService->autoGroupUnassignedCowParticipants();

        return back()->with('success', "Auto grouping selesai: {$count} sapi diperbarui.");
    }
}
