<?php

namespace Database\Seeders;

use App\Models\Habit;
use App\Models\HabitCompletion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HabitSeeder extends Seeder
{
    public function run(): void
    {
        $habits = [
            [
                'name' => 'Morning Run',
                'description' => 'Run for 30 minutes',
                'emoji' => '🏃',
                'reminder_time' => '06:30',
                'frequency' => 'weekdays',
            ],
            [
                'name' => 'Read a Book',
                'description' => 'Read at least 20 pages',
                'emoji' => '📚',
                'reminder_time' => '21:00',
                'frequency' => 'daily',
            ],
            [
                'name' => 'Drink Water',
                'description' => '8 glasses throughout the day',
                'emoji' => '💧',
                'reminder_time' => '08:00',
                'frequency' => 'daily',
            ],
            [
                'name' => 'Meditate',
                'description' => '10 minutes of mindfulness',
                'emoji' => '🧘',
                'reminder_time' => '07:00',
                'frequency' => 'daily',
            ],
            [
                'name' => 'Learn Coding',
                'description' => 'Practice for 1 hour',
                'emoji' => '💻',
                'reminder_time' => '19:00',
                'frequency' => 'weekdays',
            ],
        ];

        foreach ($habits as $habitData) {
            $habit = Habit::create($habitData);

            // Add some past completions for realistic streaks
            $daysBack = rand(3, 12);
            for ($i = 1; $i <= $daysBack; $i++) {
                // Skip some days randomly to create varied streaks
                if ($i > 3 && rand(1, 10) <= 2) {
                    continue;
                }

                HabitCompletion::create([
                    'habit_id' => $habit->id,
                    'completed_at' => Carbon::today()->subDays($i)->toDateString(),
                ]);
            }
        }
    }
}
