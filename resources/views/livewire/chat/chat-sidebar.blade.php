<div class="h-100 border-end">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">Conversations</h5>
    </div>

    <div class="list-group list-group-flush">
        @forelse($conversations as $conversation)
        <button wire:click="selectConversation({{ $conversation->id }})"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-start btn btn-outline-secondary {{ $selectedConversationId == $conversation->id ? 'active' : '' }}">
            <div class="ms-2 me-auto">
                <div class="fw-bold d-flex justify-content-between align-items-center">
                    {{ $conversation->getDisplayName(auth()->user()) }}
                    @if($conversation->unread_count > 0)
                    <span class="badge bg-primary rounded-pill mx-3 p-2">{{ $conversation->unread_count }}</span>
                    @endif
                </div>
                @if($conversation->latestMessage)
                <small class="text-muted">
                    {{ Str::limit($conversation->latestMessage->content, 50) }}
                </small>
                @endif
            </div>
            @if($conversation->latestMessage)
            <small class="text-muted">
                {{ $conversation->latestMessage->created_at->diffForHumans() }}
            </small>
            @endif
        </button>
        @empty
        <div class="p-3">
            <h6>Available Users</h6>
            @foreach($availableUsers as $user)
            <div class="d-flex align-items-center mb-2">
                <div class="bg-secondary rounded-circle me-2" style="width: 32px; height: 32px;"></div>
                <span>{{ $user->name }}</span>
            </div>
            @endforeach
        </div>
        @endforelse
    </div>
</div>
