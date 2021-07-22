<?php

namespace App\Contracts;

interface TransactionRepositoryInterface
{
    public function create(array $data): array;

    public function updateBy(string $transactionId, array $data): bool;
}
