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
    ];

    protected function casts(): array
    {
        return [
            'is_group' => 'boolean',
        ];
    }

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // Helper methods
    public function getDisplayName($currentUser = null)
    {
        if ($this->is_group) {
            return $this->name ?? 'Group Chat';
        }

        // For direct messages, show the other user's name
        $otherUser = $this->users()->where('user_id', '!=', $currentUser?->id)->first();
        return $otherUser?->name ?? 'Unknown User';
    }
}
