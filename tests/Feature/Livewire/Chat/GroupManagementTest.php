<?php

use App\Livewire\Chat\GroupManagement;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(GroupManagement::class)
        ->assertStatus(200);
});
