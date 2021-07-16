<?php

namespace App\Listeners;

use App\Events\TransactionProcessedEvent;
use App\Services\MailService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransactionProcessedMailListener implements ShouldQueue
{
    public $queue = 'notificationEventQueue';

    public $delay = 2;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        private MailService $mailService
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

    /**
     * Handle a job failure.
     *
     * @param  \App\Events\OrderShipped  $event
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(TransactionProcessedEvent $event, $exception)
    {
        dd($exception);
    }
}
