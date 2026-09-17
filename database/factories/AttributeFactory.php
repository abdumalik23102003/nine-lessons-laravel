<?php

namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'type' => Attribute::TYPE_STRING,
            'required' => false,
            'variants' => null,
        ];
    }

    public function asInteger(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Attribute::TYPE_INTEGER,
        ]);
    }

    public function asFloat(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Attribute::TYPE_FLOAT,
        ]);
    }

    public function withVariants(array $variants): static
    {
        return $this->state(fn (array $attributes) => [
            'variants' => $variants,
        ]);
    }
}
