<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TransactionProcessedEvent extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public array $transactionInfo
    ) {
    }
}
