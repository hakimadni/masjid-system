<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JamaahController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Jamaah::query()->where('mosque_id', $request->user()->mosque_id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $jamaahs = $query->orderBy('name')->paginate(20)->withQueryString();
        $canSeeSensitive = $request->user()->hasPermissionTo('jamaah.sensitive.view');

        return Inertia::render('Admin/Jamaahs/Index', [
            'jamaahs' => $jamaahs->through(fn (Jamaah $jamaah) => $this->serializeJamaah($jamaah, $canSeeSensitive)),
            'filters' => $request->only(['search', 'status', 'category']),
        ]);
    }

    public function show(Request $request, Jamaah $jamaah): Response
    {
        abort_unless($jamaah->mosque_id === $request->user()->mosque_id, 404);
        
        $canSeeSensitive = $request->user()->hasPermissionTo('jamaah.sensitive.view');
        
        return Inertia::render('Admin/Jamaahs/Show', [
            'jamaah' => $this->serializeJamaah($jamaah, $canSeeSensitive),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['nullable', 'in:male,female'],
            'category' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'family_role' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive,deceased,moved'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Jamaah::query()->create([
            'mosque_id' => $request->user()->mosque_id,
            ...$validated,
        ]);

        return back()->with('success', 'Data jamaah berhasil ditambahkan.');
    }

    public function update(Request $request, Jamaah $jamaah): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['nullable', 'in:male,female'],
            'category' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'family_role' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive,deceased,moved'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $jamaah->update($validated);
        return back()->with('success', 'Data jamaah berhasil diperbarui.');
    }

    public function destroy(Jamaah $jamaah): RedirectResponse
    {
        $jamaah->delete();
        return back()->with('success', 'Data jamaah berhasil dihapus.');
    }

    private function serializeJamaah(Jamaah $jamaah, bool $canSeeSensitive): array
    {
        $hideSensitive = $jamaah->category === 'mustahik' && ! $canSeeSensitive;

        return [
            'id' => $jamaah->id,
            'name' => $jamaah->name,
            'gender' => $jamaah->gender,
            'category' => $jamaah->category,
            'phone' => $hideSensitive ? $jamaah->maskedPhone() : $jamaah->phone,
            'email' => $hideSensitive ? $jamaah->maskedEmail() : $jamaah->email,
            'address' => $hideSensitive ? $jamaah->maskedAddress() : $jamaah->address,
            'birth_date' => $jamaah->birth_date?->toDateString(),
            'family_role' => $jamaah->family_role,
            'status' => $jamaah->status,
            'notes' => $jamaah->notes,
            'is_masked' => $hideSensitive,
        ];
    }
}
