<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $commomUser = User::factory()->create(['shopkeeper' => false]);
        $commomUserWallet = Wallet::factory()->create([
            'user_id' => $commomUser->id,
            'balance' => 100
        ]);

        $shopkeeperUser = User::factory()->create(['shopkeeper' => true]);
        $shopkeeperUserWallet = Wallet::factory()->create(['user_id' => $shopkeeperUser->id]);

        return [
            'id' => $this->faker->uuid,
            'payer_wallet_id' => $commomUserWallet->id,
            'payee_wallet_id' => $shopkeeperUserWallet->id,
            'value' => $this->faker->randomFloat(2, 1, 10000),
            'processed' => false
        ];
    }
}
