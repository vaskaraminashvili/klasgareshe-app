<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'kind' => 'path',
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'icon' => '📗',
            'grade_min' => 1,
            'grade_max' => 1,
        ];
    }

    public function topic(): static
    {
        return $this->state(['kind' => 'topic', 'grade_min' => 2, 'grade_max' => 5]);
    }
}
