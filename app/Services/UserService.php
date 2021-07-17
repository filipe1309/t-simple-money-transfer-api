<?php

namespace App\Services;

use App\Repositories\UserRepository;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserService
{
    public function __construct(
        private UserRepository $repository
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

    /**
     * @param string $string
     * @param array $searchFields
     * @param integer $limit
     * @param array $orderBy
     * @return array
     */
    public function searchBy(string $string, array $searchFields, int $limit = 10, array $orderBy = []): array
    {
        return $this->repository->searchBy($string, $searchFields, $limit, $orderBy);
    }
}
