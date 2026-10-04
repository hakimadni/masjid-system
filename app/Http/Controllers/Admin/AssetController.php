<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAssetStoreRequest;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssetController extends Controller
{
    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,maintenance,archived'],
            'condition' => ['nullable', 'in:baik,perlu_perbaikan,rusak'],
            'category' => ['nullable', 'string', 'max:150'],
        ]);

        $query = Asset::query()->where('mosque_id', $mosqueId);

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('category', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['condition'] ?? null) {
            $query->where('condition', $filters['condition']);
        }

        if ($filters['category'] ?? null) {
            $query->where('category', $filters['category']);
        }

        $assets = $query->latest('created_at')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Assets/Index', [
            'filters' => $filters,
            'assets' => $assets->through(fn (Asset $asset): array => [
                'id' => $asset->id,
                'name' => $asset->name,
                'category' => $asset->category,
                'location' => $asset->location,
                'quantity' => $asset->quantity,
                'condition' => $asset->condition,
                'status' => $asset->status,
                'notes' => $asset->notes,
                'created_at' => optional($asset->created_at)?->format('Y-m-d'),
            ]),
            'summary' => [
                'active_total' => (int) Asset::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'active')
                    ->count(),
                'maintenance_total' => (int) Asset::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'maintenance')
                    ->count(),
                'archived_total' => (int) Asset::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'archived')
                    ->count(),
                'quantity_total' => (int) Asset::query()
                    ->where('mosque_id', $mosqueId)
                    ->sum('quantity'),
            ],
            'categories' => Asset::query()
                ->where('mosque_id', $mosqueId)
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->values(),
        ]);
    }

    public function store(AdminAssetStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Asset::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'location' => $validated['location'] ?? null,
            'quantity' => $validated['quantity'],
            'condition' => $validated['condition'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Data inventaris berhasil disimpan.');
    }

    public function updateStatus(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,maintenance,archived'],
        ]);

        $asset->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Status inventaris berhasil diperbarui.');
    }

    public function show(Asset $asset): Response
    {
        return Inertia::render('Admin/Assets/Show', [
            'asset' => [
                'id' => $asset->id,
                'name' => $asset->name,
                'category' => $asset->category,
                'location' => $asset->location,
                'quantity' => $asset->quantity,
                'condition' => $asset->condition,
                'status' => $asset->status,
                'notes' => $asset->notes,
                'created_at' => optional($asset->created_at)->format('Y-m-d'),
            ]
        ]);
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $asset->delete();

        return back()->with('success', 'Inventaris berhasil dihapus.');
    }
}
