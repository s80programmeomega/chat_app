<div>
    @if($conversation->is_group && $conversation->isUserAdmin(auth()->user()))
        <button wire:click="openModal" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-gear"></i> Manage
        </button>
    @endif

    @if($showModal)
    <div class="modal show d-block" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage {{ $conversation->name }}</h5>
                    <button wire:click="closeModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <button class="nav-link @if($activeTab === 'members') active @endif"
                                    wire:click="$set('activeTab', 'members')">Members</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link @if($activeTab === 'settings') active @endif"
                                    wire:click="$set('activeTab', 'settings')">Settings</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link @if($activeTab === 'requests') active @endif"
                                    wire:click="$set('activeTab', 'requests')">
                                Requests @if($pendingRequests->count()) ({{ $pendingRequests->count() }}) @endif
                            </button>
                        </li>
                    </ul>

                    @if($activeTab === 'members')
                        @foreach($members as $member)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>{{ $member->name }}</strong>
                                    @if($member->pivot->is_admin) <span class="badge bg-primary">Admin</span> @endif
                                    @if($member->id === $conversation->created_by) <span class="badge bg-success">Creator</span> @endif
                                </div>
                                @if($member->id !== $conversation->created_by)
                                    <div>
                                        <button wire:click="toggleAdmin({{ $member->id }})" class="btn btn-sm btn-outline-primary">
                                            @if($member->pivot->is_admin) Remove Admin @else Make Admin @endif
                                        </button>
                                        <button wire:click="removeUser({{ $member->id }})" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif

                    @if($activeTab === 'settings')
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input wire:click="togglePrivacy" class="form-check-input" type="checkbox"
                                       @if($conversation->is_private) checked @endif>
                                <label class="form-check-label">Private Group</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input wire:click="toggleMessaging" class="form-check-input" type="checkbox"
                                       @if($conversation->messaging_enabled) checked @endif>
                                <label class="form-check-label">Allow Member Messaging</label>
                            </div>
                        </div>
                    @endif

                    @if($activeTab === 'requests')
                        @forelse($pendingRequests as $request)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>{{ $request->user->name }}</strong>
                                    @if($request->message)
                                        <p class="mb-0 text-muted small">{{ $request->message }}</p>
                                    @endif
                                </div>
                                <div>
                                    <button wire:click="approveJoinRequest({{ $request->id }})" class="btn btn-sm btn-success">Approve</button>
                                    <button wire:click="denyJoinRequest({{ $request->id }})" class="btn btn-sm btn-danger">Deny</button>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No pending requests</p>
                        @endforelse
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop show"></div>
    @endif
</div>
