<?php

namespace App\Repositories\Eloquent;

use App\Models\Participant;
use App\Repositories\Contracts\ParticipantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ParticipantRepository implements ParticipantRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Participant::query()->with(['animal', 'qurbanSaving'])->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Participant
    {
        return Participant::query()->findOrFail($id);
    }
}
