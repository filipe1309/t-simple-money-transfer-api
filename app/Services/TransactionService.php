<?php

namespace App\Services;

use App\Contracts\TransactionRepositoryInterface;
use App\Contracts\TransactionServiceInterface;
use App\Events\NewTransactionCreatedEvent;
use App\Jobs\ProcessTransactionJob;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Ramsey\Uuid\Uuid;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepo,
        private WalletRepository $walletRepository,
        private UserRepository $userRepository,
        private ExternalAuthorizerService $externalAuthService
    ) {
    }

    public function create(array $transaction): array
    {
        $transactionData = $this->prepareTransaction($transaction);

        $transactionDBRes = $this->transactionRepo->create($transactionData);

        dispatch(new ProcessTransactionJob($transactionDBRes))
            ->onQueue('transactionJobQueue');

        event(new NewTransactionCreatedEvent($transactionDBRes));

        return $transactionDBRes;
    }

    private function prepareTransaction(array $transaction): array
    {
        $transactionId = Uuid::uuid4()->toString();

        return [
            'id' => $transactionId,
            'payer_wallet_id' => $transaction['payer'],
            'payee_wallet_id' => $transaction['payee'],
            'value' => $transaction['value'],
            'processed' => false
        ];
    }

    public function payerWalletHasEnoughBalance(string $payerWalletId, float $value): bool
    {
        $payerWallet = $this->walletRepository->findOneBy($payerWalletId);
        return $payerWallet['balance'] >= $value;
    }

    public function payerIsACommonUser(string $payerWalletId): bool
    {
        $payerWallet = $this->walletRepository->findOneBy($payerWalletId);
        $payer = $this->userRepository->findOneBy($payerWallet['user_id']);
        return !$payer['shopkeeper'];
    }

    public function isTransactionAuthorized(array $transaction): bool
    {
        return $this->externalAuthService->authorize($transaction);
    }
}
