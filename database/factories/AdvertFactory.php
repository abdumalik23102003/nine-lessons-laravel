<?php

namespace Database\Factories;

use App\Models\Advert;
use App\Models\Category;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Advert>
 */
class AdvertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'region_id' => Region::factory(),
            'title' => $this->faker->sentence(3),
            'price' => fake()->numberBetween(10000, 5000000),
            'address' => $this->faker->address(),
            'content' => fake()->paragraph(),
            'status' => Advert::STATUS_DRAFT,
        ];
    }

    public function onModeration(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Advert::STATUS_MODERATION,
        ]);

    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Advert::STATUS_ACTIVE,
            'published_at' => now(),
            'expires_at' => now()->addDay(),
        ]);
    }


}
