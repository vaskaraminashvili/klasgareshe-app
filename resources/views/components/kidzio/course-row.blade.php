@props(['course', 'color'])

<a href="{{ route('courses.show', $course) }}" wire:navigate class="flex items-center gap-3 p-3 border-t border-token first:border-t-0">
  <div class="size-10 rounded-xl tile-{{ $color }} grid place-items-center text-xl">{{ $course->icon }}</div>
  <div class="grow">
    <p class="font-extrabold text-sm">{{ $course->title }}</p>
    <p class="text-xs text-muted">{{ $course->description }}</p>
  </div>
  <span class="chip chip-{{ $color }}">{{ $course->gradeLabel() }}</span>
</a>
