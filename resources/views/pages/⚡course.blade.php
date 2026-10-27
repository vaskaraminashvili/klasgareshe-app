<?php

use App\Models\Course;
use Livewire\Component;

new class extends Component
{
    public Course $course;

    public function mount(Course $course): void
    {
        abort_unless($course->is_published, 404);

        $this->course = $course->load('subject', 'units.lessons');
    }
};
?>

<div class="flex flex-col grow">
  <x-kidzio.appbar :back="route('subjects.show', $course->subject)"
    :eyebrow="$course->subject->name . ' · ' . ($course->isTopic() ? 'Topic' : 'School path')" :title="$course->title" />

  @foreach ($course->units as $unit)
    <section class="px-5 mt-4 {{ $loop->last ? 'mb-5' : '' }}" wire:key="unit-{{ $unit->id }}">
      <div class="section-head">
        <h2 class="h-display text-lg">{{ $unit->title }}</h2>
        @if ($unit->level)<span class="chip chip-{{ $course->subject->color }}">Level {{ $unit->level }}</span>@endif
      </div>
      <div class="k-card p-0 overflow-hidden">
        @foreach ($unit->lessons as $lesson)
          <a href="{{ route('lessons.show', $lesson) }}" wire:navigate class="flex items-center gap-3 p-3 border-t border-token first:border-t-0" wire:key="lesson-{{ $lesson->id }}">
            <div class="size-10 rounded-xl tile-{{ $course->subject->color }} grid place-items-center font-extrabold">{{ $loop->iteration }}</div>
            <div class="grow">
              <p class="font-extrabold text-sm">{{ $lesson->title }}</p>
              <p class="text-xs text-muted">~{{ $lesson->est_minutes }} min · +{{ $lesson->xp_reward }} XP</p>
            </div>
            <span class="btn btn-primary h-9 min-h-0 px-4 text-sm">Start</span>
          </a>
        @endforeach
      </div>
    </section>
  @endforeach
</div>
