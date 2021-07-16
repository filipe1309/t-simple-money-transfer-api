<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class PayerIsAShopKeeperException extends Exception
{
    public function __construct(string $message = 'Payer can\'t be a shopkeeper', int $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
