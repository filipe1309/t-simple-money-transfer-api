<?php

namespace App\Contracts;

interface UserServiceInterface
{
    public function findAll(int $limit = 10, array $orderBy = []): array;

    public function findOneBy(string $userId): array;
}
