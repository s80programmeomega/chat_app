<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\ConversationJoinRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupManagement extends Component
{
    public $conversation;
    public $showModal = false;
    public $activeTab = 'members'; // 'members', 'settings', 'requests'

    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public function openModal()
    {
        $this->authorize('manage', $this->conversation);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function toggleAdmin($userId)
    {
        $this->authorize('manage', $this->conversation);

        $user = $this->conversation->users()->where('user_id', $userId)->first();
        if ($user && $userId !== $this->conversation->created_by) {
            $this->conversation->users()->updateExistingPivot($userId, [
                'is_admin' => !$user->pivot->is_admin
            ]);
        }
    }

    public function removeUser($userId)
    {
        $this->authorize('manage', $this->conversation);

        if ($userId !== $this->conversation->created_by) {
            $this->conversation->users()->detach($userId);
        }
    }

    public function togglePrivacy()
    {
        $this->authorize('manage', $this->conversation);

        $this->conversation->update([
            'is_private' => !$this->conversation->is_private
        ]);
    }

    public function toggleMessaging()
    {
        $this->authorize('manage', $this->conversation);

        $this->conversation->update([
            'messaging_enabled' => !$this->conversation->messaging_enabled
        ]);
    }

    public function approveJoinRequest($requestId)
    {
        $this->authorize('manage', $this->conversation);

        $request = ConversationJoinRequest::findOrFail($requestId);

        $request->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->conversation->users()->attach($request->user_id, [
            'joined_at' => now(),
            'is_admin' => false,
        ]);
    }

    public function denyJoinRequest($requestId)
    {
        $this->authorize('manage', $this->conversation);

        $request = ConversationJoinRequest::findOrFail($requestId);

        $request->update([
            'status' => 'denied',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }

    public function render()
    {
        $members = $this->conversation->users()->get();
        $pendingRequests = $this->conversation->pendingJoinRequests()->with('user')->get();

        return view('livewire.chat.group-management', [
            'members' => $members,
            'pendingRequests' => $pendingRequests,
        ]);
    }
}
