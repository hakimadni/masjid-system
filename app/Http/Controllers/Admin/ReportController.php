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

        return Inertia::render('Admin/Reports/Index', [
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
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
        
        $finance = FinanceTransaction::query()
            ->with('category:id,name')
            ->where('mosque_id', $mosqueId)
            ->where('status', 'approved')
            ->orderBy('transaction_date', 'desc')
            ->get(['transaction_date', 'title', 'entry_type', 'amount', 'finance_category_id']);

        $fileName = "laporan-keuangan-{$request->start_date}-{$request->end_date}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

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
