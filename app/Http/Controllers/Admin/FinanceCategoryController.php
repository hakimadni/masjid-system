<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'entry_type' => ['nullable', 'in:income,expense'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $query = FinanceCategory::query()->where('mosque_id', $mosqueId);

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($filters['entry_type'] ?? null) {
            $query->where('entry_type', $filters['entry_type']);
        }

        if (($filters['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        }

        if (($filters['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        $categories = $query->withCount('transactions')->orderBy('entry_type')->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Admin/FinanceCategories/Index', [
            'filters' => $filters,
            'categories' => $categories->through(fn (FinanceCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'entry_type' => $category->entry_type,
                'description' => $category->description,
                'is_active' => $category->is_active,
                'transactions_count' => $category->transactions_count,
            ]),
            'summary' => [
                'records_total' => (int) FinanceCategory::query()->where('mosque_id', $mosqueId)->count(),
                'active_total' => (int) FinanceCategory::query()->where('mosque_id', $mosqueId)->where('is_active', true)->count(),
                'income_total' => (int) FinanceCategory::query()->where('mosque_id', $mosqueId)->where('entry_type', 'income')->count(),
                'expense_total' => (int) FinanceCategory::query()->where('mosque_id', $mosqueId)->where('entry_type', 'expense')->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'entry_type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        FinanceCategory::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            'name' => $validated['name'],
            'entry_type' => $validated['entry_type'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return back()->with('success', 'Kategori keuangan berhasil disimpan.');
    }

    public function update(Request $request, FinanceCategory $financeCategory): RedirectResponse
    {
        abort_unless($financeCategory->mosque_id === $request->user()->mosque_id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'entry_type' => ['required', 'in:income,expense'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $financeCategory->update([
            'name' => $validated['name'],
            'entry_type' => $validated['entry_type'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return back()->with('success', 'Kategori keuangan berhasil diperbarui.');
    }
}
