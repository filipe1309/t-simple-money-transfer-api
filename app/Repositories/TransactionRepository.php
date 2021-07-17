<?php

namespace App\Repositories;

use App\Models\Transaction;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TransactionRepository
{
    public function __construct(
        private Transaction $model
    ) {
    }

    public function create(array $data): array
    {
        return $this->model::create($data)->toArray();
    }

    /**
     * @param string $transactionId
     * @param array $data
     * @return boolean
     */
    public function updateBy(string $transactionId, array $data): bool
    {
        $result = $this->model::where('id', $transactionId)->firstOrFail()
            ->update($data);

        return (bool) $result;
    }
}
