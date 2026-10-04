<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Distribution;
use App\Models\Slaughtering;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistributionService
{
    public function markDelivered(Distribution $distribution): Distribution
    {
        $distribution->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'handled_by' => Auth::id(),
        ]);

        if ($distribution->animal_id !== null) {
            Animal::query()->whereKey($distribution->animal_id)->update(['status' => 'distributed']);
        }

        $this->syncSlaughteringStatus($distribution);

        return $distribution->refresh();
    }

    protected function syncSlaughteringStatus(Distribution $distribution): void
    {
        $slaughteringId = $distribution->slaughtering_id;

        if ($slaughteringId === null) {
            return;
        }

        $slaughtering = Slaughtering::query()->find($slaughteringId);
        if ($slaughtering === null) {
            return;
        }

        $totalDistributions = $slaughtering->distributions()->count();
        $deliveredDistributions = $slaughtering->distributions()->where('status', 'delivered')->count();

        if ($totalDistributions === 0) {
            return;
        }

        $newStatus = match (true) {
            $deliveredDistributions === 0 => 'pending',
            $deliveredDistributions < $totalDistributions => 'in_progress',
            $deliveredDistributions >= $totalDistributions => 'completed',
        };

        if ($slaughtering->distribution_status !== $newStatus) {
            $slaughtering->update(['distribution_status' => $newStatus]);
        }
    }
}
