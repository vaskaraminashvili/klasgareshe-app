<?php

use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Home')] class extends Component
{
    public string $kidName = 'Luna';

    /** @return Collection<int, Subject> */
    #[Computed]
    public function subjects(): Collection
    {
        return Subject::query()->where('is_active', true)->orderBy('sort')->withLessonsCount()->get();
    }
};
?>

<div class="flex flex-col grow">
  {{-- Header: who is learning + theme toggle --}}
  <header class="px-5 pt-4 safe-top flex items-center gap-3">
    <a href="profile.html" class="size-11 rounded-2xl grid place-items-center text-2xl tile-sun" aria-label="Open profile">🐻</a>
    <div class="grow leading-tight">
      <p class="text-xs text-muted">Let's learn</p>
      <p class="h-display text-lg">Hi, {{ $kidName }}!</p>
    </div>
    <button class="icon-btn" data-theme-toggle aria-label="Toggle theme"><i class="ph ph-moon text-xl"></i></button>
  </header>

  {{-- Progress at a glance --}}
  <section class="px-5 mt-5 grid grid-cols-3 gap-2">
    <x-kidzio.stat label="🔥 Streak" value="7" unit="days" href="streak.html" />
    <x-kidzio.stat label="⭐ XP" value="1,240" href="xp-progress.html" />
    <x-kidzio.stat label="🏆 League" value="Gold" href="league.html" />
  </section>

  {{-- The one thing to do today --}}
  <section class="px-5 mt-4">
    <a href="daily-mission.html" class="block k-card-lg card-hero-primary relative overflow-hidden focus:outline-none">
      <span class="watermark-emoji" aria-hidden="true">🎯</span>
      <div class="relative flex items-center gap-2">
        <span class="chip bg-white/20 border-0 text-white"><i class="ph-fill ph-sparkle"></i> TODAY'S MISSION</span>
        <span class="chip bg-white/20 border-0 text-white ml-auto"><i class="ph-fill ph-clock"></i> 6h left</span>
      </div>
      <p class="h-display text-2xl mt-2 relative">Finish 3 lessons</p>
      <p class="text-sm text-white relative">Earn <b>+120 XP</b> and a bonus badge.</p>
      <div class="mt-3 flex items-center gap-2 relative">
        <div class="progress on-gradient grow"><span class="w-66"></span></div>
        <span class="text-xs font-extrabold">2 / 3</span>
      </div>
      <p class="cta-soft mt-3 inline-block relative">Continue <i class="ph-fill ph-arrow-right"></i></p>
    </a>
  </section>

  {{-- Pick up where you left off --}}
  <section class="px-5 mt-4">
    <a href="lesson-continue.html" class="k-card p-4 relative overflow-hidden block">
      <div class="flex items-center gap-2">
        <div class="size-9 rounded-xl tile-mint grid place-items-center">➗</div>
        <span class="text-xs font-extrabold text-mint-ink">CONTINUE</span>
      </div>
      <p class="h-display mt-2">Counting 1–20</p>
      <p class="text-xs text-muted">3 of 5 activities</p>
      <div class="progress progress-mint mt-2"><span class="w-72"></span></div>
    </a>
  </section>

  {{-- Browse --}}
  <section class="mt-5">
    <div class="section-head px-5">
      <h2 class="h-display text-lg">Explore subjects</h2>
      <a href="learn-categories.html" class="link">See all</a>
    </div>
    <div class="swiper subjects-swiper" data-swiper-rail>
      <div class="swiper-wrapper">
        @foreach ($this->subjects as $subject)
          <x-kidzio.subject-tile :href="route('subjects.show', $subject)" :title="$subject->name" :lessons="$subject->lessons_count"
            :hint="$subject->hint" :emoji="$subject->icon" :color="$subject->color" wire:key="subject-{{ $subject->id }}" />
        @endforeach
      </div>
    </div>
  </section>

  {{-- One featured game --}}
  <section class="px-5 mt-4 mb-5">
    <a href="game-multiple-choice.html" class="k-card-lg card-hero-success relative overflow-hidden block">
      <span class="watermark-emoji" aria-hidden="true">❓</span>
      <div class="relative flex items-center gap-3">
        <div class="size-14 rounded-2xl bg-white/25 grid place-items-center text-3xl">❓</div>
        <div class="grow">
          <p class="text-xs uppercase tracking-wider font-extrabold">QUICK QUIZ</p>
          <p class="h-display text-xl">Beat yesterday's score</p>
          <p class="text-xs text-white mt-1">10 questions · ~2 min</p>
        </div>
        <span class="cta-soft">Play</span>
      </div>
    </a>
  </section>
</div>
