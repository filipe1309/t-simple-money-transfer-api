<?php

namespace App\Contracts;

interface WalletRepositoryInterface
{
    public function create(array $data): array;

    public function updateBy(string $walletId, array $data): bool;

    public function findOneBy(string $walletId): array;
}
