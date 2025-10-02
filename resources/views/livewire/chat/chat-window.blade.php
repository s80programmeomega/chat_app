<div class="h-100 d-flex flex-column">
    @if($conversation)
    <!-- Chat Header -->
    <div class="p-3 border-bottom bg-light">
        <h6 class="mb-0">{{ $conversation->getDisplayName(auth()->user()) }}</h6>
        <small class="text-muted">
            {{ $conversation->users->count() }} {{ Str::plural('participant', $conversation->users->count()) }}
        </small>
    </div>

    <!-- Messages Area -->
    <div class="flex-grow-1 p-3 overflow-auto" style="max-height: 400px;" id="messages-container">
        @forelse($messages as $message)
        <div
            class="mb-3 d-flex {{ $message->user_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
            <div class="card {{ $message->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}"
                style="max-width: 70%;">
                <div class="card-body py-2 px-3">
                    @if($message->user_id !== auth()->id())
                    <small class="fw-bold">{{ $message->user->name }}</small>
                    @endif
                    <div>{{ $message->content }}</div>
                    <small class="opacity-75">
                        {{ $message->created_at->format('H:i') }}
                    </small>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted">
            No messages yet. Start the conversation!
        </div>
        @endforelse
    </div>

    <!-- Message Input Area -->
    <div class="border-top">
        <livewire:chat.message-form :conversationId="$conversationId" />
    </div>
    @else
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="text-center text-muted">
            <i class="bi bi-chat-dots fs-1"></i>
            <p>Select a conversation to start chatting</p>
        </div>
    </div>
    @endif
</div>
<script>
    window.Laravel = {
        user: {
            id: {{ auth()->id() }}
        }
    };
</script>
