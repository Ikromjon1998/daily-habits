<?php

namespace Database\Factories;

use App\Models\Habit;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Habit> */
class HabitFactory extends Factory
{
    protected $model = Habit::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'emoji' => fake()->randomElement(['🏃', '📚', '💧', '🧘', '💻', '✅']),
            'reminder_time' => sprintf('%02d:%02d', fake()->numberBetween(6, 22), fake()->randomElement([0, 15, 30, 45])),
            'frequency' => 'daily',
            'is_active' => true,
        ];
    }
}
