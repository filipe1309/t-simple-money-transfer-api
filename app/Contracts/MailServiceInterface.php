<?php

namespace App\Contracts;

interface MailServiceInterface
{
    public function send(array $transactionInfo): void;
}
