<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function create(array $data): array
    {
        return $this->model::create($data)->toArray();
    }

    /**
     * @param string $id
     * @return array
     */
    public function findOneBy(string $id): array
    {
        $builder = $this->model::query();
        return $builder->where('id', $id)->firstOrFail()->toArray();
    }
}
