@extends('layouts.app')

@section('content')
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-md-4 col-lg-3 p-0">
                <livewire:chat.chat-sidebar />
            </div>
            <div class="col-md-8 col-lg-9 p-0">
                <livewire:chat.chat-window />
            </div>
        </div>
    </div>
    @push('scripts')
@endpush
@endsection
