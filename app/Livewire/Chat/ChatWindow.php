<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ChatWindow extends Component
{
    public $conversationId = null;
    public $messages = [];

    public function mount()
    {
        $this->initializeComponent();
    }

    private function initializeComponent($conversationId = null)
    {
        $this->conversationId = $conversationId;
        if (!$this->conversationId) {
            $this->messages = collect([
                (object) [
                    'id' => 1,
                    'content' => 'Welcome to the chat!',
                    'user_id' => Auth::id(),
                    'user' => (object) ['name' => 'System'],
                    'created_at' => now(),
                ],
            ]);
        } else {
            $this->loadMessages();
        }
    }

    #[On('messageVisible')]
    public function markAsRead($conversationId, $messageId)
    {
        Log::info('markAsRead called', ['conversationId' => $conversationId, 'messageId' => $messageId]);

        $conversation = Auth::user()->conversations()->find($conversationId);
        if (!$conversation) {
            Log::warning('Conversation not found', ['conversationId' => $conversationId]);
            return;
        }

        $message = Message::find($messageId);
        if (!$message || $message->conversation_id != $conversationId) {
            Log::warning('Message not found or mismatch', ['messageId' => $messageId, 'conversationId' => $conversationId]);
            return;
        }

        if ($message->user_id === Auth::id()) {
            Log::info('Skipping own message');
            return;
        }

        $currentLastReadAt = $conversation->pivot->last_read_at;

        if (!$currentLastReadAt || $message->created_at > $currentLastReadAt) {
            Auth::user()->conversations()->updateExistingPivot($conversationId, [
                'last_read_at' => $message->created_at
            ]);

            Log::info('Updated last_read_at', ['messageId' => $messageId, 'timestamp' => $message->created_at]);

            $this->dispatch('conversationUpdated');
        }
    }


    // #[On('messageVisible')]
    // public function markAsRead($conversationId, $messageId)
    // {
    //     $conversation = Auth::user()->conversations()->find($conversationId);
    //     if (!$conversation) return;

    //     $message = Message::find($messageId);
    //     if (!$message || $message->conversation_id != $conversationId) return;
    //     if ($message->user_id === Auth::id()) return;

    //     Auth::user()->conversations()->updateExistingPivot($conversationId, [
    //         'last_read_at' => $message->created_at
    //     ]);

    //     $this->dispatch('conversationUpdated');
    // }

    #[On('conversationSelected')]
    public function loadConversation(...$params)
    {
        if (isset($params[0])) {
            $this->joinConversation($params[0]);
        }
    }

    #[On('messageSent')]
    public function refreshMessages()
    {
        $this->loadMessages();
    }

    #[On('newMessageReceived')]
    public function handleNewMessage(...$params)
    {
        $this->loadMessages();
    }

    public function joinConversation($conversationId)
    {
        $this->conversationId = $conversationId;
        $this->loadMessages();
    }

    private function loadMessages()
    {
        if (!$this->conversationId) {
            $this->messages = [];
            return;
        }

        $this->messages = Message::where('conversation_id', $this->conversationId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function render()
    {
        $conversation = $this->conversationId
            ? Conversation::find($this->conversationId)
            : null;

        return view('livewire.chat.chat-window', [
            'conversation' => $conversation,
            'messages' => $this->messages,
            'conversationId' => $this->conversationId,
        ]);
    }
}


// namespace App\Livewire\Chat;

// use App\Models\Conversation;
// use App\Models\Message;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
// use Livewire\Attributes\On;
// use Livewire\Component;

// class ChatWindow extends Component
// {
//     public $conversationId = null;

//     public $messages = [];

//     public function mount()
//     {
//         $this->initializeComponent();
//     }

//     private function initializeComponent($conversationId = null)
//     {
//         $this->conversationId = $conversationId;
//         // Show sample messages if no conversation selected
//         if (!$this->conversationId) {
//             $this->messages = collect([
//                 (object) [
//                     'id' => 1,
//                     'content' => 'Welcome to the chat!',
//                     'user_id' => Auth::id(),
//                     'user' => (object) ['name' => 'System'],
//                     'created_at' => now(),
//                 ],
//             ]);
//         } else {
//             $this->loadMessages();
//             // Mark as read when opening chat
//             // Auth::user()->conversations()->updateExistingPivot($conversationId, [
//             //     'last_read_at' => now(),
//             // ]);
//         }
//     }

//     #[On('messageVisible')]
//     public function markAsRead($conversationId, $messageId)
//     {
//         $conversation = Auth::user()->conversations()->find($conversationId);
//         if (!$conversation)
//             return;

//         $message = Message::find($messageId);
//         if (!$message || $message->conversation_id != $conversationId)
//             return;

//         if ($message->user_id === Auth::id())
//             return;

//         $currentLastReadAt = $conversation->pivot->last_read_at;

//         if (!$currentLastReadAt || $message->created_at > $currentLastReadAt) {
//             Auth::user()->conversations()->updateExistingPivot($conversationId, [
//                 'last_read_at' => $message->created_at
//             ]);

//             $this->dispatch('unreadCountUpdated', $conversationId);
//         }
//     }

    // #[On('messageVisible')]
    // public function markAsRead($conversationId, $messageId)
    // {
    //     // Validate that the conversation belongs to the user
    //     $conversation = Auth::user()->conversations()->find($conversationId);
    //     if (!$conversation) return;

    //     $message = Message::find($messageId);
    //     if (!$message || $message->conversation_id != $conversationId) return;

    //     // Don't mark own messages as read
    //     if ($message->user_id === Auth::id()) return;

    //     // Update last_read_at to this message's timestamp
    //     Auth::user()->conversations()->updateExistingPivot($conversationId, [
    //         'last_read_at' => $message->created_at
    //     ]);

    //     // Notify other components to update unread counts
    //     $this->dispatch('conversationUpdated');
    // }

//     #[On('conversationSelected')]
//     public function loadConversation(...$params)
//     {
//         if (isset($params[0])) {
//             $this->joinConversation($params[0]);
//         }
//     }

//     #[On('messageSent')]
//     public function refreshMessages()
//     {
//         $this->loadMessages();
//     }

//     #[On('newMessageReceived')]
//     public function handleNewMessage(...$params)
//     {
//         $this->loadMessages();
//     }

//     public function joinConversation($conversationId)
//     {
//         $this->conversationId = $conversationId;
//         $this->loadMessages();
//         // $this->dispatch('joinConversation', $conversationId);
//     }

//     private function loadMessages()
//     {
//         // dd("Loading messages for conversation ID: " . $this->conversationId);
//         if (!$this->conversationId) {
//             $this->messages = [];

//             return;
//         }

//         $this->messages = Message::where('conversation_id', $this->conversationId)
//             ->with('user')
//             ->orderBy('created_at', 'asc')
//             ->get();
//     }

//     public function render()
//     {
//         $conversation = $this->conversationId
//             ? Conversation::find($this->conversationId)
//             : null;

//         return view('livewire.chat.chat-window', [
//             'conversation' => $conversation,
//             'messages' => $this->messages,
//             'conversationId' => $this->conversationId,
//         ]);
//     }
// }
