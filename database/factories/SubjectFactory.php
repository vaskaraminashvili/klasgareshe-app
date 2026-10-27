<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'slug' => $name,
            'name' => ucfirst($name),
            'hint' => fake()->words(2, true),
            'icon' => '📘',
            'color' => fake()->randomElement(['violet', 'sun', 'mint', 'coral', 'sky']),
            'sort' => fake()->numberBetween(0, 20),
        ];
    }
}
