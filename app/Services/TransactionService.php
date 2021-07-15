<?php

namespace App\Services;

use App\Events\NewTransactionCreatedEvent;
use App\Jobs\ProcessTransactionJob;
use App\Repositories\TransactionRepository;
use Ramsey\Uuid\Uuid;

class TransactionService
{
    public function __construct(
        private TransactionRepository $repository
    ) {
    }

    public function create(array $data): array
    {
        $transaction_id = Uuid::uuid4()->toString();

        $transaction = $this->repository->create(
            [
                'id' => $transaction_id,
                'payer_wallet_id' => $data['payer'],
                'payee_wallet_id' => $data['payee'],
                'value' => $data['value'],
                'processed' => false
            ]
        );

        dispatch(new ProcessTransactionJob($transaction))
            ->onQueue('transactionJobQueue');

        event(new NewTransactionCreatedEvent($transaction));

        return $transaction;
    }
}
