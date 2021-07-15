<?php

use App\Models\User;
use App\Models\Wallet;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TransactionControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_registed_users_can_perform_transactions()
    {
        /** @var User $shopkeeper */
        $shopkeeper = User::factory()
            ->create(['shopkeeper' => true])
            ->refresh();
        $shopkeeperWallet = Wallet::factory()->make();
        $shopkeeper->wallets()->save($shopkeeperWallet);

        /** @var User $common */
        $common = User::factory()
            ->create(['shopkeeper' => false])
            ->refresh();
        $commonWallet = Wallet::factory()->make();
        $common->wallets()->save($commonWallet);

        $value = 123.45;

        $this->withoutMiddleware()->post("v1/transactions", [
            'payer' => $common->id,
            'payee' => $shopkeeper->id,
            'value' => $value
        ]);

        $this->seeStatusCode(200);
        $this->seeJsonStructure(['status', 'id']);
        $this->seeInDatabase('transactions', [
            'payer_wallet_id' => $commonWallet->id,
            'payee_wallet_id' => $shopkeeperWallet->id,
            'value' => $value
        ]);
    }
}
