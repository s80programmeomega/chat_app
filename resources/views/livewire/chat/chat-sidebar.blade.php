<div class="h-100 border-end">
    <div class="p-3 border-bottom">
        <h5 class="mb-3">Conversations</h5>
        <livewire:chat.new-conversation />
        <livewire:chat.public-groups />
    </div>

    <div class="list-group list-group-flush" >
        @forelse($conversations as $conversation)
        <div class="list-group-item p-0" wire:poll.5s>
            <button wire:click="selectConversation({{ $conversation->id }})"
                class="btn btn-outline-info w-100 text-start d-flex justify-content-between align-items-start {{ $selectedConversationId == $conversation->id ? 'active' : '' }}">
                <div class="ms-2 me-auto">
                    <div class="fw-bold d-flex align-items-center">
                        @if($conversation->is_group)
                            <i class="bi bi-people me-1"></i>
                            @if($conversation->is_private)
                                <i class="bi bi-lock me-1"></i>
                            @endif
                        @endif
                        {{ $conversation->getDisplayName(auth()->user()) }}
                        @if($conversation->unread_count > 0)
                            <span class="badge bg-primary rounded-pill ms-2">{{ $conversation->unread_count }}</span>
                        @endif
                    </div>
                    @if($conversation->latestMessage)
                    <small class="text-muted">
                        {{ Str::limit($conversation->latestMessage->content, 50) }}
                    </small>
                    @endif
                </div>
                <div class="d-flex flex-column align-items-end">
                    @if($conversation->latestMessage)
                    <small class="text-muted">
                        {{ $conversation->latestMessage->created_at->diffForHumans() }}
                    </small>
                    @endif
                </div>
            </button>
            @if($conversation->is_group && $conversation->isUserAdmin(auth()->user()))
                <div class="p-2">
                    <livewire:chat.group-management :conversation="$conversation" :key="'group-mgmt-'.$conversation->id" />
                </div>
            @endif
        </div>
        @empty
        <div class="p-3 text-center text-muted">
            <p>No conversations yet</p>
            <p>Start a new chat above!</p>
        </div>
        @endforelse
    </div>
</div>
