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
    public function refreshConversations()
    {
        // Refresh the component when new messages arrive
    }

    #[On('messageReceived')]
    public function markAsRead($conversationId)
    {
        if ($this->selectedConversationId == $conversationId) {
            Auth::user()->conversations()->updateExistingPivot($conversationId, [
                'last_read_at' => now()
            ]);
        }
    }

    #[On('sendMessage')]
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
