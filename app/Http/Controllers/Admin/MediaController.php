<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminMediaStoreRequest;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    /**
     * Display a listing of the media.
     */
    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;

        $media = Media::query()
            ->where('mosque_id', $mosqueId)
            ->when($request->filled('search'), function ($query, $search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            })
            ->when($request->filled('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->filled('status'), function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('order')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Media $media): array => [
                'id' => $media->id,
                'title' => $media->title,
                'description' => $media->description,
                'url' => $media->url,
                'type' => $media->type,
                'is_active' => $media->is_active,
                'order' => $media->order,
                'created_at' => $media->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Media/Index', [
            'media' => $media,
            'filters' => $request->only(['search', 'type', 'status']),
            'types' => [
                ['value' => 'youtube', 'label' => 'YouTube'],
                ['value' => 'video', 'label' => 'Video Lokal'],
                ['value' => 'audio', 'label' => 'Audio/Podcast'],
                ['value' => 'image', 'label' => 'Gambar'],
                ['value' => 'document', 'label' => 'Dokumen'],
            ],
        ]);
    }

    /**
     * Show the form for creating new media.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Media/Create', [
            'types' => [
                ['value' => 'youtube', 'label' => 'YouTube'],
                ['value' => 'video', 'label' => 'Video Lokal'],
                ['value' => 'audio', 'label' => 'Audio/Podcast'],
                ['value' => 'image', 'label' => 'Gambar'],
                ['value' => 'document', 'label' => 'Dokumen'],
            ],
        ]);
    }

    /**
     * Store a newly created media in storage.
     */
    public function store(AdminMediaStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Media::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'url' => $validated['url'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'],
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()->route('media.index')
            ->with('success', 'Media berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified media.
     */
    public function edit(Media $media): Response
    {
        return Inertia::render('Admin/Media/Edit', [
            'media' => [
                'id' => $media->id,
                'title' => $media->title,
                'description' => $media->description,
                'url' => $media->url,
                'type' => $media->type,
                'is_active' => $media->is_active,
                'order' => $media->order,
            ],
            'types' => [
                ['value' => 'youtube', 'label' => 'YouTube'],
                ['value' => 'video', 'label' => 'Video Lokal'],
                ['value' => 'audio', 'label' => 'Audio/Podcast'],
                ['value' => 'image', 'label' => 'Gambar'],
                ['value' => 'document', 'label' => 'Dokumen'],
            ],
        ]);
    }

    /**
     * Update the specified media in storage.
     */
    public function update(AdminMediaStoreRequest $request, Media $media): RedirectResponse
    {
        $validated = $request->validated();

        $media->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'url' => $validated['url'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'],
            'order' => $validated['order'] ?? 0,
        ]);

        return back()->with('success', 'Media berhasil diperbarui.');
    }

    /**
     * Remove the specified media from storage.
     */
    public function destroy(Media $media): RedirectResponse
    {
        $media->delete();

        return back()->with('success', 'Media berhasil dihapus.');
    }
}
