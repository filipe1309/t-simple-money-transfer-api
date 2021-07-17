<?php

namespace App\Events;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
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
