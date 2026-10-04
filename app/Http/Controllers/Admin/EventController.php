<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminEventStoreRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:draft,published,completed,archived'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $query = Event::query()->where('mosque_id', $mosqueId);

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%')
                    ->orWhere('pic_name', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['start_date'] ?? null) {
            $query->whereDate('start_at', '>=', $filters['start_date']);
        }

        if ($filters['end_date'] ?? null) {
            $query->whereDate('start_at', '<=', $filters['end_date']);
        }

        $events = $query->latest('start_at')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Events/Index', [
            'filters' => $filters,
            'events' => $events->through(fn (Event $event): array => [
                'id' => $event->id,
                'title' => $event->title,
                'start_at' => optional($event->start_at)?->format('Y-m-d H:i'),
                'end_at' => optional($event->end_at)?->format('Y-m-d H:i'),
                'location' => $event->location,
                'pic_name' => $event->pic_name,
                'status' => $event->status,
                'notes' => $event->notes,
            ]),
            'summary' => [
                'upcoming_total' => (int) Event::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('start_at', '>=', now())
                    ->whereIn('status', ['draft', 'published'])
                    ->count(),
                'published_total' => (int) Event::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'published')
                    ->count(),
                'completed_total' => (int) Event::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'completed')
                    ->count(),
                'records_total' => (int) Event::query()
                    ->where('mosque_id', $mosqueId)
                    ->count(),
            ],
        ]);
    }

    public function store(AdminEventStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Event::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            'title' => $validated['title'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'] ?? null,
            'location' => $validated['location'] ?? null,
            'pic_name' => $validated['pic_name'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Kegiatan berhasil disimpan.');
    }

    public function updateStatus(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,published,completed,archived'],
        ]);

        $event->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Status kegiatan berhasil diperbarui.');
    }

    public function show(Event $event): Response
    {
        return Inertia::render('Admin/Events/Show', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'start_at' => optional($event->start_at)->format('Y-m-d H:i'),
                'end_at' => optional($event->end_at)->format('Y-m-d H:i'),
                'location' => $event->location,
                'pic_name' => $event->pic_name,
                'status' => $event->status,
                'notes' => $event->notes,
            ]
        ]);
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();
        return back()->with('success', 'Kegiatan berhasil dihapus.');
    }
}
