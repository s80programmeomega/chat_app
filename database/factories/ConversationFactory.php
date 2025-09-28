<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->optional(0.3)->words(2, true), // 30% chance of having a name
            'is_group' => fake()->boolean(30), // 30% chance of being a group chat
        ];
    }

    public function group(): static
    {
        return $this->state([
            'name' => fake()->words(2, true),
            'is_group' => true,
        ]);
    }

    public function direct(): static
    {
        return $this->state([
            'name' => null,
            'is_group' => false,
        ]);
    }
}
