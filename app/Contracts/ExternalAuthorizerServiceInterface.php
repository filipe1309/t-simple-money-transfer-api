<?php

namespace App\Contracts;

interface ExternalAuthorizerServiceInterface
{
    public function authorize(array $transaction): bool;
}
