<?php

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function findAll(int $limit = 10, array $orderBy = []): array;

    public function findOneBy(string $userId): array;
}
