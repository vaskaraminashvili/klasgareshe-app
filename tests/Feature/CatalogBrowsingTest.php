<?php

use App\Models\Course;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use Database\Seeders\CatalogSeeder;

it('shows school paths and topics of a subject', function () {
    $subject = Subject::factory()->create();
    Course::factory()->for($subject)->create(['title' => 'Path A']);
    Course::factory()->topic()->for($subject)->create(['title' => 'Topic B']);
    Course::factory()->for($subject)->create(['title' => 'Draft C', 'is_published' => false]);

    $this->get(route('subjects.show', $subject))
        ->assertOk()
        ->assertSeeInOrder(['School path', 'Path A', 'Topics', 'Topic B'])
        ->assertDontSee('Draft C');
});

it('404s for an inactive subject', function () {
    $subject = Subject::factory()->create(['is_active' => false]);

    $this->get(route('subjects.show', $subject))->assertNotFound();
});

it('lists units and lessons of a course', function () {
    $course = Course::factory()->create();
    $unit = Unit::factory()->for($course)->create(['title' => 'Unit One']);
    Lesson::factory()->for($unit)->create(['title' => 'Lesson Alpha']);

    $this->get(route('courses.show', $course))
        ->assertOk()
        ->assertSeeInOrder(['Unit One', 'Lesson Alpha']);
});

it('seeds a catalog where every lesson has exercises', function () {
    $this->seed(CatalogSeeder::class);

    expect(Subject::count())->toBe(5)
        ->and(Lesson::doesntHave('exercises')->count())->toBe(0)
        ->and(Exercise::count())->toBeGreaterThan(40);

    // idempotent
    $this->seed(CatalogSeeder::class);
    expect(Subject::count())->toBe(5);
});
