<?php

namespace Database\Factories;

use App\Models\Advert;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'advert_id' => Advert::factory(),
            'file' => 'adverts/test/' . fake()->uuid . '.jpg',
        ];
    }
}
