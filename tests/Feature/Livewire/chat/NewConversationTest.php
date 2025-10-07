<?php

use App\Livewire\NewConversation;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(NewConversation::class)
        ->assertStatus(200);
});
