<?php

namespace Database\Factories;

use App\Models\Network;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NetworkFactory extends Factory
{
    protected $model = Network::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->randomElement(['google', 'facebook', 'github']),
            'network_id' => $this->faker->uuid(),
            'data' => [
                'email' => $this->faker->email(),
                'name' => $this->faker->name(),
                'avatar' => $this->faker->imageUrl(),
            ],
        ];
    }
}
