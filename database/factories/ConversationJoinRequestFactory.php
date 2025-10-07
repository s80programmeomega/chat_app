<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationJoinRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'approved', 'denied']),
            'message' => fake()->optional(0.7)->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'reviewed_by' => User::factory(),
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function denied(): static
    {
        return $this->state([
            'status' => 'denied',
            'reviewed_by' => User::factory(),
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
