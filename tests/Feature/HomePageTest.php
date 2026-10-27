<?php

use App\Models\Subject;
use Livewire\Livewire;

it('renders the simplified home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Hi, Luna!')
        ->assertSee('Finish 3 lessons')
        ->assertSee('Explore subjects');
});

it('lists active subjects from the database with links', function () {
    $math = Subject::factory()->create(['name' => 'Math', 'slug' => 'math']);
    Subject::factory()->create(['name' => 'Hidden', 'is_active' => false]);

    Livewire::test('pages::home')
        ->assertSee('Math')
        ->assertDontSee('Hidden')
        ->assertSee(route('subjects.show', $math));
});
