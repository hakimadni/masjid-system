<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FinanceTransaction;
use App\Models\Media;
use App\Models\Mosque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PrayerTimesController extends Controller
{
    /**
     * Display prayer times with media, finance report, and events
     */
    public function index(Request $request)
    {
        // Get active mosque (first active one or from subdomain/config)
        $mosque = Mosque::where('is_active', true)->first();

        if (! $mosque) {
            return view('public.prayer-times.index')
                ->with('error', 'Mosque not configured');
        }

        $cityId = (string) ($mosque->city_id ?? '1301');

        // Get prayer times from API (cache for 1 hour)
        $prayerTimes = Cache::remember(
            'prayer_times_'.$cityId.'_'.now()->format('Y-m'),
            3600,
            function () use ($cityId) {
                return $this->fetchPrayerTimes($cityId);
            }
        );

        // Get today's prayer times
        $today = now()->format('Y-m-d');
        $todayPrayerTimes = $prayerTimes['jadwal'][$today] ?? null;

        // Get media (YouTube videos, etc.)
        $media = Media::where('mosque_id', $mosque->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Get finance summary (last 30 days)
        $financeSummary = FinanceTransaction::where('mosque_id', $mosque->id)
            ->where('status', 'approved')
            ->where('transaction_date', '>=', now()->subDays(30))
            ->get()
            ->groupBy('entry_type')
            ->map(function ($items) {
                return [
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            });

        // Get upcoming events (next 7 days)
        $upcomingEvents = Event::where('mosque_id', $mosque->id)
            ->where('start_at', '>=', now())
            ->where('start_at', '<=', now()->addDays(7))
            ->where('status', 'published')
            ->orderBy('start_at')
            ->take(5)
            ->get();

        return view('public.prayer-times.index', [
            'mosque' => $mosque,
            'prayerTimes' => $prayerTimes,
            'todayPrayerTimes' => $todayPrayerTimes,
            'media' => $media,
            'financeSummary' => $financeSummary,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    /**
     * Fetch prayer times from MyQuran API
     */
    private function fetchPrayerTimes(string $cityId): array
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $response = Http::get(
            "https://api.myquran.com/v3/sholat/jadwal/{$cityId}/{$currentYear}/{$currentMonth}"
        );

        if ($response->successful() && $response->json('status') === true) {
            return $response->json('data');
        }

        // Fallback: try previous month if current month fails
        $prevMonth = now()->subMonth()->month;
        $prevYear = now()->subMonth()->year;

        $response = Http::get(
            "https://api.myquran.com/v3/sholat/jadwal/{$cityId}/{$prevYear}/{$prevMonth}"
        );

        if ($response->successful() && $response->json('status') === true) {
            return $response->json('data');
        }

        // Return empty data if both fail
        return [
            'id' => $cityId,
            'jadwal' => [],
        ];
    }
}
