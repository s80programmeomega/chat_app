

<form wire:submit="sendMessage" class="p-3">
    <div class="input-group">
        <input
            type="text"
            wire:model="content"
            class="form-control"
            placeholder="Type a message..."
            wire:keydown.enter="sendMessage"
        >
        <button
            type="submit"
            class="btn btn-primary"
            {{ empty($content) ? 'disabled' : '' }}
        >
            <i class="bi bi-send"></i>
        </button>
    </div>
</form>
