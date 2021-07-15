<?php

namespace App\Events;

class NewTransactionCreatedEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        private array $transaction
    ) {
    }
}
