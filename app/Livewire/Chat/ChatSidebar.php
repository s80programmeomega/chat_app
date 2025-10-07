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
        $this->dispatch('conversationSelected', $conversationId);
    }

    #[On('conversationUpdated')]
    #[On('conversationCreated')]
    #[On('messageSent')]
    #[On('newMessageReceived')]
    public function refreshConversations()
    {
        // Force re-render to update unread counts
    }

    #[On('conversationSelected')]
    public function setSelectedConversation(...$params)
    {
        if (isset($params[0])) {
            $this->selectedConversationId = $params[0];
        }
    }

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

        return view('livewire.chat.chat-sidebar', [
            'conversations' => $conversations,
        ]);
    }
}


// namespace App\Livewire\Chat;

// use App\Models\Conversation;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Livewire\Attributes\On;
// use Livewire\Component;

// class ChatSidebar extends Component
// {
//     public $selectedConversationId = null;

//     public function selectConversation($conversationId)
//     {
//         $this->selectedConversationId = $conversationId;

//         $this->dispatch('conversationSelected', $conversationId);
//     }

//     #[On('unreadCountUpdated')]
//     public function updateUnreadCount($conversationId)
//     {
//         $this->render();
//     }

//     #[On('conversationUpdated')]
//     #[On('conversationCreated')]
//     #[On('messageSent')]
//     public function refreshConversations()
//     {
//         $this->selectedConversationId = $this->selectedConversationId;
//     }

//     #[On('conversationSelected')]
//     public function setSelectedConversation(...$params)
//     {
//         if (isset($params[0])) {
//             $this->selectedConversationId = $params[0];
//         }
//     }

//     #[On('newMessageReceived')]
//     public function markAsRead(...$params)
//     {
//         if (isset($params[0]) && $this->selectedConversationId == $params[0]) {
//             Auth::user()->conversations()->updateExistingPivot($params[0], [
//                 'last_read_at' => now()
//             ]);
//         }
//     }

//     public function render()
//     {
//         $conversations = Auth::user()
//             ->conversations()
//             ->with(['users', 'latestMessage.user'])
//             ->get()
//             ->map(function ($conversation) {
//                 $conversation->unread_count = $conversation->getUnreadCountForUser(Auth::user());
//                 return $conversation;
//             });

//         return view('livewire.chat.chat-sidebar', [
//             'conversations' => $conversations,
//         ]);
//     }
// }
