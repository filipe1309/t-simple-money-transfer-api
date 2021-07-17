<?php

use App\Events\NewTransactionCreatedEvent;
use App\Jobs\ProcessTransactionJob;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TransactionControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->artisan('db:seed');
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
        $shopkeeper = User::factory()->create(['shopkeeper' => true]);
        $shopkeeperWallet = Wallet::factory()->create(['user_id' => $shopkeeper->id]);

        /** @var User $common */
        $common = User::factory()->create(['shopkeeper' => false]);
        $commonWallet = Wallet::factory()->create(['user_id' => $common->id]);

        $value = 123.45;

        $this->post("v1/transactions", [
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

        $shopkeeper = User::factory()->create(['shopkeeper' => true]);
        $shopkeeperWallet = Wallet::factory()->create(['user_id' => $shopkeeper->id]);

        $common = User::factory()->create(['shopkeeper' => false]);
        $commonWallet = Wallet::factory()->create(['user_id' => $common->id]);

        $value = 123.45;

        $this->post("v1/transactions", [
            'payer' => $commonWallet->id,
            'payee' => $shopkeeperWallet->id,
            'value' => $value
        ]);

        Event::assertDispatched(NewTransactionCreatedEvent::class);
    }

    public function test_a_job_is_dispatched_when_transaction_is_created(): void
    {
        Bus::fake([ProcessTransactionJob::class]);

        $shopkeeper = User::factory()->create(['shopkeeper' => true]);
        $shopkeeperWallet = Wallet::factory()->create(['user_id' => $shopkeeper->id]);

        $common = User::factory()->create(['shopkeeper' => false]);
        $commonWallet = Wallet::factory()->create(['user_id' => $common->id]);

        $value = 123.45;

        $this->post("v1/transactions", [
            'payer' => $commonWallet->id,
            'payee' => $shopkeeperWallet->id,
            'value' => $value
        ]);

        Bus::assertDispatched(ProcessTransactionJob::class);
    }
}
