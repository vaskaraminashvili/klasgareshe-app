<?php

use App\Models\Exercise;
use App\Models\Lesson;
use Livewire\Livewire;

function lessonWith(array $states): Lesson
{
    $lesson = Lesson::factory()->create();
    foreach ($states as $i => $state) {
        Exercise::factory()->for($lesson)->{$state}()->create(['sort' => $i]);
    }

    return $lesson;
}

it('404s for a lesson without exercises', function () {
    $this->get(route('lessons.show', Lesson::factory()->create()))->assertNotFound();
});

it('plays a perfect lesson of all three exercise types and awards 3 stars', function () {
    $lesson = lessonWith(['choice', 'trueFalse', 'numberInput']);

    Livewire::test('pages::lesson', ['lesson' => $lesson])
        ->call('answer', 1)->assertSet('feedback.correct', true)->call('next')
        ->call('answer', true)->assertSet('feedback.correct', true)->call('next')
        ->set('numberAnswer', '2')->call('submitNumber')->assertSet('feedback.correct', true)->call('next')
        ->assertSet('correctFirstTry', 3)
        ->assertSee('Lesson complete!')
        ->assertSee('3 of 3');
});

it('repeats a wrongly answered exercise once at the end', function () {
    $lesson = lessonWith(['choice', 'trueFalse']);

    Livewire::test('pages::lesson', ['lesson' => $lesson])
        ->call('answer', 0)->assertSet('feedback.correct', false)->call('next')
        ->call('answer', true)->call('next')
        ->assertCount('queue', 3)
        ->assertSet('position', 2)
        ->call('answer', 0)->assertSet('feedback.correct', false)->call('next')
        ->assertCount('queue', 3) // not requeued a second time
        ->assertSee('Lesson complete!')
        ->assertSee('1 of 2');
});

it('ignores a second answer and an empty number', function () {
    $lesson = lessonWith(['numberInput']);

    Livewire::test('pages::lesson', ['lesson' => $lesson])
        ->call('submitNumber')->assertSet('feedback', null)
        ->call('answer', '2')->call('answer', '9')
        ->assertSet('feedback.correct', true);
});

it('never sends the correct answer to the browser before answering', function () {
    $lesson = lessonWith(['choice']);

    Livewire::test('pages::lesson', ['lesson' => $lesson])
        ->assertSet('feedback', null)
        ->assertDontSee('"correct"');
});
