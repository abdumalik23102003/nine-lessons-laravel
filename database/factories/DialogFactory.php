<?php

namespace Database\Factories;

use App\Models\Advert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DialogFactory extends Factory
{

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        return [
            'advert_id' => Advert::factory(),
            'user_id' => User::factory(),
            'client_id' => User::factory(),
            'user_new_messages' => 0,
            'client_new_messages' => 0,
        ];
    }
}
