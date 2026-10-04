<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DonationCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = DonationCategory::query()
            ->where('mosque_id', $request->user()->mosque_id)
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/DonationCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        DonationCategory::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Kategori donasi berhasil ditambahkan.');
    }

    public function update(Request $request, DonationCategory $donationCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);

        $donationCategory->update($validated);

        return back()->with('success', 'Kategori donasi berhasil diperbarui.');
    }

    public function destroy(DonationCategory $donationCategory): RedirectResponse
    {
        $donationCategory->delete();
        return back()->with('success', 'Kategori donasi berhasil dihapus.');
    }
}
