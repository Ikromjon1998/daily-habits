<?php

namespace Tests\Feature;

use App\Livewire\Today;
use App\Models\Habit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TodayTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_empty_state_when_no_habits(): void
    {
        $this->get('/')->assertSee('No habits yet');
    }

    public function test_shows_habits_list(): void
    {
        Habit::factory()->create(['name' => 'Morning Run', 'emoji' => '🏃']);

        $this->get('/')
            ->assertSee('Morning Run')
            ->assertSee('🏃');
    }

    public function test_shows_daily_progress(): void
    {
        Habit::factory()->create(['name' => 'Test Habit']);

        $this->get('/')->assertSee('Daily Progress');
    }

    public function test_toggle_habit_creates_completion(): void
    {
        $habit = Habit::factory()->create();

        Livewire::test(Today::class)
            ->call('toggleHabit', $habit->id);

        $this->assertTrue($habit->fresh()->isCompletedToday());
    }

    public function test_toggle_habit_removes_completion_when_already_completed(): void
    {
        $habit = Habit::factory()->create();
        $habit->completions()->create(['completed_at' => today()]);

        Livewire::test(Today::class)
            ->call('toggleHabit', $habit->id);

        $this->assertFalse($habit->fresh()->isCompletedToday());
    }

    public function test_only_shows_active_habits(): void
    {
        Habit::factory()->create(['name' => 'Active Habit', 'is_active' => true]);
        Habit::factory()->create(['name' => 'Inactive Habit', 'is_active' => false]);

        $this->get('/')
            ->assertSee('Active Habit')
            ->assertDontSee('Inactive Habit');
    }
}
