<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@email.com',
        ]);

        // Create additional test users
        User::factory(9)->create();

        // Seed conversations and messages
        $this->call([
            ConversationSeeder::class,
            MessageSeeder::class,
            ConversationJoinRequestSeeder::class,
        ]);
    }
}
