<?php

namespace App\Contracts;

interface TransactionServiceInterface
{
    public function create(array $transaction): array;

    public function payerWalletHasEnoughBalance(string $payerWalletId, float $value): bool;

    public function payerIsACommonUser(string $payerWalletId): bool;

    public function isTransactionAuthorized(array $transaction): bool;
}
