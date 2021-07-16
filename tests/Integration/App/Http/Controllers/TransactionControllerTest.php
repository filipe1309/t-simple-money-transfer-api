<?php

use App\Events\NewTransactionCreatedEvent;
use App\Jobs\ProcessTransactionJob;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TransactionControllerTest extends TestCase
{
    //use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        // $this->artisan('db:seed');
    }

    public function test_transaction_value_must_greater_than_zero(): void
    {
        $walletId1 = Wallet::factory()->create()->id;
        $walletId2 = Wallet::factory()->create()->id;

        $value = 0;

        $this->withoutMiddleware()->post("v1/transactions", [
            'payer' => $walletId1,
            'payee' => $walletId2,
            'value' => $value
        ]);

        $this->seeStatusCode(400);
        $this->seeJsonStructure(['message']);
        $this->notSeeInDatabase('transactions', [
            'payer_wallet_id' => $walletId1,
            'payee_wallet_id' => $walletId2,
            'value' => $value
        ]);
    }


    public function test_users_in_a_transaction_must_be_different(): void
    {
        $commomUser = User::factory()->create(['shopkeeper' => false]);
        $commomUserWallet = Wallet::factory()->create([
            'user_id' => $commomUser->id,
            'balance' => 100,
        ]);

        $value = 50;

        $this->withoutMiddleware()->post("v1/transactions", [
            'payer' => $commomUserWallet->id,
            'payee' => $commomUserWallet->id,
            'value' => $value
        ]);

        $this->seeStatusCode(400);
        $this->seeJsonStructure(['message']);
        $this->notSeeInDatabase('transactions', [
            'payer_wallet_id' => $commomUserWallet->id,
            'payee_wallet_id' => $commomUserWallet->id,
            'value' => $value
        ]);
    }

    public function test_registed_users_can_perform_transactions(): void
    {
        /** @var User $shopkeeper */
        $shopkeeper = User::factory()
            ->create(['shopkeeper' => true]);
        //->refresh();
        $shopkeeperWallet = Wallet::factory()->make();
        $shopkeeper->wallets()->save($shopkeeperWallet);

        /** @var User $common */
        $common = User::factory()
            ->create(['shopkeeper' => false]);
        //->refresh();
        $commonWallet = Wallet::factory()->make();
        $common->wallets()->save($commonWallet);

        $value = 123.45;

        $this->withoutMiddleware()->post("v1/transactions", [
            'payer' => $commonWallet->id,
            'payee' => $shopkeeperWallet->id,
            'value' => $value
        ]);

        $this->seeStatusCode(201);
        $this->seeJsonStructure(['status', 'id']);
        $this->seeInDatabase('transactions', [
            'payer_wallet_id' => $commonWallet->id,
            'payee_wallet_id' => $shopkeeperWallet->id,
            'value' => $value
        ]);
    }

    public function test_an_event_is_dispatched_when_transaction_is_created(): void
    {
        Event::fake([NewTransactionCreatedEvent::class]);

        $this->withoutMiddleware()->post("v1/transactions", [
            'payer' => '91e92c5f-d9d0-437a-9435-58839fdbb6c5',
            'payee' => '9442fd46-44cf-4571-9bfd-59670b765719',
            'value' => '123'
        ]);

        Event::assertDispatched(NewTransactionCreatedEvent::class);
    }

    public function test_a_job_is_dispatched_when_transaction_is_created(): void
    {
        Event::fake([ProcessTransactionJob::class]);
        Queue::fake();

        //docker-compose exec php ./vendor/bin/phpunit
        $this->withoutMiddleware()->post("v1/transactions", [
            'payer' => '91e92c5f-d9d0-437a-9435-58839fdbb6c5',
            'payee' => '9442fd46-44cf-4571-9bfd-59670b765719',
            'value' => '1'
        ]);

        Queue::assertPushed(ProcessTransactionJob::class, function ($job) {
            return !empty($job->transaction);
        });
    }
}
