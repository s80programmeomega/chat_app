
<div class="p-3 border-top">
    @if($conversationId && $canMessage)
    <form wire:submit="sendMessage" class="p-3">
        <div class="input-group">
            <input
                type="text"
                wire:model.live="content"
                class="form-control"
                placeholder="Type a message..."
                wire:keydown.enter="sendMessage"
            >
            <button
            type="submit"
            class="btn btn-primary"
            @if(empty($content)) disabled @endif
        >
            <i class="bi {{ empty($content) ? 'bi-send' : 'bi-send-fill' }}"></i>
        </button>

        </div>
    </form>
    @elseif($conversationId && !$canMessage)
        <div class="text-center text-muted">
            <small>Messaging is disabled by group admin</small>
        </div>
    @else
        <div class="text-center text-muted">
            <small>Select a conversation to start messaging</small>
        </div>
    @endif
</div>

