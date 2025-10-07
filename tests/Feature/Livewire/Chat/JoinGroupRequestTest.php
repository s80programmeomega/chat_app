<?php

use App\Livewire\Chat\JoinGroupRequest;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(JoinGroupRequest::class)
        ->assertStatus(200);
});
