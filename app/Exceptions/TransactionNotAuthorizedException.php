<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TransactionNotAuthorizedException extends Exception
{
    public function __construct(string $message = 'Transaction not authorized', int $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
