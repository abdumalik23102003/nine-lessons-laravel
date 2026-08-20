<?php

namespace Database\Factories;

use App\Models\Banner;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'url' => fake()->url(),
            'format' => '728x90',
            'file' => 'banners/test/' . fake()->uuid() . '.jpg',
            'status' => Banner::STATUS_DRAFT,
        ];
    }

    public function onModeration(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Banner::STATUS_MODERATION,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Banner::STATUS_ACTIVE,
            'published_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);
    }
}
