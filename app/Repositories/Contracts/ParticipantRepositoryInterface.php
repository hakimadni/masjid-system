<?php

namespace App\Repositories\Contracts;

use App\Models\Participant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ParticipantRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Participant;
}
