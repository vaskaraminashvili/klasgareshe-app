<?php

namespace Database\Factories;

use App\Enums\ExerciseType;
use App\Models\Exercise;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'type' => ExerciseType::Choice,
            'prompt' => 'What is 1 + 1?',
            'payload' => ['options' => ['1', '2', '3'], 'correct' => 1],
        ];
    }

    public function choice(): static
    {
        return $this->state([]);
    }

    public function trueFalse(bool $correct = true): static
    {
        return $this->state(['type' => ExerciseType::TrueFalse, 'payload' => ['correct' => $correct]]);
    }

    public function numberInput(int $answer = 2): static
    {
        return $this->state(['type' => ExerciseType::NumberInput, 'payload' => ['answer' => $answer]]);
    }
}
