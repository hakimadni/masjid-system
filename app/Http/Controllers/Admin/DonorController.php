<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DonorController extends Controller
{
    public function index(Request $request): Response
    {
        $mosqueId = $request->user()->mosque_id;

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $query = Donor::query()
            ->where('donors.mosque_id', $mosqueId)
            ->leftJoin('donations', 'donors.id', '=', 'donations.donor_id')
            ->select([
                'donors.id',
                'donors.name',
                'donors.phone',
                'donors.email',
                'donors.address',
                'donors.notes',
                'donors.created_at',
                DB::raw('COUNT(donations.id) as donation_count'),
                DB::raw('COALESCE(SUM(CASE WHEN donations.status = \'confirmed\' THEN donations.amount ELSE 0 END), 0) as total_amount'),
                DB::raw('MAX(donations.donation_date) as last_donation_date'),
            ])
            ->groupBy(
                'donors.id', 'donors.name', 'donors.phone', 'donors.email',
                'donors.address', 'donors.notes', 'donors.created_at'
            )
            ->orderBy('donors.name');

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('donors.name', 'like', '%'.$search.'%')
                    ->orWhere('donors.phone', 'like', '%'.$search.'%')
                    ->orWhere('donors.email', 'like', '%'.$search.'%');
            });
        }

        $donors = $query->paginate(15)->withQueryString();

        // Donors with at least one donation = active
        $activeCount = Donor::query()
            ->where('mosque_id', $mosqueId)
            ->whereHas('donations')
            ->count();

        return Inertia::render('Admin/Donors/Index', [
            'filters' => $filters,
            'donors' => $donors,
            'summary' => [
                'total_donors' => Donor::query()->where('mosque_id', $mosqueId)->count(),
                'active_donors' => $activeCount,
                'total_donations' => DB::table('donations')
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'confirmed')
                    ->count(),
                'total_amount' => (int) DB::table('donations')
                    ->where('mosque_id', $mosqueId)
                    ->where('status', 'confirmed')
                    ->sum('amount'),
            ],
        ]);
    }
}
