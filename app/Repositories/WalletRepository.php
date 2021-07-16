<?php

namespace App\Repositories;

use App\Models\Wallet;

class WalletRepository
{
    public function __construct(Wallet $model)
    {
        $this->model = $model;
    }

    public function create(array $data): array
    {
        return $this->model::create($data)->toArray();
    }

    /**
     * @param string $id
     * @param array $data
     * @return boolean
     */
    public function updateBy(string $id, array $data): bool
    {
        $result = $this->model::where('id', $id)->firstOrFail()
            ->update($data);

        return (bool) $result;
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
