<?php

namespace App\Livewire\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageForm extends Component
{
    public $conversationId;
    public $content = '';
    public $conversation;

    public function mount($conversationId = null)
    {
        $this->conversationId = $conversationId;
        $this->loadConversation();
    }

    #[On('conversationSelected')]
    public function setConversation($conversationId)
    {
        $this->conversationId = $conversationId;
        $this->loadConversation();
    }

    private function loadConversation()
    {
        if ($this->conversationId) {
            $this->conversation = Conversation::find($this->conversationId);
        }
    }

    public function sendMessage()
    {
        if (!$this->conversationId || !$this->conversation) return;

        // Check if user can message
        if (!$this->conversation->canUserMessage(Auth::user())) {
            session()->flash('error', 'Messaging is disabled for this group.');
            return;
        }

        $this->validate(['content' => 'required|string|max:1000']);

        $message = Message::create([
            'conversation_id' => $this->conversationId,
            'user_id' => Auth::id(),
            'content' => $this->content,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        $this->reset('content');
        $this->dispatch('messageSent', [
            'conversationId' => $this->conversationId,
            'userId' => Auth::id()
        ]);
    
    }

    public function render()
    {
        $canMessage = $this->conversation ? $this->conversation->canUserMessage(Auth::user()) : true;

        return view('livewire.chat.message-form', [
            'canMessage' => $canMessage
        ]);
    }
}
