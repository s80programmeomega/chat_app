<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\ConversationJoinRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JoinGroupRequest extends Component
{
    public $conversation;
    public $message = '';

    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public function requestJoin()
    {
        if ($this->conversation->is_private || $this->hasExistingRequest()) {
            return;
        }

        ConversationJoinRequest::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
        ]);

        session()->flash('message', 'Join request sent!');
    }

    private function hasExistingRequest()
    {
        return ConversationJoinRequest::where('conversation_id', $this->conversation->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->exists();
    }

    public function render()
    {
        return view('livewire.chat.join-group-request');
    }
}
