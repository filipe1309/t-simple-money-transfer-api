<?php

namespace App\Jobs;

use App\Events\TransactionProcessedEvent;
use App\Exceptions\NotEnoughtBalanceException;
use App\Exceptions\PayerIsAShopKeeperException;
use App\Exceptions\TransactionNotAuthorizedException;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use App\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessTransactionJob implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;

    private TransactionRepository $transactionRepository;
    private WalletRepository $walletRepository;
    private TransactionService $transactionService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private array $transaction
    ) {
        $this->transactionRepository = app(TransactionRepository::class);
        $this->walletRepository = app(WalletRepository::class);
        $this->transactionService = app(TransactionService::class);
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
        } catch (Throwable $e) {
            dd($e);
            throw $e;
        }
    }
    private function processWallets(): void
    {
        $payerWallet = $this->walletRepository->findOneBy($this->transaction['payer_wallet_id']);
        $payerWalletNewBalance = bcsub($payerWallet['balance'], $this->transaction['value'], 2);
        $this->walletRepository->updateBy($this->transaction['payer_wallet_id'], ['balance' => $payerWalletNewBalance]);

        $payeeWallet = $this->walletRepository->findOneBy($this->transaction['payee_wallet_id']);
        $payeeWalletNewBalance = bcadd($payeeWallet['balance'], $this->transaction['value'], 2);
        $this->walletRepository->updateBy($this->transaction['payee_wallet_id'], ['balance' => $payeeWalletNewBalance]);
    }

    private function processTransaction(): void
    {
        $this->transactionRepository->updateBy($this->transaction['id'], ['processed' => true]);
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
            'message' => 'Good news, you receive a transaction  of ' . $this->transaction['value']
        ]));
    }
}
