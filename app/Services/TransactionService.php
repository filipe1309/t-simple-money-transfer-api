<?php

namespace App\Services;

use App\Events\NewTransactionCreatedEvent;
use App\Jobs\ProcessTransactionJob;
use App\Models\Wallet;
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

        $payer_wallet_id = Wallet::where('user_id', $data['payer'])->first()->id;
        $payee_wallet_id = Wallet::where('user_id', $data['payee'])->first()->id;
        $transaction_id = Uuid::uuid4()->toString();


        $transaction = $this->repository->create(
            [
                'id' => $transaction_id,
                'payer_wallet_id' => $payer_wallet_id,
                'payee_wallet_id' => $payee_wallet_id,
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
