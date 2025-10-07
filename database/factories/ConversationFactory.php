<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->optional(0.3)->words(2, true),
            'is_group' => fake()->boolean(30),
            'created_by' => User::factory(),
            'is_private' => fake()->boolean(20),
            'messaging_enabled' => fake()->boolean(90),
            'description' => fake()->optional(0.4)->sentence(),
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
            'is_private' => false,
        ]);
    }
}
