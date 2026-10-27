@props(['href', 'title', 'lessons', 'hint', 'emoji', 'color'])

<a href="{{ $href }}" class="swiper-slide tile tile-{{ $color }}">
  <span class="chip chip-on-tile text-{{ $color }}-ink">{{ $lessons }} lessons</span>
  <h3 class="mt-3">{{ $title }}</h3>
  <p class="text-xs mt-1 text-{{ $color }}-ink opacity-80">{{ $hint }}</p>
  <span class="tile-emoji">{{ $emoji }}</span>
</a>
