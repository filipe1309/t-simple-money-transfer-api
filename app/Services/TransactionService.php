<?php

namespace App\Services;

use App\Events\NewTransactionCreatedEvent;
use App\Jobs\ProcessTransactionJob;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Ramsey\Uuid\Uuid;

class TransactionService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private WalletRepository $walletRepository,
        private UserRepository $userRepository,
        private ExternalAuthorizerService $externalAuthorizerService
    ) {
    }

    public function create(array $transaction): array
    {
        $transactionData = $this->prepareTransaction($transaction);

        $transactionDatabaseResponse = $this->transactionRepository->create($transactionData);

        dispatch(new ProcessTransactionJob($transactionDatabaseResponse))
            ->onQueue('transactionJobQueue');

        event(new NewTransactionCreatedEvent($transactionDatabaseResponse));

        return $transactionDatabaseResponse;
    }

    private function prepareTransaction(array $transaction): array
    {
        $transaction_id = Uuid::uuid4()->toString();

        return [
            'id' => $transaction_id,
            'payer_wallet_id' => $transaction['payer'],
            'payee_wallet_id' => $transaction['payee'],
            'value' => $transaction['value'],
            'processed' => false
        ];
    }

    public function payerWalletHasEnoughBalance(string $payer_wallet_id, $value): bool
    {
        $payer_wallet = $this->walletRepository->findOneBy($payer_wallet_id);
        return $payer_wallet['balance'] >= $value;
    }

    public function payerIsACommonUser(string $payer_wallet_id): bool
    {
        $payer_wallet = $this->walletRepository->findOneBy($payer_wallet_id);
        $payer = $this->userRepository->findOneBy($payer_wallet['user_id']);
        return !$payer['shopkeeper'];
    }

    public function isTransactionAuthorized(array $transaction): bool
    {
        return $this->externalAuthorizerService->authorize($transaction);
    }
}
