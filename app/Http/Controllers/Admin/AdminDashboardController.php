<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Donation;
use App\Models\Event;
use App\Models\FinanceTransaction;
use App\Models\PrayerSchedule;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $mosqueId = $request->user()?->mosque_id;

        $approvedIncome = FinanceTransaction::query()
            ->where('mosque_id', $mosqueId)
            ->where('entry_type', 'income')
            ->where('status', 'approved');

        $approvedExpense = FinanceTransaction::query()
            ->where('mosque_id', $mosqueId)
            ->where('entry_type', 'expense')
            ->where('status', 'approved');

        $cards = [
            [
                'label' => 'Saldo Kas',
                'value' => (float) ((clone $approvedIncome)->sum('amount') - (clone $approvedExpense)->sum('amount')),
                'type' => 'currency',
            ],
            [
                'label' => 'Pemasukan Bulan Ini',
                'value' => (float) (clone $approvedIncome)
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->sum('amount'),
                'type' => 'currency',
            ],
            [
                'label' => 'Pengeluaran Bulan Ini',
                'value' => (float) (clone $approvedExpense)
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->sum('amount'),
                'type' => 'currency',
            ],
            [
                'label' => 'Donasi Bulan Ini',
                'value' => (float) Donation::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'confirmed')
                    ->whereMonth('donation_date', now()->month)
                    ->whereYear('donation_date', now()->year)
                    ->sum('amount'),
                'type' => 'currency',
            ],
            [
                'label' => 'Transaksi Pending',
                'value' => FinanceTransaction::query()
                    ->where('mosque_id', $mosqueId)
                    ->whereIn('status', ['draft', 'pending'])
                    ->count(),
                'type' => 'number',
            ],
            [
                'label' => 'Kegiatan Terdekat',
                'value' => Event::query()
                    ->where('mosque_id', $mosqueId)
                    ->where('start_at', '>=', now())
                    ->count(),
                'type' => 'number',
            ],
        ];

        $sixMonthsAgo = now()->subMonthsNoOverflow(5)->startOfMonth();
        
        $financeTx = FinanceTransaction::query()
            ->where('mosque_id', $mosqueId)
            ->where('status', 'approved')
            ->where('transaction_date', '>=', $sixMonthsAgo)
            ->get(['amount', 'entry_type', 'transaction_date']);
            
        $financeChart = [
            'labels' => [],
            'income' => [],
            'expense' => [],
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonthsNoOverflow($i);
            $monthKey = $date->format('Y-m');
            $financeChart['labels'][] = $date->format('M Y');
            
            $monthTx = $financeTx->filter(fn($t) => optional($t->transaction_date)->format('Y-m') === $monthKey);
            $financeChart['income'][] = $monthTx->where('entry_type', 'income')->sum('amount');
            $financeChart['expense'][] = $monthTx->where('entry_type', 'expense')->sum('amount');
        }

        $donationsTx = Donation::query()
            ->with('category:id,name')
            ->where('mosque_id', $mosqueId)
            ->where('status', 'confirmed')
            ->where('donation_date', '>=', $sixMonthsAgo)
            ->get(['amount', 'donation_date', 'donation_category_id']);

        $donationChart = [
            'labels' => $financeChart['labels'],
            'datasets' => [],
        ];

        $categories = collect($donationsTx->pluck('category.name')->filter()->unique()->values()->all());
        
        foreach ($categories as $cat) {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonthsNoOverflow($i);
                $monthKey = $date->format('Y-m');
                
                $data[] = (float) $donationsTx->filter(function($d) use ($monthKey, $cat) {
                    return optional($d->donation_date)->format('Y-m') === $monthKey && optional($d->category)->name === $cat;
                })->sum('amount');
            }
            $donationChart['datasets'][] = [
                'label' => $cat,
                'data' => $data,
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'cards' => $cards,
            'financeChart' => $financeChart,
            'donationChart' => $donationChart,
            'recentFinance' => FinanceTransaction::query()
                ->with(['category:id,name', 'account:id,name'])
                ->where('mosque_id', $mosqueId)
                ->latest('transaction_date')
                ->limit(5)
                ->get()
                ->map(fn (FinanceTransaction $transaction): array => [
                    'id' => $transaction->id,
                    'title' => $transaction->title,
                    'reference_no' => $transaction->reference_no,
                    'amount' => (float) $transaction->amount,
                    'entry_type' => $transaction->entry_type,
                    'status' => $transaction->status,
                    'category' => $transaction->category?->name,
                    'transaction_date' => optional($transaction->transaction_date)->toDateString(),
                ])
                ->all(),
            'recentDonations' => Donation::query()
                ->with('donor:id,name')
                ->where('mosque_id', $mosqueId)
                ->latest('donation_date')
                ->limit(5)
                ->get()
                ->map(fn (Donation $donation): array => [
                    'id' => $donation->id,
                    'donor_name' => $donation->donor?->name ?? 'Donatur Manual',
                    'campaign' => $donation->campaign,
                    'amount' => (float) $donation->amount,
                    'status' => $donation->status,
                    'donation_date' => optional($donation->donation_date)->toDateString(),
                ])
                ->all(),
            'upcomingSchedules' => $this->upcomingSchedules($mosqueId),
            'activeAnnouncements' => Announcement::query()
                ->where('mosque_id', $mosqueId)
                ->where('status', 'published')
                ->latest('published_at')
                ->limit(5)
                ->get(['id', 'title', 'published_at'])
                ->map(fn (Announcement $announcement): array => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'published_at' => optional($announcement->published_at)->format('d M Y H:i'),
                ])
                ->all(),
        ]);
    }

    private function upcomingSchedules(?int $mosqueId): array
    {
        $prayerSchedules = PrayerSchedule::query()
            ->where('mosque_id', $mosqueId)
            ->whereDate('schedule_date', '>=', now()->toDateString())
            ->whereIn('status', ['draft', 'published'])
            ->orderBy('schedule_date')
            ->limit(3)
            ->get()
            ->map(fn (PrayerSchedule $schedule): array => [
                'id' => 'prayer-'.$schedule->id,
                'title' => ucfirst($schedule->prayer_name),
                'subtitle' => trim(collect([$schedule->imam_name ? 'Imam: '.$schedule->imam_name : null, $schedule->muadzin_name ? 'Muadzin: '.$schedule->muadzin_name : null])->filter()->implode(' • ')),
                'scheduled_at' => $schedule->schedule_date?->format('d M Y').($schedule->prayer_time ? ' '.$schedule->prayer_time : ''),
                'status' => $schedule->status,
                'sort_key' => $schedule->schedule_date?->format('Y-m-d').' '.($schedule->prayer_time ?: '00:00'),
            ]);

        $serviceSchedules = ServiceSchedule::query()
            ->where('mosque_id', $mosqueId)
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['draft', 'published'])
            ->orderBy('scheduled_at')
            ->limit(3)
            ->get()
            ->map(fn (ServiceSchedule $schedule): array => [
                'id' => 'service-'.$schedule->id,
                'title' => $schedule->title,
                'subtitle' => ucfirst($schedule->role_type).' • '.$schedule->person_name,
                'scheduled_at' => optional($schedule->scheduled_at)->format('d M Y H:i'),
                'status' => $schedule->status,
                'sort_key' => optional($schedule->scheduled_at)->format('Y-m-d H:i:s'),
            ]);

        return Collection::make($prayerSchedules)
            ->merge($serviceSchedules)
            ->sortBy('sort_key')
            ->take(5)
            ->map(function (array $item): array {
                unset($item['sort_key']);

                return $item;
            })
            ->values()
            ->all();
    }
}
