<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_group',
        'created_by',
        'is_private',
        'messaging_enabled',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_group' => 'boolean',
            'is_private' => 'boolean',
            'messaging_enabled' => 'boolean',
        ];
    }

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['is_admin', 'last_read_at'])->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function joinRequests()
    {
        return $this->hasMany(ConversationJoinRequest::class);
    }

    public function pendingJoinRequests()
    {
        return $this->joinRequests()->where('status', 'pending');
    }

    // Helper methods
    public function getDisplayName($currentUser = null)
    {
        if ($this->is_group) {
            return $this->name ?? 'Group Chat';
        }

        $otherUser = $this->users()->where('user_id', '!=', $currentUser?->id)->first();
        return $otherUser?->name ?? 'Unknown User';
    }

    public function isUserAdmin($user)
    {
        return $this->users()->where('user_id', $user->id)->first()?->pivot?->is_admin ?? false;
    }

    public function canUserMessage($user)
    {
        if (!$this->messaging_enabled) {
            return $this->isUserAdmin($user);
        }
        return true;
    }

    public function getUnreadCountForUser($user)
    {
        $lastReadAt = $this->users()->where('user_id', $user->id)->first()?->pivot?->last_read_at;

        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->when($lastReadAt, fn($query) => $query->where('created_at', '>', $lastReadAt))
            ->count();
    }
}
