<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnimalResource;
use App\Http\Resources\QurbanSavingResource;
use App\Http\Resources\SlaughteringResource;
use App\Models\Animal;
use App\Models\Distribution;
use App\Models\Participant;
use App\Models\QurbanSaving;
use App\Models\Slaughtering;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $totalAnimals = Animal::query()->count();
        $totalParticipants = Participant::query()->count();
        $totalSavings = QurbanSaving::query()->count();
        $activeSavings = QurbanSaving::query()->where('status', 'active')->count();
        $completedSavings = QurbanSaving::query()->where('status', 'completed')->count();
        $slaughtered = Animal::query()->where('status', 'slaughtered')->count();
        $distributed = Animal::query()->where('status', 'distributed')->count();
        $totalMeatKg = Slaughtering::query()->sum('meat_total_kg');

        $totalCollection = QurbanSaving::query()->sum('current_balance');

        $recentSavings = QurbanSavingResource::collection(
            QurbanSaving::with('user')->latest()->limit(5)->get()
        );

        $recentAnimals = AnimalResource::collection(
            Animal::withCount('participants')->latest()->limit(5)->get()
        );

        $recentSlaughterings = SlaughteringResource::collection(
            Slaughtering::with('animal:id,type,weight')
                ->withCount('volunteers')
                ->latest()
                ->limit(5)
                ->get()
        );

        return Inertia::render('Dashboard', [
            'analytics' => [
                'total_animals' => $totalAnimals,
                'total_participants' => $totalParticipants,
                'total_savings' => $totalSavings,
                'active_savings' => $activeSavings,
                'completed_savings' => $completedSavings,
                'total_collection' => $totalCollection,
                'slaughter_progress' => $totalAnimals > 0 ? round(($slaughtered / $totalAnimals) * 100, 2) : 0,
                'distribution_progress' => $totalAnimals > 0 ? round(($distributed / $totalAnimals) * 100, 2) : 0,
                'distribution_pending' => Distribution::query()->where('status', 'pending')->count(),
                'distribution_delivered' => Distribution::query()->where('status', 'delivered')->count(),
                'slaughtering_total' => Slaughtering::query()->count(),
                'total_meat_kg' => $totalMeatKg,
            ],
            'recent_savings' => $recentSavings,
            'recent_animals' => $recentAnimals,
            'recent_slaughterings' => $recentSlaughterings,
        ]);
    }
}
