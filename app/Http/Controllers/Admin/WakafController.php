<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\WakafWakif;
use App\Models\WakafRecord;
use App\Models\WakafUsage;
use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WakafController extends Controller
{
    public function index(): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $wakif = WakafWakif::where('mosque_id', $mosque->id)->get();

        $records = WakafRecord::where('mosque_id', $mosque->id)
            ->with('wakif')
            ->latest()
            ->limit(50)
            ->get();

        $usages = WakafUsage::where('mosque_id', $mosque->id)
            ->with('record')
            ->latest()
            ->limit(50)
            ->get();

        $summary = [
            'total_pledged' => WakafRecord::where('mosque_id', $mosque->id)
                ->where('status', 'pledged')
                ->sum('amount'),
            'total_received' => WakafRecord::where('mosque_id', $mosque->id)
                ->where('status', 'received')
                ->sum('amount'),
            'total_allocated' => WakafRecord::where('mosque_id', $mosque->id)
                ->where('status', 'allocated')
                ->sum('amount'),
            'total_completed' => WakafRecord::where('mosque_id', $mosque->id)
                ->where('status', 'completed')
                ->sum('amount'),
            'total_usage' => WakafUsage::where('mosque_id', $mosque->id)
                ->sum('amount'),
        ];

        return Inertia::render('Admin/Wakaf/Index', [
            'wakif' => $wakif,
            'records' => $records,
            'usages' => $usages,
            'summary' => $summary,
        ]);
    }

    public function storeWakif(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'display_publicly' => 'boolean',
        ]);

        WakafWakif::create([
            'mosque_id' => $mosque->id,
            ...$validated,
        ]);

        return back()->with('success', 'Wakif berhasil ditambahkan.');
    }

    public function storeRecord(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'wakaf_wakif_id' => 'nullable|exists:wakaf_wakif,id',
            'wakaf_type' => 'required|in:uang,aset,fidiyah',
            'amount' => 'numeric|min:0',
            'asset_description' => 'nullable|string',
            'purpose' => 'nullable|string',
            'pledged_date' => 'nullable|date',
            'status' => 'required|in:pledged,received,allocated,completed,cancelled',
        ]);

        $record = WakafRecord::create([
            'mosque_id' => $mosque->id,
            ...$validated,
        ]);

        // Create finance transaction for received monetary wakaf
        if ($validated['status'] === 'received' && $validated['wakaf_type'] === 'uang' && $validated['amount'] > 0) {
            $account = FinanceAccount::where('mosque_id', $mosque->id)
                ->where('is_active', true)
                ->first();

            if ($account) {
                FinanceTransaction::create([
                    'mosque_id' => $mosque->id,
                    'finance_account_id' => $account->id,
                    'finance_category_id' => null,
                    'title' => 'Wakaf - ' . ($record->wakif?->name ?? 'Umum'),
                    'entry_type' => 'income',
                    'amount' => $validated['amount'],
                    'payment_method' => 'transfer',
                    'transaction_date' => $validated['received_date'] ?? now(),
                    'status' => 'approved',
                    'notes' => $validated['purpose'] ?? 'Wakaf dana',
                ]);
            }
        }

        return back()->with('success', 'Data wakaf berhasil ditambahkan.');
    }

    public function updateRecordStatus(WakafRecord $record, Request $request): Response
    {
        $validated = $request->validate([
            'status' => 'required|in:pledged,received,allocated,completed,cancelled',
        ]);

        $record->update($validated);

        // Auto-set dates based on status
        if ($validated['status'] === 'received' && !$record->received_date) {
            $record->update(['received_date' => now()]);
        } elseif ($validated['status'] === 'allocated' && !$record->allocated_date) {
            $record->update(['allocated_date' => now()]);
        } elseif ($validated['status'] === 'completed' && !$record->completed_date) {
            $record->update(['completed_date' => now()]);
        }

        return back()->with('success', 'Status wakaf diperbarui.');
    }

    public function storeUsage(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'wakaf_record_id' => 'required|exists:wakaf_records,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'usage_date' => 'required|date',
        ]);

        WakafUsage::create([
            'mosque_id' => $mosque->id,
            ...$validated,
        ]);

        return back()->with('success', 'Penggunaan wakaf berhasil dicatat.');
    }
}