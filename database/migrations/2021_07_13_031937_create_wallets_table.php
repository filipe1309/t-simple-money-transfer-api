<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    const WALLETS = [
        [
            'id' => '91e92c5f-d9d0-437a-9435-58839fdbb6c5',
            'user_id' => '5d541b1b-e038-41dd-96d1-8b757b4d23f0',
            'balance' => 1000
        ],
        [
            'id' => 'b786b829-2a9a-4454-af52-b06a552d845c',
            'user_id' => '5f97a1d3-a2e2-4a58-aa48-66cd49ab14a8',
            'balance' => 100
        ],
        [
            'id' => '9442fd46-44cf-4571-9bfd-59670b765719',
            'user_id' => '1084fe32-5a84-4ce4-8ec0-3cbbe4485863',
            'balance' => 10000
        ],
        [
            'id' => '90e75b1a-ae80-4c28-b1c8-7f06d35e7f60',
            'user_id' => '92ffc998-5e2a-488f-ba43-76f0467c7f6f',
            'balance' => 50000
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->foreignUuid('user_id')->references('id')->on('users');
            $table->float('balance')->define(0);
            $table->timestamps();
        });

        if (app()->environment('local')) {
            // To populate db only once
            DB::table('wallets')->insert(self::WALLETS);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
