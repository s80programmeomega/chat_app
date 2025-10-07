<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\ConversationJoinRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationJoinRequestSeeder extends Seeder
{
    public function run(): void
    {
        $groupConversations = Conversation::where('is_group', true)->get();
        $users = User::all();

        foreach ($groupConversations as $conversation) {
            $nonMembers = $users->whereNotIn('id', $conversation->users->pluck('id'));

            if ($nonMembers->count() > 0) {
                $requesters = $nonMembers->random(min(2, $nonMembers->count()));

                foreach ($requesters as $user) {
                    ConversationJoinRequest::factory()->create([
                        'conversation_id' => $conversation->id,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
    }
}
