<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $isShopkeeper = (int) $this->faker->boolean;
        return [
            'id' => $this->faker->uuid,
            'full_name' => $isShopkeeper ? $this->faker->company : $this->faker->name,
            'email' =>  $isShopkeeper
                ? $this->faker->unique()->companyEmail
                : $this->faker->unique()->safeEmail,
            'password' => '$2y$10$IiPnvo1IcavNTDUCeeTK7OEj8lZm65eedj2/A0dgvwm67LBK3onAa',
            'registration_number' =>  $isShopkeeper
                ? $this->faker->numerify('##############')
                : $this->faker->numerify('###########'),
            'shopkeeper' =>  $isShopkeeper
        ];
    }
}
