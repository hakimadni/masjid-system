<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAnnouncementStoreRequest;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:draft,published,archived'],
        ]);

        $query = Announcement::query()->where('mosque_id', $mosqueId);

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('content', 'like', '%'.$search.'%');
            });
        }

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        $announcements = $query->latest('published_at')->latest('created_at')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Announcements/Index', [
            'filters' => $filters,
            'announcements' => $announcements->through(fn (Announcement $announcement): array => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'excerpt' => str($announcement->content)->limit(110)->value(),
                'published_at' => optional($announcement->published_at)?->format('Y-m-d H:i'),
                'status' => $announcement->status,
                'created_at' => optional($announcement->created_at)?->format('Y-m-d H:i'),
            ]),
            'summary' => [
                'published_total' => (int) Announcement::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'published')
                    ->count(),
                'draft_total' => (int) Announcement::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'draft')
                    ->count(),
                'archived_total' => (int) Announcement::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'archived')
                    ->count(),
                'records_total' => (int) Announcement::query()
                    ->where('mosque_id', $mosqueId)
                    ->count(),
            ],
        ]);
    }

    public function store(AdminAnnouncementStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Announcement::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published'
                ? ($validated['published_at'] ?? now())
                : null,
        ]);

        return back()->with('success', 'Pengumuman berhasil disimpan.');
    }

    public function updateStatus(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $announcement->update([
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published'
                ? ($announcement->published_at ?? now())
                : ($validated['status'] === 'draft' ? null : $announcement->published_at),
        ]);

        return back()->with('success', 'Status pengumuman berhasil diperbarui.');
    }
}
