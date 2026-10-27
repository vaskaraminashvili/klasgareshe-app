<?php

namespace App\Enums;

enum ExerciseType: string
{
    case Choice = 'choice';
    case TrueFalse = 'true_false';
    case NumberInput = 'number_input';

    /**
     * Payload shapes:
     * choice: {options: list<string>, correct: int}
     * true_false: {correct: bool}
     * number_input: {answer: int|float}
     *
     * @param  array<string, mixed>  $payload
     */
    public function check(array $payload, mixed $answer): bool
    {
        return match ($this) {
            self::Choice => is_numeric($answer) && (int) $answer === $payload['correct'],
            self::TrueFalse => filter_var($answer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === $payload['correct'],
            self::NumberInput => is_numeric($answer) && (float) $answer === (float) $payload['answer'],
        };
    }

    /**
     * Human readable correct answer, shown after a wrong try.
     *
     * @param  array<string, mixed>  $payload
     */
    public function correctLabel(array $payload): string
    {
        return match ($this) {
            self::Choice => (string) $payload['options'][$payload['correct']],
            self::TrueFalse => $payload['correct'] ? 'True' : 'False',
            self::NumberInput => (string) $payload['answer'],
        };
    }
}
