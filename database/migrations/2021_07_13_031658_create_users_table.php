<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    const USERS = [
        [
            'id' => '5d541b1b-e038-41dd-96d1-8b757b4d23f0',
            'full_name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'password' => '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa',
            'registration_number' => '76997937063',
            'shopkeeper' => 0,
        ],
        [
            'id' => '5f97a1d3-a2e2-4a58-aa48-66cd49ab14a8',
            'full_name' => 'Bob Dylan',
            'email' => 'bob.dylan@test.com',
            'password' => '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa',
            'registration_number' => '08087648021',
            'shopkeeper' => 0
        ],
        [
            'id' => '1084fe32-5a84-4ce4-8ec0-3cbbe4485863',
            'full_name' => 'John Dealer',
            'email' => 'john.dealer@test.com',
            'password' => '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa',
            'registration_number' => '39382854000115',
            'shopkeeper' => 1
        ],
        [
            'id' => '92ffc998-5e2a-488f-ba43-76f0467c7f6f',
            'full_name' => 'Bob Dealer',
            'email' => 'bob.dealer@test.com',
            'password' => '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa',
            'registration_number' => '64041937000198',
            'shopkeeper' => 1
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('registration_number', 14)->unique();
            $table->binary('shopkeeper');
            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment('local')) {
            // To populate db only once
            DB::table('users')->insert(self::USERS);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
