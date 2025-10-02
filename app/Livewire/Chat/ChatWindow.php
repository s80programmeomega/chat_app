<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

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
        // Show sample messages if no conversation selected
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
            // Mark as read when opening chat
            Auth::user()->conversations()->updateExistingPivot($conversationId, [
                'last_read_at' => now(),
            ]);
        }
    }

    #[On('conversationSelected')]
public function loadConversation(...$params)
{
    if (isset($params[0])) {
        $this->joinConversation($params[0]);
    }
}


    #[On('sendMessage')]
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
        $this->dispatch('joinConversation', $conversationId);
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
