<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotEnoughtBalanceException extends Exception
{
    public function __construct(string $message = 'Payer does not have enough balance', int $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
