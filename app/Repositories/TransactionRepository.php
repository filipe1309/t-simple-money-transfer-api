<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    public function __construct(Transaction $model)
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
}
