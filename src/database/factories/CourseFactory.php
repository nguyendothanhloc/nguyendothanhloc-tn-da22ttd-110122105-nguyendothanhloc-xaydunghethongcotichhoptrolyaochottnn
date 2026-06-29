<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'language' => fake()->randomElement(['English', 'Japanese', 'Korean', 'Chinese', 'French', 'Spanish']),
            'level' => fake()->randomElement(['beginner', 'elementary', 'intermediate', 'advanced']),
            'duration_weeks' => fake()->numberBetween(4, 52),
            'price' => fake()->randomFloat(2, 1000000, 10000000),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the course is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the course is for beginners.
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'beginner',
        ]);
    }

    /**
     * Indicate that the course is for intermediate level.
     */
    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'intermediate',
        ]);
    }
}
