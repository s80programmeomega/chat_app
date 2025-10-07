<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicGroups extends Component
{
    public $showModal = false;

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        $publicGroups = Conversation::where('is_group', true)
            ->where('is_private', false)
            ->whereDoesntHave('users', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->with('creator')
            ->get();

        return view('livewire.chat.public-groups', [
            'publicGroups' => $publicGroups
        ]);
    }
}
