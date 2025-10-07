<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NewConversation extends Component
{
    public $showModal = false;
    public $conversationType = 'direct'; // 'direct' or 'group'
    public $selectedUserId = null;
    public $groupName = '';
    public $groupDescription = '';
    public $isPrivate = false;
    public $selectedUsers = [];

    // protected $rules = [
    //     'selectedUserId' => 'required_if:conversationType,direct|exists:users,id',
    //     'groupName' => 'required_if:conversationType,group|string|max:255',
    //     'groupDescription' => 'nullable|string|max:1000',
    //     'selectedUsers' => 'required_if:conversationType,group|array|min:1',
    //     'selectedUsers.*' => 'exists:users,id',
    // ];

    public function openModal()
    {
        $this->showModal = true;
        $this->reset(['selectedUserId', 'groupName', 'groupDescription', 'selectedUsers']);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function toggleUserSelection($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    public function createConversation()
    {
        try {
            if ($this->conversationType === 'direct') {
                $this->validate([
                    'selectedUserId' => 'required|exists:users,id',
                ]);
            } else {
                $this->validate([
                    'groupName' => 'required|string|max:255',
                    'groupDescription' => 'nullable|string|max:1000',
                    'selectedUsers' => 'required|array|min:1',
                    'selectedUsers.*' => 'exists:users,id',
                ]);
            }


            if ($this->conversationType === 'direct') {
                $this->createDirectConversation();
            } else {
                $this->createGroupConversation();
            }

            $this->closeModal();
            $this->dispatch('conversationCreated');
        } catch (\Exception $e) {
            // Log the error or show it
            session()->flash('error', 'Error creating conversation: ' . $e->getMessage());
            dd("Validation failed", $e->getMessage(), $this->selectedUserId);

        }
    }


    private function createDirectConversation()
    {
        // Check if conversation already exists between these two users
        $existingConversation = Conversation::where('is_group', false)
            ->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereHas('users', function ($query) {
                $query->where('user_id', $this->selectedUserId);
            })
            ->whereDoesntHave('users', function ($query) {
                $query->whereNotIn('user_id', [Auth::id(), $this->selectedUserId]);
            })
            ->first();

        if ($existingConversation) {
            $this->dispatch('conversationSelected', $existingConversation->id);
            return;
        }

        $conversation = Conversation::create([
            'is_group' => false,
        ]);

        $conversation->users()->attach([
            Auth::id() => ['joined_at' => now()],
            $this->selectedUserId => ['joined_at' => now()],
        ]);

        $this->dispatch('conversationSelected', $conversation->id);
    }


    // private function createDirectConversation()
    // {
    //     // Check if conversation already exists
    //     $existingConversation = Conversation::whereHas('users', function ($query) {
    //         $query->where('user_id', Auth::id());
    //     })->whereHas('users', function ($query) {
    //         $query->where('user_id', $this->selectedUserId);
    //     })->where('is_group', false)->first();

    //     // dd($existingConversation->toArray());
    //     if ($existingConversation) {
    //         $this->dispatch('conversationSelected', $existingConversation->id);
    //         return;
    //     }

    //     $conversation = Conversation::create([
    //         'is_group' => false,
    //     ]);

    //     $conversation->users()->attach([
    //         Auth::id() => ['joined_at' => now()],
    //         $this->selectedUserId => ['joined_at' => now()],
    //     ]);

    //     $this->dispatch('conversationSelected', $conversation->id);
    // }

    private function createGroupConversation()
    {
        $conversation = Conversation::create([
            'name' => $this->groupName,
            'description' => $this->groupDescription,
            'is_group' => true,
            'is_private' => $this->isPrivate,
            'created_by' => Auth::id(),
        ]);

        // Add creator as admin
        $users = [Auth::id() => ['joined_at' => now(), 'is_admin' => true]];

        // Add selected users as regular members
        foreach ($this->selectedUsers as $userId) {
            $users[$userId] = ['joined_at' => now(), 'is_admin' => false];
        }

        $conversation->users()->attach($users);

        $this->dispatch('conversationSelected', $conversation->id);
    }

    public function render()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        return view('livewire.chat.new-conversation', [
            'users' => $users,
        ]);
    }
}
