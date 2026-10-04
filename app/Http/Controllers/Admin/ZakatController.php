<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\Mosque;
use App\Models\ZakatMuzakki;
use App\Models\ZakatMustahik;
use App\Models\ZakatDistribution;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ZakatController extends Controller
{
    public function index(): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $muzakki = ZakatMuzakki::where('mosque_id', $mosque->id)
            ->latest()
            ->limit(50)
            ->get();

        $mustahik = ZakatMustahik::where('mosque_id', $mosque->id)
            ->where('is_active', true)
            ->get();

        $distributions = ZakatDistribution::where('mosque_id', $mosque->id)
            ->with('mustahik')
            ->latest()
            ->limit(50)
            ->get();

        $summary = [
            'total_fitrah' => ZakatMuzakki::where('mosque_id', $mosque->id)
                ->where('zakat_type', 'fitrah')
                ->where('status', 'confirmed')
                ->sum('money_amount'),
            'total_mal' => ZakatMuzakki::where('mosque_id', $mosque->id)
                ->where('zakat_type', 'mal')
                ->where('status', 'confirmed')
                ->sum('money_amount'),
            'total_rice' => ZakatMuzakki::where('mosque_id', $mosque->id)
                ->where('status', 'confirmed')
                ->sum('rice_amount'),
            'distributed_money' => ZakatDistribution::where('mosque_id', $mosque->id)
                ->sum('money_amount'),
            'distributed_rice' => ZakatDistribution::where('mosque_id', $mosque->id)
                ->sum('rice_amount'),
        ];

        return Inertia::render('Admin/Zakat/Index', [
            'muzakki' => $muzakki,
            'mustahik' => $mustahik,
            'distributions' => $distributions,
            'summary' => $summary,
            'mosque' => $mosque,
        ]);
    }

    public function storeMuzakki(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'zakat_type' => 'required|in:fitrah,mal',
            'payment_form' => 'required|in:uang,beras',
            'money_amount' => 'numeric|min:0',
            'rice_amount' => 'numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $muzakki = ZakatMuzakki::create([
            'mosque_id' => $mosque->id,
            ...$validated,
        ]);

        return back()->with('success', 'Data muzakki berhasil ditambahkan.');
    }

    public function updateMuzakkiStatus(ZakatMuzakki $muzakki, Request $request): Response
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,rejected',
        ]);

        $muzakki->update($validated);

        // Create finance transaction for confirmed zakat
        if ($validated['status'] === 'confirmed' && $muzakki->money_amount > 0) {
            $account = \App\Models\FinanceAccount::where('mosque_id', $muzakki->mosque_id)
                ->where('is_active', true)
                ->first();

            if ($account) {
                FinanceTransaction::create([
                    'mosque_id' => $muzakki->mosque_id,
                    'finance_account_id' => $account->id,
                    'finance_category_id' => null,
                    'title' => "Zakat {$muzakki->zakat_type} - {$muzakki->name}",
                    'entry_type' => 'income',
                    'amount' => $muzakki->money_amount,
                    'payment_method' => 'transfer',
                    'transaction_date' => $muzakki->payment_date,
                    'status' => 'approved',
                    'notes' => $muzakki->notes,
                ]);
            }
        }

        return back()->with('success', 'Status muzakki diperbarui.');
    }

    public function storeMustahik(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'category' => 'required|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $mustahik = ZakatMustahik::create([
            'mosque_id' => $mosque->id,
            ...$validated,
        ]);

        return back()->with('success', 'Data mustahik berhasil ditambahkan.');
    }

    public function storeDistribution(Request $request): Response
    {
        $mosque = Mosque::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'mustahik_id' => 'required|exists:zakat_mustahik,id',
            'zakat_type' => 'required|in:fitrah,mal',
            'money_amount' => 'numeric|min:0',
            'rice_amount' => 'numeric|min:0',
            'distribution_date' => 'required|date',
            'received' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $distribution = ZakatDistribution::create([
            'mosque_id' => $mosque->id,
            ...$validated,
        ]);

        // Create expense transaction for money distribution
        if ($validated['money_amount'] > 0) {
            $account = \App\Models\FinanceAccount::where('mosque_id', $mosque->id)
                ->where('is_active', true)
                ->first();

            if ($account) {
                FinanceTransaction::create([
                    'mosque_id' => $mosque->id,
                    'finance_account_id' => $account->id,
                    'finance_category_id' => null,
                    'title' => "Distribusi Zakat {$validated['zakat_type']} - {$distribution->mustahik->name}",
                    'entry_type' => 'expense',
                    'amount' => $validated['money_amount'],
                    'payment_method' => 'cash',
                    'transaction_date' => $validated['distribution_date'],
                    'status' => 'approved',
                    'notes' => $validated['notes'],
                ]);
            }
        }

        return back()->with('success', 'Data distribusi zakat berhasil ditambahkan.');
    }
}