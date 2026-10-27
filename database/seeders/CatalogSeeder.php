<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Seeder;

/**
 * Demo catalog: subjects → courses (path / topic) → units → lessons → exercises.
 *
 * Exercise shorthand: [prompt, 'choice', [options], correctIndex, emoji?]
 *                     [prompt, 'tf', bool, emoji?]
 *                     [prompt, 'num', number, emoji?]
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->catalog() as $sort => $data) {
            $courses = $data['courses'];
            unset($data['courses']);

            $subject = Subject::updateOrCreate(['slug' => $data['slug']], $data + ['sort' => $sort]);

            foreach ($courses as $courseSort => $courseData) {
                $this->seedCourse($subject, $courseData, $courseSort);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function seedCourse(Subject $subject, array $data, int $sort): void
    {
        $units = $data['units'];
        unset($data['units']);

        $course = Course::updateOrCreate(
            ['subject_id' => $subject->id, 'title' => $data['title']],
            $data + ['sort' => $sort],
        );
        $course->units()->delete();

        foreach ($units as $unitSort => $unitData) {
            $lessons = $unitData['lessons'];
            unset($unitData['lessons']);
            $unit = $course->units()->create($unitData + ['sort' => $unitSort]);

            $lessonSort = 0;
            foreach ($lessons as $title => $exercises) {
                $lesson = $unit->lessons()->create(['title' => $title, 'sort' => $lessonSort++, 'est_minutes' => max(1, intdiv(count($exercises), 2))]);

                foreach ($exercises as $exerciseSort => $exercise) {
                    $lesson->exercises()->create($this->exercise($exercise) + ['sort' => $exerciseSort]);
                }
            }
        }
    }

    /**
     * @param  list<mixed>  $row
     * @return array<string, mixed>
     */
    private function exercise(array $row): array
    {
        return match ($row[1]) {
            'choice' => ['prompt' => $row[0], 'type' => 'choice', 'payload' => ['options' => $row[2], 'correct' => $row[3]], 'emoji' => $row[4] ?? null],
            'tf' => ['prompt' => $row[0], 'type' => 'true_false', 'payload' => ['correct' => $row[2]], 'emoji' => $row[3] ?? null],
            'num' => ['prompt' => $row[0], 'type' => 'number_input', 'payload' => ['answer' => $row[2]], 'emoji' => $row[3] ?? null],
        };
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function catalog(): array
    {
        return [
            [
                'slug' => 'math', 'name' => 'Math', 'hint' => 'Numbers, shapes…', 'icon' => '➗', 'color' => 'violet',
                'courses' => [
                    [
                        'kind' => 'path', 'title' => 'Math: Grade 1', 'description' => 'Numbers and first sums.', 'icon' => '🔢', 'grade_min' => 1, 'grade_max' => 1,
                        'units' => [
                            ['title' => 'Numbers 1–10', 'icon' => '🔢', 'lessons' => [
                                'Counting' => [
                                    ['How many apples?', 'num', 3, '🍎🍎🍎'],
                                    ['How many stars?', 'num', 5, '⭐⭐⭐⭐⭐'],
                                    ['Which number comes after 4?', 'choice', ['3', '5', '6'], 1],
                                    ['7 is bigger than 5.', 'tf', true],
                                    ['How many fingers on one hand?', 'num', 5, '✋'],
                                ],
                                'Bigger or smaller' => [
                                    ['Which number is the biggest?', 'choice', ['2', '9', '4'], 1],
                                    ['Which number is the smallest?', 'choice', ['8', '3', '6'], 1],
                                    ['3 is bigger than 8.', 'tf', false],
                                    ['What number is between 4 and 6?', 'num', 5],
                                ],
                            ]],
                            ['title' => 'Add & subtract', 'icon' => '➕', 'lessons' => [
                                'Adding' => [
                                    ['1 + 1 = ?', 'num', 2],
                                    ['2 + 3 = ?', 'num', 5],
                                    ['4 + 4 = ?', 'choice', ['6', '8', '9'], 1],
                                    ['5 + 0 = 5', 'tf', true],
                                ],
                                'Taking away' => [
                                    ['5 − 2 = ?', 'num', 3],
                                    ['9 − 4 = ?', 'num', 5],
                                    ['7 − 7 = ?', 'choice', ['0', '1', '7'], 0],
                                    ['6 − 1 = 4', 'tf', false],
                                ],
                            ]],
                        ],
                    ],
                    [
                        'kind' => 'topic', 'title' => 'Multiplication', 'description' => 'From easy to hard.', 'icon' => '✖️', 'grade_min' => 2, 'grade_max' => 5,
                        'units' => [
                            ['title' => 'Easy', 'level' => 1, 'lessons' => [
                                'Times 2 and 5' => [
                                    ['2 × 3 = ?', 'num', 6],
                                    ['5 × 2 = ?', 'num', 10],
                                    ['2 × 4 = ?', 'choice', ['6', '8', '10'], 1],
                                    ['5 × 1 = 5', 'tf', true],
                                ],
                            ]],
                            ['title' => 'Medium', 'level' => 2, 'lessons' => [
                                'Times 3 and 4' => [
                                    ['3 × 4 = ?', 'num', 12],
                                    ['4 × 4 = ?', 'num', 16],
                                    ['3 × 3 = ?', 'choice', ['6', '9', '12'], 1],
                                    ['4 × 3 = 11', 'tf', false],
                                ],
                            ]],
                            ['title' => 'Hard', 'level' => 3, 'lessons' => [
                                'Times 6 to 9' => [
                                    ['6 × 7 = ?', 'num', 42],
                                    ['8 × 8 = ?', 'num', 64],
                                    ['9 × 6 = ?', 'choice', ['54', '56', '63'], 0],
                                    ['7 × 8 = 56', 'tf', true],
                                ],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'alphabet', 'name' => 'Alphabet', 'hint' => 'A–Z & phonics', 'icon' => '🔤', 'color' => 'sun',
                'courses' => [
                    [
                        'kind' => 'path', 'title' => 'Alphabet: Grade 1', 'description' => 'First letters and sounds.', 'icon' => '🔤', 'grade_min' => 1, 'grade_max' => 1,
                        'units' => [
                            ['title' => 'Letters A–E', 'lessons' => [
                                'First letters' => [
                                    ['Which letter starts "Apple"?', 'choice', ['A', 'B', 'C'], 0, '🍎'],
                                    ['Which letter starts "Ball"?', 'choice', ['A', 'B', 'D'], 1, '⚽'],
                                    ['Which letter starts "Egg"?', 'choice', ['E', 'D', 'A'], 0, '🥚'],
                                    ['"Cat" starts with C.', 'tf', true, '🐱'],
                                ],
                                'Letter order' => [
                                    ['Which letter comes after A?', 'choice', ['C', 'B', 'D'], 1],
                                    ['Which letter comes before D?', 'choice', ['C', 'E', 'B'], 0],
                                    ['E comes before B.', 'tf', false],
                                ],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'animals', 'name' => 'Animals', 'hint' => 'Wildlife & habitats', 'icon' => '🦁', 'color' => 'mint',
                'courses' => [
                    [
                        'kind' => 'path', 'title' => 'Animals: Grade 1', 'description' => 'Farm and wild animals.', 'icon' => '🐾', 'grade_min' => 1, 'grade_max' => 2,
                        'units' => [
                            ['title' => 'Animals around us', 'lessons' => [
                                'Farm animals' => [
                                    ['What animal says "Moo"?', 'choice', ['Dog', 'Cow', 'Sheep'], 1, '🐄'],
                                    ['What animal gives us wool?', 'choice', ['Sheep', 'Hen', 'Horse'], 0, '🐑'],
                                    ['A hen lays eggs.', 'tf', true, '🐔'],
                                ],
                                'Wild animals' => [
                                    ['Which animal is the king of the jungle?', 'choice', ['Lion', 'Mouse', 'Duck'], 0, '🦁'],
                                    ['Elephants have a long trunk.', 'tf', true, '🐘'],
                                    ['How many legs does a spider have?', 'num', 8, '🕷️'],
                                ],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'words', 'name' => 'Words', 'hint' => 'Read & spell', 'icon' => '📚', 'color' => 'coral',
                'courses' => [
                    [
                        'kind' => 'topic', 'title' => 'Opposites', 'description' => 'Find the opposite word.', 'icon' => '↔️', 'grade_min' => 1, 'grade_max' => 3,
                        'units' => [
                            ['title' => 'Easy', 'level' => 1, 'lessons' => [
                                'Big and small' => [
                                    ['The opposite of "big" is…', 'choice', ['small', 'tall', 'red'], 0],
                                    ['The opposite of "hot" is…', 'choice', ['warm', 'cold', 'dry'], 1],
                                    ['The opposite of "up" is "down".', 'tf', true],
                                ],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'knowledge', 'name' => 'Knowledge', 'hint' => 'World & science', 'icon' => '🌍', 'color' => 'sky',
                'courses' => [
                    [
                        'kind' => 'path', 'title' => 'Knowledge: Grade 2', 'description' => 'Our planet and the sky.', 'icon' => '🌍', 'grade_min' => 2, 'grade_max' => 2,
                        'units' => [
                            ['title' => 'Our planet', 'lessons' => [
                                'Earth and sky' => [
                                    ['What do we call the star that warms the Earth?', 'choice', ['Moon', 'Sun', 'Cloud'], 1, '☀️'],
                                    ['The Earth is flat.', 'tf', false, '🌍'],
                                    ['How many days are in a week?', 'num', 7, '📅'],
                                ],
                            ]],
                        ],
                    ],
                ],
            ],
        ];
    }
}
