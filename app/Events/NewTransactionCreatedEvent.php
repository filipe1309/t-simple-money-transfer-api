<?php

namespace App\Events;

use App\Models\Transaction;

class NewTransactionCreatedEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        private Transaction $transaction
    ) {
    }
}
