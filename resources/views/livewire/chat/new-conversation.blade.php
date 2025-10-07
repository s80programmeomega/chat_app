<div>
    <button wire:click="openModal" class="btn btn-primary mb-3 w-100">
        <i class="bi bi-plus-circle"></i> New Chat
    </button>

    @if($showModal)
    <div class="modal show d-block" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Start New Conversation</h5>
                    <button wire:click="closeModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="btn-group w-100">
                            <input type="radio" class="btn-check" wire:model.live="conversationType" value="direct" id="direct">
                            <label class="btn btn-outline-primary" for="direct">Direct Message</label>

                            <input type="radio" class="btn-check" wire:model.live="conversationType" value="group" id="group">
                            <label class="btn btn-outline-primary" for="group">Group Chat</label>
                        </div>
                    </div>

                    @if($conversationType === 'direct')
                        <div class="mb-3">
                            <h1 class="text-center text-4xl font-bold text-blue-600">
                                {{ $selectedUserId }}
                            </h1>
                            <label class="form-label">Select User</label>
                            <select wire:model.live="selectedUserId" class="form-select">
                                <option value="">Choose a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedUserId') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" wire:model="groupName" class="form-control" placeholder="Enter group name">
                            @error('groupName') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea wire:model="groupDescription" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input wire:model="isPrivate" class="form-check-input" type="checkbox" id="isPrivate">
                                <label class="form-check-label" for="isPrivate">Private Group</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Add Members</label>
                            @foreach($users as $user)
                                <div class="form-check">
                                    <input wire:click="toggleUserSelection({{ $user->id }})"
                                           class="form-check-input" type="checkbox"
                                           @if(in_array($user->id, $selectedUsers)) checked @endif>
                                    <label class="form-check-label">{{ $user->name }}</label>
                                </div>
                            @endforeach
                            @error('selectedUsers') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn btn-secondary">Cancel</button>
                    <button wire:click="createConversation" class="btn btn-primary">Create</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop show"></div>
    @endif
</div>
