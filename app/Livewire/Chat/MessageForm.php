<?php

namespace App\Livewire\Chat;

use App\Events\MessageSent;
use App\Events\ConversationUpdated;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

use function Symfony\Component\String\b;

class MessageForm extends Component
{
    public $conversationId;
    public $content = '';

    #[On('conversationSelected')]
    public function setConversation($conversationId)
    {
        $this->conversationId = $conversationId;
    }

    public function sendMessage()
    {
        $this->validate([
            'content' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'conversation_id' => $this->conversationId,
            'user_id' => Auth::user()->id,
            'content' => $this->content
        ]);

        Auth::user()->conversations()->updateExistingPivot($this->conversationId, [
            'last_read_at' => now()
        ]);

        // Load the user relationship for broadcasting
        $message->load('user');

        // Broadcast the event
        broadcast(new MessageSent($message));
        broadcast(new ConversationUpdated($message));

        $this->dispatch('sendMessage', $message->id);

        $this->content = '';
        $this->dispatch('messageAdded');
        $this->dispatch('conversationUpdated', $message);
    }

    public function render()
    {
        return view('livewire.chat.message-form');
    }
}
