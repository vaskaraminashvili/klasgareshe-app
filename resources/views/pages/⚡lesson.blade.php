<?php

use App\Enums\ExerciseType;
use App\Models\Exercise;
use App\Models\Lesson;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['hideTabbar' => true])] class extends Component
{
    public Lesson $lesson;

    /** Exercise ids still to play; a wrongly answered exercise is appended once. @var list<int> */
    public array $queue = [];

    public int $total = 0;

    public int $position = 0;

    public int $correctFirstTry = 0;

    public string $numberAnswer = '';

    /** @var array{correct: bool, picked: mixed, label: string, explanation: ?string}|null */
    public ?array $feedback = null;

    public function mount(Lesson $lesson): void
    {
        $this->lesson = $lesson->load('unit.course.subject');
        $this->queue = $lesson->exercises()->pluck('id')->all();
        $this->total = count($this->queue);

        abort_if($this->total === 0, 404);
    }

    #[Computed]
    public function exercise(): ?Exercise
    {
        return Exercise::find($this->queue[$this->position] ?? null);
    }

    public function isFinished(): bool
    {
        return $this->position >= count($this->queue);
    }

    public function stars(): int
    {
        $ratio = $this->correctFirstTry / $this->total;

        return $ratio === 1.0 ? 3 : ($ratio >= 0.8 ? 2 : 1);
    }

    public function submitNumber(): void
    {
        if (trim($this->numberAnswer) !== '') {
            $this->answer(trim($this->numberAnswer));
        }
    }

    public function answer(mixed $value): void
    {
        $exercise = $this->exercise;

        if ($exercise === null || $this->feedback !== null) {
            return;
        }

        $correct = $exercise->isCorrect($value);
        $firstTry = $this->position < $this->total;

        if ($correct && $firstTry) {
            $this->correctFirstTry++;
        }

        if (! $correct && $firstTry) {
            $this->queue[] = $exercise->id;
        }

        $this->feedback = [
            'correct' => $correct,
            'picked' => $value,
            'label' => $exercise->type->correctLabel($exercise->payload),
            'explanation' => $exercise->explanation,
        ];
    }

    public function next(): void
    {
        if ($this->feedback === null) {
            return;
        }

        $this->position++;
        $this->feedback = null;
        $this->numberAnswer = '';
        unset($this->exercise);
    }
};
?>

<div class="flex flex-col grow safe-top">
  @php($courseUrl = route('courses.show', $lesson->unit->course))

  @if ($this->isFinished())
    <section class="px-6 mt-16 text-center">
      <div class="text-7xl">{{ str_repeat('⭐', $this->stars()) }}{{ str_repeat('☆', 3 - $this->stars()) }}</div>
      <h1 class="h-display text-2xl mt-4">Lesson complete!</h1>
      <p class="text-muted mt-2">{{ $lesson->title }}</p>
      <p class="mt-4"><span class="chip chip-mint">{{ $correctFirstTry }} of {{ $total }} right first try</span></p>
    </section>

    <div class="mt-auto px-6 pb-6 pt-4 safe-bottom space-y-2">
      <a href="{{ route('lessons.show', $lesson) }}" class="btn btn-primary w-full">Play again</a>
      <a href="{{ $courseUrl }}" wire:navigate class="btn btn-ghost w-full">Back to course</a>
    </div>
  @else
    @php($exercise = $this->exercise)

    <header class="appbar">
      <a href="{{ $courseUrl }}" wire:navigate class="icon-btn" aria-label="Close"><i class="ph ph-x"></i></a>
      <div class="grow"><div class="progress"><span style="width: {{ round($position / count($queue) * 100) }}%"></span></div></div>
    </header>

    <section class="px-6 mt-2 text-center" wire:key="ex-{{ $exercise->id }}-{{ $position }}">
      <p class="text-xs font-extrabold text-muted">QUESTION {{ min($position + 1, $total) }} OF {{ $total }}</p>
      <h1 class="h-display text-2xl mt-2">{{ $exercise->prompt }}</h1>
      @if ($exercise->emoji)<div class="text-6xl mt-4">{{ $exercise->emoji }}</div>@endif
    </section>

    <section class="px-6 mt-5 space-y-3">
      @if ($exercise->type === ExerciseType::Choice)
        @foreach ($exercise->payload['options'] as $index => $option)
          @php($isCorrect = $index === $exercise->payload['correct'])
          @php($isPicked = $feedback !== null && $feedback['picked'] === $index)
          <button type="button" wire:click="answer({{ $index }})" @disabled($feedback !== null)
            class="ans w-full {{ $feedback !== null && $isCorrect ? 'correct' : '' }} {{ $isPicked && ! $isCorrect ? 'wrong' : '' }}">
            <span class="ans-key">{{ chr(65 + $index) }}</span><span class="grow text-left">{{ $option }}</span>
          </button>
        @endforeach
      @elseif ($exercise->type === ExerciseType::TrueFalse)
        @foreach ([true => 'True', false => 'False'] as $value => $label)
          @php($isCorrect = (bool) $value === $exercise->payload['correct'])
          @php($isPicked = $feedback !== null && $feedback['picked'] === (bool) $value)
          <button type="button" wire:click="answer({{ $value ? 'true' : 'false' }})" @disabled($feedback !== null)
            class="ans w-full {{ $feedback !== null && $isCorrect ? 'correct' : '' }} {{ $isPicked && ! $isCorrect ? 'wrong' : '' }}">
            <span class="grow text-left">{{ $label }}</span>
          </button>
        @endforeach
      @else
        <form wire:submit="submitNumber">
          <div class="input-wrap">
            <input type="number" step="any" inputmode="decimal" wire:model="numberAnswer" class="input text-center text-2xl"
              placeholder="?" aria-label="Your answer" autofocus @disabled($feedback !== null) />
          </div>
        </form>
      @endif
    </section>

    <div class="mt-auto px-6 pb-6 pt-4 safe-bottom">
      @if ($feedback)
        <p class="text-center font-extrabold mb-3 {{ $feedback['correct'] ? 'text-mint-ink' : 'text-coral-ink' }}">
          {{ $feedback['correct'] ? 'Great job!' : 'Try again later — answer: ' . $feedback['label'] }}
        </p>
        @if (! $feedback['correct'] && $feedback['explanation'])<p class="text-center text-xs text-muted mb-3">{{ $feedback['explanation'] }}</p>@endif
        <button type="button" wire:click="next" class="btn btn-primary w-full">Continue <i class="ph ph-arrow-right"></i></button>
      @elseif ($exercise->type === ExerciseType::NumberInput)
        <button type="button" wire:click="submitNumber" class="btn btn-primary w-full">Check</button>
      @endif
    </div>
  @endif
</div>
