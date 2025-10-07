<?php

use App\Livewire\Chat\PublicGroups;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(PublicGroups::class)
        ->assertStatus(200);
});
