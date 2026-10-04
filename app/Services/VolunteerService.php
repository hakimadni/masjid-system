<?php

namespace App\Services;

use App\Models\Volunteer;
use Illuminate\Support\Str;

class VolunteerService
{
    public function create(array $data): Volunteer
    {
        $availabilities = $data['availabilities'] ?? [];
        unset($data['availabilities']);

        $data['qr_token'] = (string) Str::uuid();

        $volunteer = Volunteer::query()->create($data);

        if ($availabilities !== []) {
            $volunteer->availabilities()->createMany($availabilities);
        }

        return $volunteer->refresh();
    }

    public function update(Volunteer $volunteer, array $data): Volunteer
    {
        $availabilities = $data['availabilities'] ?? null;
        unset($data['availabilities']);

        $volunteer->update($data);

        if (is_array($availabilities)) {
            $volunteer->availabilities()->delete();
            $volunteer->availabilities()->createMany($availabilities);
        }

        return $volunteer->refresh();
    }
}
