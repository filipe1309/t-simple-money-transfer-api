<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Contracts\UserServiceInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {
    }

    /**
     * @param integer $limit
     * @param array $orderBy
     * @return array
     */
    public function findAll(int $limit = 10, array $orderBy = []): array
    {
        return $this->repository->findAll($limit, $orderBy);
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findOneBy(string $userId): array
    {
        return $this->repository->findOneBy($userId);
    }
}
