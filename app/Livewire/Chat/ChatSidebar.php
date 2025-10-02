<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatSidebar extends Component
{
    public $selectedConversationId = null;

    public function selectConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;

        // Mark messages as read
        Auth::user()->conversations()->updateExistingPivot($conversationId, [
            'last_read_at' => now()
        ]);

        $this->dispatch('conversationSelected', $conversationId);
    }

    #[On('conversationUpdated')]
    public function refreshConversations($message)
    {
        $this->selectedConversationId = $this->selectedConversationId;
    }

    #[On('newMessageReceived')]
    public function markAsRead(...$params)
    {
        if (isset($params[0]) && $this->selectedConversationId == $params[0]) {
            Auth::user()->conversations()->updateExistingPivot($params[0], [
                'last_read_at' => now()
            ]);
        }
    }

    #[On('newMessage')]
    public function render()
    {
        $conversations = Auth::user()
            ->conversations()
            ->with(['users', 'latestMessage.user'])
            ->get()
            ->map(function ($conversation) {
                $conversation->unread_count = $conversation->getUnreadCountForUser(Auth::user());
                return $conversation;
            });

        // If no conversations, show available users to start chat
        $availableUsers = collect();
        if ($conversations->isEmpty()) {
            $availableUsers = User::where('id', '!=', Auth::id())->get();
        }

        return view('livewire.chat.chat-sidebar', [
            'conversations' => $conversations,
            'availableUsers' => $availableUsers
        ]);
    }
}
