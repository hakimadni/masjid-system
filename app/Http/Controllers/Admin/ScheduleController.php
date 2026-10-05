<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminScheduleStoreRequest;
use App\Models\PrayerSchedule;
use App\Models\ServiceSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
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

        $prayerQuery = PrayerSchedule::query()->where('mosque_id', $mosqueId);
        $serviceQuery = ServiceSchedule::query()->where('mosque_id', $mosqueId);

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $prayerQuery->where(function ($builder) use ($search): void {
                $builder
                    ->where('prayer_name', 'like', '%'.$search.'%')
                    ->orWhere('imam_name', 'like', '%'.$search.'%')
                    ->orWhere('muadzin_name', 'like', '%'.$search.'%')
                    ->orWhere('khatib_name', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
            $serviceQuery->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('person_name', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        if ($filters['status'] ?? null) {
            $prayerQuery->where('status', $filters['status']);
            $serviceQuery->where('status', $filters['status']);
        }

        if ($filters['start_date'] ?? null) {
            $prayerQuery->whereDate('schedule_date', '>=', $filters['start_date']);
            $serviceQuery->whereDate('scheduled_at', '>=', $filters['start_date']);
        }

        if ($filters['end_date'] ?? null) {
            $prayerQuery->whereDate('schedule_date', '<=', $filters['end_date']);
            $serviceQuery->whereDate('scheduled_at', '<=', $filters['end_date']);
        }

        return Inertia::render('Admin/Schedules/Index', [
            'filters' => $filters,
            'prayerSchedules' => $prayerQuery
                ->orderBy('schedule_date')
                ->paginate(8, ['*'], 'prayer_page')
                ->withQueryString()
                ->through(fn (PrayerSchedule $schedule): array => [
                    'id' => $schedule->id,
                    'prayer_name' => $schedule->prayer_name,
                    'schedule_date' => optional($schedule->schedule_date)->toDateString(),
                    'prayer_time' => $schedule->prayer_time,
                    'imam_name' => $schedule->imam_name,
                    'muadzin_name' => $schedule->muadzin_name,
                    'khatib_name' => $schedule->khatib_name,
                    'status' => $schedule->status,
                    'notes' => $schedule->notes,
                ]),
            'serviceSchedules' => $serviceQuery
                ->orderBy('scheduled_at')
                ->paginate(8, ['*'], 'service_page')
                ->withQueryString()
                ->through(fn (ServiceSchedule $schedule): array => [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'role_type' => $schedule->role_type,
                    'person_name' => $schedule->person_name,
                    'location' => $schedule->location,
                    'scheduled_at' => optional($schedule->scheduled_at)->format('Y-m-d\TH:i'),
                    'status' => $schedule->status,
                    'notes' => $schedule->notes,
                ]),
            'summary' => [
                'upcoming_total' => PrayerSchedule::query()->where('mosque_id', $mosqueId)->whereDate('schedule_date', '>=', now()->toDateString())->count()
                    + ServiceSchedule::query()->where('mosque_id', $mosqueId)->where('scheduled_at', '>=', now())->count(),
                'published_total' => PrayerSchedule::query()->where('mosque_id', $mosqueId)->where('status', 'published')->count()
                    + ServiceSchedule::query()->where('mosque_id', $mosqueId)->where('status', 'published')->count(),
                'completed_total' => PrayerSchedule::query()->where('mosque_id', $mosqueId)->where('status', 'completed')->count()
                    + ServiceSchedule::query()->where('mosque_id', $mosqueId)->where('status', 'completed')->count(),
                'records_total' => PrayerSchedule::query()->where('mosque_id', $mosqueId)->count()
                    + ServiceSchedule::query()->where('mosque_id', $mosqueId)->count(),
            ],
        ]);
    }

    public function storePrayer(AdminScheduleStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $recurrence = $validated['recurrence'] ?? 'none';
        $iterations = 1;
        if ($recurrence === 'daily') $iterations = 30;
        if ($recurrence === 'weekly') $iterations = 4;
        if ($recurrence === 'monthly') $iterations = 3;

        $baseDate = \Carbon\Carbon::parse($validated['schedule_date']);

        for ($i = 0; $i < $iterations; $i++) {
            $date = $baseDate->copy();
            if ($recurrence === 'daily') $date->addDays($i);
            if ($recurrence === 'weekly') $date->addWeeks($i);
            if ($recurrence === 'monthly') $date->addMonths($i);

            PrayerSchedule::query()->create([
                'mosque_id' => $request->user()->mosque_id,
                'schedule_date' => $date->toDateString(),
                'prayer_name' => $validated['prayer_name'],
                'prayer_time' => $validated['prayer_time'] ?? null,
                'imam_name' => $validated['imam_name'] ?? null,
                'muadzin_name' => $validated['muadzin_name'] ?? null,
                'khatib_name' => $validated['khatib_name'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
        }

        return back()->with('success', 'Jadwal shalat berhasil ditambahkan.');
    }

    public function storeService(AdminScheduleStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $recurrence = $validated['recurrence'] ?? 'none';
        $iterations = 1;
        if ($recurrence === 'daily') $iterations = 30;
        if ($recurrence === 'weekly') $iterations = 4;
        if ($recurrence === 'monthly') $iterations = 3;

        $baseDate = \Carbon\Carbon::parse($validated['scheduled_at']);

        for ($i = 0; $i < $iterations; $i++) {
            $date = $baseDate->copy();
            if ($recurrence === 'daily') $date->addDays($i);
            if ($recurrence === 'weekly') $date->addWeeks($i);
            if ($recurrence === 'monthly') $date->addMonths($i);

            ServiceSchedule::query()->create([
                'mosque_id' => $request->user()->mosque_id,
                'title' => $validated['title'],
                'role_type' => $validated['role_type'],
                'person_name' => $validated['person_name'],
                'location' => $validated['location'] ?? null,
                'scheduled_at' => $date->toDateTimeString(),
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
        }

        return back()->with('success', 'Jadwal petugas berhasil ditambahkan.');
    }

    public function updatePrayerStatus(Request $request, PrayerSchedule $prayerSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,published,completed,archived'],
        ]);

        $prayerSchedule->update(['status' => $validated['status']]);

        return back()->with('success', 'Status jadwal shalat berhasil diperbarui.');
    }

    public function updateServiceStatus(Request $request, ServiceSchedule $serviceSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,published,completed,archived'],
        ]);

        $serviceSchedule->update(['status' => $validated['status']]);

        return back()->with('success', 'Status jadwal petugas berhasil diperbarui.');
    }

    public function destroyPrayer(PrayerSchedule $prayerSchedule): RedirectResponse
    {
        $prayerSchedule->delete();

        return back()->with('success', 'Jadwal shalat berhasil dihapus.');
    }

    public function destroyService(ServiceSchedule $serviceSchedule): RedirectResponse
    {
        $serviceSchedule->delete();

        return back()->with('success', 'Jadwal petugas berhasil dihapus.');
    }
}
