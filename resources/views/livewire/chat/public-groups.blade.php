<div>
    <button wire:click="openModal" class="btn btn-outline-primary btn-sm w-100 mb-2">
        <i class="bi bi-search"></i> Browse Public Groups
    </button>

    @if($showModal)
    <div class="modal show d-block">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Public Groups</h5>
                    <button wire:click="closeModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    @forelse($publicGroups as $group)
                        <div class="border rounded p-3 mb-2">
                            <h6>{{ $group->name }}</h6>
                            @if($group->description)
                                <p class="text-muted small">{{ $group->description }}</p>
                            @endif
                            <small class="text-muted">Created by {{ $group->creator?->name ?? 'Unknown' }}</small>
                            <div class="mt-2">
                                <livewire:chat.join-group-request :conversation="$group" :key="'join-'.$group->id" />
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No public groups available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop show"></div>
    @endif
</div>
