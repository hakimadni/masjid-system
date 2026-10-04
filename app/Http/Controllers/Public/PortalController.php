<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\FinanceTransaction;
use App\Models\Mosque;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortalController extends Controller
{
    public function home(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->first();

        if (!$mosque) {
            return Inertia::render('Public/Home', [
                'mosque' => null,
                'todayPrayerTimes' => null,
                'upcomingEvents' => [],
                'latestAnnouncements' => [],
                'financeSummary' => [
                    'income_total' => 0,
                    'expense_total' => 0,
                    'balance' => 0,
                ],
                'settings' => [
                    'show_public_report' => false,
                    'show_donation_page' => false,
                    'show_events' => false,
                    'show_announcements' => false,
                    'show_prayer_schedule' => false,
                    'show_contact' => true,
                ],
            ]);
        }

        // Load settings from mosque or dedicated settings table
        $settings = [
            'show_public_report' => $mosque->settings['show_public_report'] ?? false,
            'show_donation_page' => $mosque->settings['show_donation_page'] ?? true,
            'show_events' => $mosque->settings['show_events'] ?? true,
            'show_announcements' => $mosque->settings['show_announcements'] ?? true,
            'show_prayer_schedule' => $mosque->settings['show_prayer_schedule'] ?? true,
            'show_contact' => $mosque->settings['show_contact'] ?? true,
        ];

        // Get upcoming events (next 7 days)
        $upcomingEvents = Event::where('mosque_id', $mosque->id)
            ->where('start_at', '>=', now())
            ->where('start_at', '<=', now()->addDays(7))
            ->where('status', 'published')
            ->where('is_public', true)
            ->orderBy('start_at')
            ->take(5)
            ->get(['id', 'title', 'start_at', 'location']);

        // Get latest announcements
        $latestAnnouncements = Announcement::where('mosque_id', $mosque->id)
            ->where('is_public', true)
            ->where('status', 'published')
            ->latest('published_at')
            ->take(5)
            ->get(['id', 'title', 'excerpt', 'published_at']);

        // Calculate public financial summary (approved transactions only, aggregated data)
        $financeSummary = [
            'income_total' => (float) FinanceTransaction::query()
                ->where('mosque_id', $mosque->id)
                ->where('entry_type', 'income')
                ->where('status', 'approved')
                ->sum('amount'),
            'expense_total' => (float) FinanceTransaction::query()
                ->where('mosque_id', $mosque->id)
                ->where('entry_type', 'expense')
                ->where('status', 'approved')
                ->sum('amount'),
            'balance' => 0,
        ];
        $financeSummary['balance'] = $financeSummary['income_total'] - $financeSummary['expense_total'];

        return Inertia::render('Public/Home', [
            'mosque' => $mosque,
            'todayPrayerTimes' => null, // Will be populated by PrayerTimesController
            'upcomingEvents' => $upcomingEvents,
            'latestAnnouncements' => $latestAnnouncements,
            'financeSummary' => $financeSummary,
            'settings' => $settings,
        ]);
    }

    public function profile(): Response
    {
        $mosque = Mosque::where('is_active', true)->first();

        return Inertia::render('Public/Profile', [
            'mosque' => $mosque,
        ]);
    }

    public function schedule(): Response
    {
        $mosque = Mosque::where('is_active', true)->first();

        // Get prayer schedules - this would integrate with PrayerSchedule model or external API
        // For now, returning placeholder
        return Inertia::render('Public/Schedule', [
            'mosque' => $mosque,
            'prayerSchedules' => [],
            'serviceSchedules' => [],
        ]);
    }

    public function events(): Response
    {
        $mosque = Mosque::where('is_active', true)->first();
        $settings = Mosque::where('is_active', true)->first()?->settings ?? [];

        $events = Event::where('mosque_id', $mosque->id ?? 0)
            ->where('status', 'published')
            ->where('start_at', '>=', now())
            ->where('is_public', true)
            ->orderBy('start_at')
            ->paginate(12);

        return Inertia::render('Public/Events', [
            'mosque' => $mosque,
            'events' => $events,
        ]);
    }

    public function announcements(): Response
    {
        $mosque = Mosque::where('is_active', true)->first();

        $announcements = Announcement::where('mosque_id', $mosque->id ?? 0)
            ->where('status', 'published')
            ->where('is_public', true)
            ->latest('published_at')
            ->paginate(12);

        return Inertia::render('Public/Announcements', [
            'mosque' => $mosque,
            'announcements' => $announcements,
        ]);
    }

    public function donation(): Response
    {
        $mosque = Mosque::where('is_active', true)->first();

        return Inertia::render('Public/Donation', [
            'mosque' => $mosque,
            'settings' => [
                'bank_accounts' => [],
                'qris_info' => null,
            ],
        ]);
    }

    public function financeReport(): Response
    {
        $mosque = Mosque::where('is_active', true)->first();
        $settings = Mosque::where('is_active', true)->first()?->settings ?? [];

        if (!($settings['show_public_report'] ?? false)) {
            return Inertia::render('Public/FinanceReport', [
                'mosque' => $mosque,
                'financeData' => null,
                'error' => 'Laporan keuangan publik belum diaktifkan.',
            ]);
        }

        // Get monthly aggregated data (last 12 months)
        $transactions = FinanceTransaction::query()
            ->where('mosque_id', $mosque->id ?? 0)
            ->where('status', 'approved')
            ->where('transaction_date', '>=', now()->subYear())
            ->get(['transaction_date', 'entry_type', 'amount']);

        // Group by month
        $monthlyIncome = $transactions->where('entry_type', 'income')
            ->groupBy(fn($t) => $t->transaction_date->format('Ym'))
            ->map(fn($group) => $group->sum('amount'))
            ->map(fn($sum, $key) => [
                'year' => (int) substr($key, 0, 4),
                'month' => (int) substr($key, 4, 2),
                'income' => (float) $sum,
            ]);

        $monthlyExpense = $transactions->where('entry_type', 'expense')
            ->groupBy(fn($t) => $t->transaction_date->format('Ym'))
            ->map(fn($group) => $group->sum('amount'))
            ->map(fn($sum, $key) => [
                'year' => (int) substr($key, 0, 4),
                'month' => (int) substr($key, 4, 2),
                'expense' => (float) $sum,
            ]);

        // Combine and sort
        $monthly = collect();
        $allMonths = $monthlyIncome->keys()->merge($monthlyExpense->keys())->unique()->sort();
        foreach ($allMonths as $month) {
            $monthly->push([
                'year' => (int) substr($month, 0, 4),
                'month' => (int) substr($month, 4, 2),
                'income' => (float) ($monthlyIncome[$month]['income'] ?? 0),
                'expense' => (float) ($monthlyExpense[$month]['expense'] ?? 0),
            ]);
        }

        $financeData = [
            'monthly' => $monthly->values()->all(),
            'totals' => [
                'income' => (float) $transactions->where('entry_type', 'income')->sum('amount'),
                'expense' => (float) $transactions->where('entry_type', 'expense')->sum('amount'),
                'balance' => (float) ($transactions->where('entry_type', 'income')->sum('amount') - $transactions->where('entry_type', 'expense')->sum('amount')),
            ],
            'income_by_category' => [],
            'expense_by_category' => [],
        ];

        return Inertia::render('Public/FinanceReport', [
            'mosque' => $mosque,
            'financeData' => $financeData,
        ]);
    }

    public function contact(): Response
    {
        $mosque = Mosque::where('is_active', true)->first();
        $settings = Mosque::where('is_active', true)->first()?->settings ?? [];

        return Inertia::render('Public/Contact', [
            'mosque' => $mosque,
            'show_contact' => $settings['show_contact'] ?? true,
        ]);
    }
}