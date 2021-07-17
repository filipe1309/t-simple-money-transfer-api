<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TransactionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $username,
        public string $message,
        public bool $status
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('SMT - Transaction')->markdown('mail.notification-mail');
    }
}
