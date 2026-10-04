<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceAccount;
use App\Models\Mosque;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();
        $accounts = FinanceAccount::where('mosque_id', $mosque->id)
            ->where('is_active', true)
            ->get();

        return Inertia::render('Admin/Settings/Index', [
            'mosque' => $mosque,
            'accounts' => $accounts,
            'settings' => $mosque->settings ?? [],
        ]);
    }

    public function update(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'settings.show_public_report' => 'boolean',
            'settings.show_donation_page' => 'boolean',
            'settings.show_events' => 'boolean',
            'settings.show_announcements' => 'boolean',
            'settings.show_prayer_schedule' => 'boolean',
            'settings.show_contact' => 'boolean',
        ]);

        $mosque->update([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'description' => $validated['description'] ?? null,
            'settings' => $validated['settings'] ?? [],
        ]);

        return back()->with('success', 'Pengaturan masjid berhasil diperbarui.');
    }
}