<?php

namespace App\Http\Controllers;

use App\Http\Requests\VolunteerStoreRequest;
use App\Http\Resources\VolunteerResource;
use App\Models\Volunteer;
use App\Services\AuditLogService;
use App\Services\VolunteerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class VolunteerController extends Controller
{
    public function __construct(
        private readonly VolunteerService $volunteerService,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function show(Volunteer $volunteer): Response
    {
        return Inertia::render('Volunteers/Show', [
            'volunteer' => (new VolunteerResource($volunteer))->resolve(),
        ]);
    }

    public function index(): InertiaResponse
    {
        $volunteers = Volunteer::query()
            ->latest()
            ->paginate(15);

        return Inertia::render('Volunteers/Index', [
            'volunteers' => VolunteerResource::collection($volunteers),
        ]);
    }

    public function store(VolunteerStoreRequest $request): RedirectResponse
    {
        $volunteer = $this->volunteerService->create($request->validated());
        $this->auditLogService->log('volunteer.created', $volunteer, null, $volunteer->toArray());

        return back()->with('success', 'Relawan berhasil dibuat.');
    }

    public function update(VolunteerStoreRequest $request, Volunteer $volunteer): RedirectResponse
    {
        $before = $volunteer->toArray();
        $volunteer = $this->volunteerService->update($volunteer, $request->validated());
        $this->auditLogService->log('volunteer.updated', $volunteer, $before, $volunteer->toArray());

        return back()->with('success', 'Relawan diperbarui.');
    }

    public function destroy(Volunteer $volunteer): RedirectResponse
    {
        $before = $volunteer->toArray();
        $volunteer->delete();
        $this->auditLogService->log('volunteer.deleted', $volunteer, $before, null);

        return back()->with('success', 'Relawan dihapus.');
    }

    public function idCardPdf(Volunteer $volunteer): Response
    {
        $pdf = Pdf::loadView('pdf.volunteer-id-card', ['volunteers' => [$volunteer]]);

        return $pdf->download("id-card-relawan-{$volunteer->id}.pdf");
    }

    public function batchIdCardPdf(): Response
    {
        $volunteers = Volunteer::query()->where('is_active', true)->get();
        $pdf = Pdf::loadView('pdf.volunteer-id-card', compact('volunteers'));

        return $pdf->download('id-card-relawan-batch.pdf');
    }
}
