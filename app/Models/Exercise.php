<?php

namespace App\Models;

use App\Enums\ExerciseType;
use Database\Factories\ExerciseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
    /** @use HasFactory<ExerciseFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['type' => ExerciseType::class, 'payload' => 'array', 'is_active' => 'boolean'];
    }

    public function isCorrect(mixed $answer): bool
    {
        return $this->type->check($this->payload, $answer);
    }

    /** @return BelongsTo<Lesson, $this> */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
