<?php

namespace App\Jobs;

use App\Exceptions\NotEnoughtBalanceException;
use App\Exceptions\PayerIsAShopKeeperException;
use App\Exceptions\TransactionNotAuthorizedException;
use App\Repositories\TransactionRepository;
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

            $this->transactionRepository->updateBy($this->transaction['id'], ['processed' => true]);

            DB::commit();
        } catch (Throwable $e) {
            dd($e);
            throw $e;
        }
    }
}
