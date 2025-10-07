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
        <div data-message-id="{{ $message->id }}"
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
    document.addEventListener('livewire:init', () => {
        @if($conversationId)
            const container = document.getElementById('messages-container');
            if (!container) return;
            const currentUserId = {{ auth()->id() }};

            let lastReadMessageId = null;
            let userScrolled = false;
            let conversationJustSelected = true;
            let scrollTimeout;

            const getVisibleMessages = () => {
                const messages = container.querySelectorAll('[data-message-id]');
                const containerRect = container.getBoundingClientRect();
                const visible = [];

                messages.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < containerRect.bottom && rect.bottom > containerRect.top) {
                        visible.push(parseInt(el.dataset.messageId));
                    }
                });

                return visible;
            };

            const markAsRead = () => {
                const visible = getVisibleMessages();
                if (visible.length === 0) return;

                const latestVisible = visible[visible.length - 1];
                if (latestVisible !== lastReadMessageId) {
                    lastReadMessageId = latestVisible;
                    console.log('Marking as read:', {{ $conversationId }}, latestVisible);
                    Livewire.dispatch('messageVisible', {
                        conversationId: {{ $conversationId }},
                        messageId: latestVisible
                    });
                }
            };

            container.addEventListener('scroll', () => {
                if (conversationJustSelected) {
                    conversationJustSelected = false;
                    return;
                }

                userScrolled = true;
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(markAsRead, 200);
            });

            const handleConversationSelection = () => {
                conversationJustSelected = true;
                userScrolled = false;

                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;

                    setTimeout(() => {
                        markAsRead();
                        conversationJustSelected = false;
                    }, 100);
                }, 50);
            };

            Livewire.hook('morph.updated', () => {
                if (conversationJustSelected) {
                    handleConversationSelection();
                }
            });

            handleConversationSelection();

            // Auto-scroll only for sender
            Livewire.on('messageSent', (data) => {
                if (data[0]?.userId === currentUserId) {
                    setTimeout(() => {
                        container.scrollTop = container.scrollHeight;
                        markAsRead();
                    }, 100);
                }
            });

            // Recipients: mark as read only on manual scroll
            Livewire.on('newMessageReceived', (data) => {
                if (data[0] !== currentUserId) {
                    if (userScrolled) {
                        markAsRead();
                    }
                }
            });
        @endif
    });
    </script>





{{-- <div class="h-100 d-flex flex-column">
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
        <div data-message-id="{{ $message->id }}"
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
    document.addEventListener('livewire:init', () => {
        @if($conversationId)
            const container = document.getElementById('messages-container');
            if (!container) return;

            let lastReadMessageId = null;
            let userScrolled = false;
            let scrollTimeout;
            let isNewlySelected = true;

            const getVisibleMessages = () => {
                const messages = container.querySelectorAll('[data-message-id]');
                const containerRect = container.getBoundingClientRect();
                const visible = [];

                messages.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    const isVisible = rect.top >= containerRect.top && rect.bottom <= containerRect.bottom;
                    if (isVisible) {
                        visible.push(el.dataset.messageId);
                    }
                });

                return visible;
            };

            const markVisibleAsRead = () => {
                const visible = getVisibleMessages();
                if (visible.length === 0) return;

                const latestVisible = visible[visible.length - 1];
                if (latestVisible && latestVisible !== lastReadMessageId) {
                    lastReadMessageId = latestVisible;
                    Livewire.dispatch('messageVisible', {{ $conversationId }}, parseInt(latestVisible));
                }
            };

            container.addEventListener('scroll', () => {
                userScrolled = true;
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(markVisibleAsRead, 200);
            });

            Livewire.hook('morph.updated', () => {
                if (isNewlySelected) {
                    setTimeout(() => {
                        container.scrollTop = container.scrollHeight;
                        markVisibleAsRead();
                        isNewlySelected = false;
                    }, 100);
                } else if (userScrolled) {
                    markVisibleAsRead();
                }
            });

            setTimeout(() => {
                if (isNewlySelected) {
                    container.scrollTop = container.scrollHeight;
                    markVisibleAsRead();
                    isNewlySelected = false;
                }
            }, 100);
        @endif
    });
    </script>

 --}}
