<?php

namespace App\Listeners;

use App\Contracts\MailServiceInterface;
use App\Events\TransactionProcessedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SendTransactionProcessedMailListener implements ShouldQueue
{
    public string $queue = 'notificationEventQueue';

    public int $delay = 2;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        private MailServiceInterface $mailService
    ) {
    }

    /**
     * Handle the event.
     *
     * @param  TransactionProcessedEvent $event
     * @return void
     */
    public function handle(TransactionProcessedEvent $event)
    {
        try {
            $this->mailService->send($event->transactionInfo);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
