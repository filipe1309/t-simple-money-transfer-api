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
}
