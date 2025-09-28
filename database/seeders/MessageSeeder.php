<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $conversations = Conversation::with('users')->get();

        foreach ($conversations as $conversation) {
            $users = $conversation->users;
            $messageCount = fake()->numberBetween(5, 15);

            for ($i = 0; $i < $messageCount; $i++) {
                Message::factory()->create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
    }
}
