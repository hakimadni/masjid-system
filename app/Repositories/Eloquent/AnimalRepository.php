<?php

namespace App\Repositories\Eloquent;

use App\Models\Animal;
use App\Repositories\Contracts\AnimalRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AnimalRepository implements AnimalRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Animal::query()->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): Animal
    {
        return Animal::query()->findOrFail($id);
    }
}
