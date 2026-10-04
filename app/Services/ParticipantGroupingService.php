<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class ParticipantGroupingService
{
    public function addParticipant(array $data): Participant
    {
        return DB::transaction(function () use ($data): Participant {
            $animal = Animal::query()->lockForUpdate()->findOrFail($data['animal_id']);

            if ($animal->type === 'sapi') {
                $currentCount = $animal->participants()->count();
                if ($currentCount >= 7) {
                    abort(422, 'Cow already has 7 participants.');
                }
                $data['slot_number'] = $currentCount + 1;
            } else {
                $data['slot_number'] = null;
            }

            $participant = Participant::query()->create($data);

            $this->refreshAnimalStatus($animal);

            return $participant;
        });
    }

    public function autoGroupUnassignedCowParticipants(): int
    {
        $updated = 0;

        Animal::query()
            ->where('type', 'sapi')
            ->whereIn('status', ['available', 'assigned'])
            ->get()
            ->each(function (Animal $animal) use (&$updated): void {
                $count = $animal->participants()->count();
                if ($count === 7 && $animal->status !== 'assigned') {
                    $animal->update(['status' => 'assigned']);
                    $updated++;
                }
            });

        return $updated;
    }

    public function refreshAnimalStatus(Animal $animal): void
    {
        if ($animal->type === 'sapi') {
            $count = $animal->participants()->count();
            $animal->update(['status' => $count === 7 ? 'assigned' : 'available']);
        }
    }
}
