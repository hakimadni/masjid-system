<?php

namespace App\Services;

use App\Models\Animal;
use Illuminate\Support\Str;

class AnimalService
{
    public function create(array $data): Animal
    {
        $data['qr_token'] = (string) Str::uuid();

        if (($data['slaughter_type'] ?? 'onsite') === 'onsite') {
            $data['vendor'] = null;
            $data['vendor_cost'] = null;
            $data['pickup_schedule'] = null;
            $data['external_status'] = null;
        }

        return Animal::query()->create($data);
    }

    public function update(Animal $animal, array $data): Animal
    {
        if (($data['slaughter_type'] ?? $animal->slaughter_type) === 'onsite') {
            $data['vendor'] = null;
            $data['vendor_cost'] = null;
            $data['pickup_schedule'] = null;
            $data['external_status'] = null;
        }

        $animal->update($data);

        return $animal->refresh();
    }
}
