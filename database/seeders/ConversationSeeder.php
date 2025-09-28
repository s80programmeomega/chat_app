<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        // Create 3 direct conversations
        for ($i = 0; $i < 3; $i++) {
            $conversation = Conversation::factory()->direct()->create();
            $conversation->users()->attach($users->random(2));
        }

        // Create 2 group conversations
        for ($i = 0; $i < 2; $i++) {
            $conversation = Conversation::factory()->group()->create();
            $conversation->users()->attach($users->random(fake()->numberBetween(3, 5)));
        }
    }
}
