<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => $title,
            'menu_title' => null,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'show_in_menu' => false,
        ];
    }

    public function inMenu(): static
    {
        return $this->state(fn (array $attributes) => [
            'show_in_menu' => true,
        ]);
    }
}
