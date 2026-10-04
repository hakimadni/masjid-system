<?php

namespace App\Repositories\Contracts;

use App\Models\Animal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AnimalRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Animal;
}
