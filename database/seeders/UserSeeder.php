<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(4)
            ->create()
            ->each(function ($user) {
                Wallet::factory()->create(['user_id' => $user->id]);
            });
    }
}
