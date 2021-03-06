<?php

namespace App\Jobs;

use App\Contracts\TransactionRepositoryInterface;
use App\Contracts\TransactionServiceInterface;
use App\Contracts\WalletRepositoryInterface;
use App\Events\TransactionProcessedEvent;
use App\Exceptions\NotEnoughtBalanceException;
use App\Exceptions\PayerIsAShopKeeperException;
use App\Exceptions\TransactionNotAuthorizedException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProcessTransactionJob implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;

    private TransactionRepositoryInterface $transactionRepo;
    private WalletRepositoryInterface $walletRepository;
    private TransactionServiceInterface $transactionService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public array $transaction
    ) {
        $this->transactionRepo = app(TransactionRepositoryInterface::class);
        $this->walletRepository = app(WalletRepositoryInterface::class);
        $this->transactionService = app(TransactionServiceInterface::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (!$this->transactionService->payerWalletHasEnoughBalance($this->transaction['payer_wallet_id'], $this->transaction['value'])) {
                throw new NotEnoughtBalanceException();
            }

            if (!$this->transactionService->payerIsACommonUser($this->transaction['payer_wallet_id'])) {
                throw new PayerIsAShopKeeperException();
            }

            if (!$this->transactionService->isTransactionAuthorized($this->transaction)) {
                throw new TransactionNotAuthorizedException();
            }

            DB::beginTransaction();

            $this->processWallets();
            $this->processTransaction();

            DB::commit();

            $this->dispatchNotificationEvents();
        } catch (NotEnoughtBalanceException | PayerIsAShopKeeperException | TransactionNotAuthorizedException $e) {
            DB::rollback();
            $this->dispatchFailedNotificationEvent($e->getMessage());
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }
    private function processWallets(): void
    {
        $payerWallet = $this->walletRepository->findOneBy($this->transaction['payer_wallet_id']);
        $payerWalletBalance = bcsub($payerWallet['balance'], $this->transaction['value'], 2);
        $this->walletRepository->updateBy($this->transaction['payer_wallet_id'], ['balance' => $payerWalletBalance]);

        $payeeWallet = $this->walletRepository->findOneBy($this->transaction['payee_wallet_id']);
        $payeeWalletBalance = bcadd($payeeWallet['balance'], $this->transaction['value'], 2);
        $this->walletRepository->updateBy($this->transaction['payee_wallet_id'], ['balance' => $payeeWalletBalance]);
    }

    private function processTransaction(): void
    {
        $this->transactionRepo->updateBy($this->transaction['id'], ['processed' => true]);
    }

    private function dispatchNotificationEvents(): void
    {
        event(new TransactionProcessedEvent([
            'status' => true,
            'wallet_id' => $this->transaction['payer_wallet_id'],
            'message' => 'Transaction processed successfully'
        ]));
        event(new TransactionProcessedEvent([
            'status' => true,
            'wallet_id' => $this->transaction['payee_wallet_id'],
            'message' => 'Good news, you receive a transaction  of $ ' . $this->transaction['value']
        ]));
    }

    private function dispatchFailedNotificationEvent(string $reason): void
    {
        event(new TransactionProcessedEvent([
            'status' => false,
            'wallet_id' => $this->transaction['payer_wallet_id'],
            'message' => 'Transaction failed, reason: ' . $reason
        ]));
    }
}
