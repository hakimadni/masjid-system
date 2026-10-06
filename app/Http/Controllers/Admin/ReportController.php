<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Donation;
use App\Models\Event;
use App\Models\FinanceTransaction;
use App\Models\PrayerSchedule;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;
        $filters = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $filters['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $filters['end_date'] ?? now()->endOfMonth()->toDateString();

        $financeQuery = FinanceTransaction::query()
            ->where('mosque_id', $mosqueId)
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate);

        $donationQuery = Donation::query()
            ->where('mosque_id', $mosqueId)
            ->whereDate('donation_date', '>=', $startDate)
            ->whereDate('donation_date', '<=', $endDate);

        $eventQuery = Event::query()
            ->where('mosque_id', $mosqueId)
            ->whereDate('start_at', '>=', $startDate)
            ->whereDate('start_at', '<=', $endDate);

        // Chart Data Calculations
        $months = [];
        $incomeTotals = [];
        $expenseTotals = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->translatedFormat('M Y');
            
            $incomeTotals[] = (float) FinanceTransaction::where('mosque_id', $mosqueId)
                ->where('entry_type', 'income')
                ->where('status', 'approved')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount');
                
            $expenseTotals[] = (float) FinanceTransaction::where('mosque_id', $mosqueId)
                ->where('entry_type', 'expense')
                ->where('status', 'approved')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount');
        }

        $campaignBreakdownRaw = (clone $donationQuery)
            ->selectRaw('campaign, count(*) as total_records, sum(amount) as total_amount')
            ->groupBy('campaign')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        $assetConditionsRaw = Asset::query()
            ->where('mosque_id', $mosqueId)
            ->selectRaw('condition, count(*) as total')
            ->groupBy('condition')
            ->orderBy('condition')
            ->get();

        $chartData = [
            'keuangan_bulanan' => [
                'labels' => $months,
                'datasets' => [
                    [
                        'label' => 'Pemasukan',
                        'backgroundColor' => '#10b981',
                        'data' => $incomeTotals
                    ],
                    [
                        'label' => 'Pengeluaran',
                        'backgroundColor' => '#f87171',
                        'data' => $expenseTotals
                    ]
                ]
            ],
            'donasi_campaign' => [
                'labels' => $campaignBreakdownRaw->pluck('campaign')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Total Donasi',
                        'backgroundColor' => ['#f472b6', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa'],
                        'data' => $campaignBreakdownRaw->pluck('total_amount')->toArray()
                    ]
                ]
            ],
            'aset_kondisi' => [
                'labels' => $assetConditionsRaw->pluck('condition')->map(fn($c) => ucfirst($c))->toArray(),
                'datasets' => [
                    [
                        'label' => 'Jumlah Aset',
                        'backgroundColor' => ['#34d399', '#fbbf24', '#f87171'],
                        'data' => $assetConditionsRaw->pluck('total')->toArray()
                    ]
                ]
            ]
        ];

        return Inertia::render('Admin/Reports/Index', [
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'chartData' => $chartData,
            'summary' => [
                'income_total' => (float) (clone $financeQuery)
                    ->where('entry_type', 'income')
                    ->where('status', 'approved')
                    ->sum('amount'),
                'expense_total' => (float) (clone $financeQuery)
                    ->where('entry_type', 'expense')
                    ->where('status', 'approved')
                    ->sum('amount'),
                'donation_total' => (float) (clone $donationQuery)
                    ->where('status', 'confirmed')
                    ->sum('amount'),
                'event_total' => (int) (clone $eventQuery)->count(),
                'asset_total' => (int) Asset::query()
                    ->where('mosque_id', $mosqueId)
                    ->count(),
                'schedule_total' => (int) PrayerSchedule::query()
                    ->where('mosque_id', $mosqueId)
                    ->whereDate('schedule_date', '>=', $startDate)
                    ->whereDate('schedule_date', '<=', $endDate)
                    ->count() + (int) ServiceSchedule::query()
                    ->where('mosque_id', $mosqueId)
                    ->whereDate('scheduled_at', '>=', $startDate)
                    ->whereDate('scheduled_at', '<=', $endDate)
                    ->count(),
            ],
            'recentFinance' => (clone $financeQuery)
                ->latest('transaction_date')
                ->limit(5)
                ->get(['id', 'title', 'entry_type', 'status', 'amount', 'transaction_date'])
                ->map(fn (FinanceTransaction $transaction): array => [
                    'id' => $transaction->id,
                    'title' => $transaction->title,
                    'entry_type' => $transaction->entry_type,
                    'status' => $transaction->status,
                    'amount' => (float) $transaction->amount,
                    'transaction_date' => optional($transaction->transaction_date)?->toDateString(),
                ]),
            'campaignBreakdown' => (clone $donationQuery)
                ->selectRaw('campaign, count(*) as total_records, sum(amount) as total_amount')
                ->groupBy('campaign')
                ->orderByDesc('total_amount')
                ->limit(5)
                ->get()
                ->map(fn ($item): array => [
                    'campaign' => $item->campaign,
                    'total_records' => (int) $item->total_records,
                    'total_amount' => (float) $item->total_amount,
                ]),
            'upcomingEvents' => Event::query()
                ->where('mosque_id', $mosqueId)
                ->where('start_at', '>=', now())
                ->orderBy('start_at')
                ->limit(5)
                ->get(['id', 'title', 'location', 'status', 'start_at'])
                ->map(fn (Event $event): array => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'location' => $event->location,
                    'status' => $event->status,
                    'start_at' => optional($event->start_at)?->format('Y-m-d H:i'),
                ]),
            'assetConditions' => Asset::query()
                ->where('mosque_id', $mosqueId)
                ->selectRaw('condition, count(*) as total')
                ->groupBy('condition')
                ->orderBy('condition')
                ->get()
                ->map(fn ($item): array => [
                    'condition' => $item->condition,
                    'total' => (int) $item->total,
                ]),
        ]);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $mosqueId = $request->user()->mosque_id;
        $type = $request->get('type', 'keuangan');
        $fileName = "laporan-{$type}-{$request->start_date}-{$request->end_date}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        if ($type === 'donasi') {
            $donations = Donation::query()
                ->where('mosque_id', $mosqueId)
                ->where('status', 'confirmed')
                ->whereDate('donation_date', '>=', $request->start_date)
                ->whereDate('donation_date', '<=', $request->end_date)
                ->orderBy('donation_date', 'desc')
                ->get();

            $callback = function () use ($donations) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Tanggal', 'Campaign', 'Jumlah', 'Metode']);
                foreach ($donations as $row) {
                    fputcsv($file, [
                        optional($row->donation_date)->toDateString(),
                        $row->campaign,
                        number_format((float) $row->amount, 0, ',', '.'),
                        $row->payment_method ?? '-',
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($type === 'aset') {
            $assets = Asset::query()
                ->where('mosque_id', $mosqueId)
                ->orderBy('name', 'asc')
                ->get();

            $callback = function () use ($assets) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Nama Aset', 'Kategori', 'Kondisi', 'Tanggal Perolehan']);
                foreach ($assets as $row) {
                    fputcsv($file, [
                        $row->name,
                        $row->category ?? '-',
                        $row->condition,
                        optional($row->acquisition_date)->toDateString() ?? '-',
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // Default: Keuangan
        $finance = FinanceTransaction::query()
            ->with('category:id,name')
            ->where('mosque_id', $mosqueId)
            ->where('status', 'approved')
            ->whereDate('transaction_date', '>=', $request->start_date)
            ->whereDate('transaction_date', '<=', $request->end_date)
            ->orderBy('transaction_date', 'desc')
            ->get(['transaction_date', 'title', 'entry_type', 'amount', 'finance_category_id']);

        $callback = function () use ($finance) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Judul', 'Tipe', 'Jumlah', 'Kategori']);
            foreach ($finance as $row) {
                fputcsv($file, [
                    optional($row->transaction_date)?->toDateString(),
                    $row->title,
                    $row->entry_type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    number_format((float) $row->amount, 0, ',', '.'),
                    $row->category?->name ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
