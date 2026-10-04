<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Slaughtering;

class SlaughteringService
{
    public function assignVolunteers(Slaughtering $slaughtering, array $volunteerIds): Slaughtering
    {
        $animal = $slaughtering->animal;
        if ($animal->type === 'sapi' && count($volunteerIds) !== 7) {
            abort(422, 'Cow slaughtering requires exactly 7 administrative volunteers.');
        }

        $attach = collect($volunteerIds)
            ->mapWithKeys(fn (int $id): array => [$id => ['team_role' => 'administrasi']])
            ->all();

        $slaughtering->volunteers()->sync($attach);

        return $slaughtering->refresh();
    }

    public function markCut(Slaughtering $slaughtering, ?string $cutTime, ?float $meatTotalKg): Slaughtering
    {
        $slaughtering->update([
            'cut_time' => $cutTime,
            'meat_total_kg' => $meatTotalKg,
        ]);

        Animal::query()->whereKey($slaughtering->animal_id)->update(['status' => 'slaughtered']);

        return $slaughtering->refresh();
    }
}
