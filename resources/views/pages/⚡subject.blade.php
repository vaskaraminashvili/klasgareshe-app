<?php

use App\Models\Subject;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component
{
    public Subject $subject;

    public function mount(Subject $subject): void
    {
        abort_unless($subject->is_active, 404);

        $this->subject = $subject;
    }

    public function with(): array
    {
        return [
            'paths' => $this->subject->pathCourses()->get(),
            'topics' => $this->subject->topicCourses()->get(),
        ];
    }
};
?>

<div class="flex flex-col grow">
  <x-kidzio.appbar :back="route('home')" eyebrow="Subject" :title="$subject->name" />

  @if ($paths->isNotEmpty())
    <section class="px-5 mt-2">
      <div class="section-head"><h2 class="h-display text-lg">School path</h2></div>
      <div class="k-card p-0 overflow-hidden">
        @foreach ($paths as $course)
          <x-kidzio.course-row :$course :color="$subject->color" wire:key="path-{{ $course->id }}" />
        @endforeach
      </div>
    </section>
  @endif

  @if ($topics->isNotEmpty())
    <section class="px-5 mt-5 mb-5">
      <div class="section-head"><h2 class="h-display text-lg">Topics</h2></div>
      <div class="k-card p-0 overflow-hidden">
        @foreach ($topics as $course)
          <x-kidzio.course-row :$course :color="$subject->color" wire:key="topic-{{ $course->id }}" />
        @endforeach
      </div>
    </section>
  @endif

  @if ($paths->isEmpty() && $topics->isEmpty())
    <p class="px-5 mt-6 text-center text-muted">No courses yet.</p>
  @endif
</div>
