<div>
    @if(!$conversation->users->contains(auth()->id()) && !$conversation->is_private)
        <div class="p-3 border rounded mb-3">
            <h6>Join {{ $conversation->name }}</h6>
            <div class="mb-2">
                <textarea wire:model="message" class="form-control" placeholder="Optional message to admin" rows="2"></textarea>
            </div>
            <button wire:click="requestJoin" class="btn btn-primary btn-sm">Request to Join</button>
        </div>
    @endif
</div>
