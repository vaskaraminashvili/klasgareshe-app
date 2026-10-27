<?php

namespace App\Models;

use Database\Factories\SubjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /** @use HasFactory<SubjectFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<Course, $this> */
    public function pathCourses(): HasMany
    {
        return $this->hasMany(Course::class)->published()->where('kind', 'path')->orderBy('grade_min');
    }

    /** @return HasMany<Course, $this> */
    public function topicCourses(): HasMany
    {
        return $this->hasMany(Course::class)->published()->where('kind', 'topic')->orderBy('sort');
    }

    /**
     * Adds a `lessons_count` column (all lessons in all published courses).
     *
     * @param  Builder<Subject>  $query
     */
    public function scopeWithLessonsCount(Builder $query): void
    {
        $query->addSelect(['lessons_count' => Lesson::query()
            ->selectRaw('count(*)')
            ->join('units', 'units.id', '=', 'lessons.unit_id')
            ->join('courses', 'courses.id', '=', 'units.course_id')
            ->whereColumn('courses.subject_id', 'subjects.id')
            ->where('courses.is_published', true),
        ]);
    }
}
