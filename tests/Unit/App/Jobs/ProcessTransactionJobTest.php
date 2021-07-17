<?php

use App\Events\TransactionProcessedEvent;
use App\Jobs\ProcessTransactionJob;
use App\Mail\TransactionNotificationMail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProcessTransactionJobTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->artisan('db:seed');
    }

    public function test_transactions_can_be_processed(): void
    {
        Event::fake([TransactionProcessedEvent::class]);

        $commomUser = User::factory()->create(['shopkeeper' => false]);
        $commomUserWallet = Wallet::factory()->create([
            'user_id' => $commomUser->id,
            'balance' => 100,
        ]);

        $shopkeeperUser = User::factory()->create(['shopkeeper' => true]);
        $shopkeeperUserWallet = Wallet::factory()->create(['user_id' => $shopkeeperUser->id]);

        $transactionValue = 50;

        $transaction = [
            'payer_wallet_id' => $commomUserWallet->id,
            'payee_wallet_id' => $shopkeeperUserWallet->id,
            'value' => $transactionValue
        ];

        $transactionDatabase = Transaction::factory()->create($transaction);

        $transaction['id'] = $transactionDatabase->id;

        $job = new ProcessTransactionJob($transaction);
        $job->handle();

        Event::assertDispatched(TransactionProcessedEvent::class, function ($event) {
            return $event->transactionInfo['status'];
        });
    }

    public function test_shopkeeper_cant_be_payer(): void
    {
        Event::fake([TransactionProcessedEvent::class]);

        $shopkeeperUser = User::factory()->create(['shopkeeper' => true]);
        $shopkeeperUserWallet = Wallet::factory()->create(['user_id' => $shopkeeperUser->id]);

        $transactionValue = 50;

        $transaction = [
            'payer_wallet_id' => $shopkeeperUserWallet->id,
            'value' => $transactionValue
        ];

        $transactionDatabase = Transaction::factory()->create($transaction);

        $transaction['id'] = $transactionDatabase->id;
        $transaction['payer_wallet_id'] = $transactionDatabase->payer_wallet_id;


        $job = new ProcessTransactionJob($transaction);
        $job->handle();

        Event::assertDispatched(TransactionProcessedEvent::class, function ($event) {
            return !$event->transactionInfo['status'];
        });
    }

    public function test_common_users_can_send_transactions_between_them(): void
    {
        Event::fake([TransactionProcessedEvent::class]);

        $commomUser = User::factory()->create(['shopkeeper' => false]);
        $commomUserWallet = Wallet::factory()->create([
            'user_id' => $commomUser->id,
            'balance' => 100,
        ]);

        $transactionValue = 50;

        $transaction = [
            // payer_wallet_id is a common user by default with 100 balance
            'payee_wallet_id' => $commomUserWallet->id,
            'value' => $transactionValue
        ];

        $transactionDatabase = Transaction::factory()->create($transaction);

        $transaction['id'] = $transactionDatabase->id;
        $transaction['payer_wallet_id'] = $transactionDatabase->payer_wallet_id;

        $job = new ProcessTransactionJob($transaction);
        $job->handle();

        Event::assertDispatched(TransactionProcessedEvent::class, function ($event) {
            return $event->transactionInfo['status'];
        });
    }

    public function test_common_user_with_enought_balance_can_transact(): void
    {
        Event::fake([TransactionProcessedEvent::class]);

        $transactionValue = 50;
        $transaction = ['value' => $transactionValue];

        $transactionDatabase = Transaction::factory()->create($transaction);

        $transaction['id'] = $transactionDatabase->id;
        $transaction['payer_wallet_id'] = $transactionDatabase->payer_wallet_id;
        $transaction['payee_wallet_id'] = $transactionDatabase->payee_wallet_id;

        $job = new ProcessTransactionJob($transaction);
        $job->handle();

        Event::assertDispatched(TransactionProcessedEvent::class, function ($event) {
            return $event->transactionInfo['status'];
        });
    }

    public function test_common_user_without_enought_balance_cant_transact(): void
    {
        Event::fake([TransactionProcessedEvent::class]);

        $transactionValue = 150;
        $transaction = ['value' => $transactionValue];

        $transactionDatabase = Transaction::factory()->create($transaction);

        $transaction['id'] = $transactionDatabase->id;
        $transaction['payer_wallet_id'] = $transactionDatabase->payer_wallet_id;
        $transaction['payee_wallet_id'] = $transactionDatabase->payee_wallet_id;

        $job = new ProcessTransactionJob($transaction);
        $job->handle();

        Event::assertDispatched(TransactionProcessedEvent::class, function ($event) {
            return !$event->transactionInfo['status'];
        });
    }

    public function test_an_transaction_email_is_sent()
    {
        $transactionValue = 50;
        $transaction = ['value' => $transactionValue];

        $transactionDatabase = Transaction::factory()->create($transaction);

        $transaction['id'] = $transactionDatabase->id;
        $transaction['payer_wallet_id'] = $transactionDatabase->payer_wallet_id;
        $transaction['payee_wallet_id'] = $transactionDatabase->payee_wallet_id;

        $job = new ProcessTransactionJob($transaction);
        $job->handle();

        Mail::assertSent(TransactionNotificationMail::class);
    }
}
