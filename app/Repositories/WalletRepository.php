<?php

namespace App\Repositories;

use App\Contracts\WalletRepositoryInterface;
use App\Models\Wallet;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class WalletRepository implements WalletRepositoryInterface
{
    public function __construct(
        private Wallet $model
    ) {
    }

    public function create(array $data): array
    {
        return $this->model::create($data)->toArray();
    }

    /**
     * @param string $walletId
     * @param array $data
     * @return boolean
     */
    public function updateBy(string $walletId, array $data): bool
    {
        $result = $this->model::where('id', $walletId)->firstOrFail()
            ->update($data);

        return (bool) $result;
    }

    /**
     * @param string $walletId
     * @return array
     */
    public function findOneBy(string $walletId): array
    {
        $builder = $this->model::query();
        return $builder->where('id', $walletId)->firstOrFail()->toArray();
    }
}
