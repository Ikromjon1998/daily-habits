<?php

namespace Tests\Unit;

use App\Models\Habit;
use App\Models\HabitCompletion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HabitTest extends TestCase
{
    use RefreshDatabase;

    public function test_habit_can_be_created_with_fillable_attributes(): void
    {
        $habit = Habit::create([
            'name' => 'Meditate',
            'description' => '10 minutes of mindfulness',
            'emoji' => '🧘',
            'reminder_time' => '07:00',
            'frequency' => 'daily',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('habits', ['name' => 'Meditate', 'emoji' => '🧘']);
        $this->assertTrue($habit->is_active);
    }

    public function test_habit_has_many_completions(): void
    {
        $habit = Habit::factory()->create();
        $habit->completions()->create(['completed_at' => today()]);

        $this->assertCount(1, $habit->completions);
        $this->assertInstanceOf(HabitCompletion::class, $habit->completions->first());
    }

    public function test_is_completed_today_returns_true_when_completed(): void
    {
        $habit = Habit::factory()->create();
        $habit->completions()->create(['completed_at' => today()]);

        $this->assertTrue($habit->isCompletedToday());
    }

    public function test_is_completed_today_returns_false_when_not_completed(): void
    {
        $habit = Habit::factory()->create();

        $this->assertFalse($habit->isCompletedToday());
    }

    public function test_is_completed_on_specific_date(): void
    {
        $habit = Habit::factory()->create();
        $date = '2026-03-05';
        $habit->completions()->create(['completed_at' => $date]);

        $this->assertTrue($habit->isCompletedOn($date));
        $this->assertFalse($habit->isCompletedOn('2026-03-06'));
    }

    public function test_toggle_today_creates_completion_when_not_exists(): void
    {
        $habit = Habit::factory()->create();

        $result = $habit->toggleToday();

        $this->assertTrue($result);
        $this->assertTrue($habit->isCompletedToday());
    }

    public function test_toggle_today_removes_completion_when_exists(): void
    {
        $habit = Habit::factory()->create();
        $habit->completions()->create(['completed_at' => today()]);

        $result = $habit->toggleToday();

        $this->assertFalse($result);
        $this->assertFalse($habit->isCompletedToday());
    }

    public function test_current_streak_counts_consecutive_days(): void
    {
        $habit = Habit::factory()->create();

        // Complete today and 2 days before
        $habit->completions()->create(['completed_at' => today()]);
        $habit->completions()->create(['completed_at' => today()->subDay()]);
        $habit->completions()->create(['completed_at' => today()->subDays(2)]);

        $this->assertEquals(3, $habit->currentStreak());
    }

    public function test_current_streak_starts_from_yesterday_if_today_not_completed(): void
    {
        $habit = Habit::factory()->create();

        $habit->completions()->create(['completed_at' => today()->subDay()]);
        $habit->completions()->create(['completed_at' => today()->subDays(2)]);

        $this->assertEquals(2, $habit->currentStreak());
    }

    public function test_current_streak_returns_zero_with_no_completions(): void
    {
        $habit = Habit::factory()->create();

        $this->assertEquals(0, $habit->currentStreak());
    }

    public function test_longest_streak_finds_longest_run(): void
    {
        $habit = Habit::factory()->create();

        // Old streak of 3
        $habit->completions()->create(['completed_at' => '2026-01-01']);
        $habit->completions()->create(['completed_at' => '2026-01-02']);
        $habit->completions()->create(['completed_at' => '2026-01-03']);

        // Gap, then streak of 2
        $habit->completions()->create(['completed_at' => '2026-01-10']);
        $habit->completions()->create(['completed_at' => '2026-01-11']);

        $this->assertEquals(3, $habit->longestStreak());
    }

    public function test_longest_streak_returns_zero_with_no_completions(): void
    {
        $habit = Habit::factory()->create();

        $this->assertEquals(0, $habit->longestStreak());
    }

    public function test_should_show_today_returns_true_for_daily_habit(): void
    {
        $habit = Habit::factory()->create(['frequency' => 'daily']);

        $this->assertTrue($habit->shouldShowToday());
    }

    public function test_should_show_today_returns_false_for_inactive_habit(): void
    {
        $habit = Habit::factory()->create(['is_active' => false]);

        $this->assertFalse($habit->shouldShowToday());
    }

    public function test_should_show_today_weekdays_only_on_weekdays(): void
    {
        $habit = Habit::factory()->create(['frequency' => 'weekdays']);

        Carbon::setTestNow(Carbon::parse('2026-03-09')); // Monday
        $this->assertTrue($habit->shouldShowToday());

        Carbon::setTestNow(Carbon::parse('2026-03-07')); // Saturday
        $this->assertFalse($habit->shouldShowToday());

        Carbon::setTestNow(); // Reset
    }

    public function test_should_show_today_weekends_only_on_weekends(): void
    {
        $habit = Habit::factory()->create(['frequency' => 'weekends']);

        Carbon::setTestNow(Carbon::parse('2026-03-07')); // Saturday
        $this->assertTrue($habit->shouldShowToday());

        Carbon::setTestNow(Carbon::parse('2026-03-09')); // Monday
        $this->assertFalse($habit->shouldShowToday());

        Carbon::setTestNow(); // Reset
    }

    public function test_total_completions_returns_count(): void
    {
        $habit = Habit::factory()->create();
        $habit->completions()->create(['completed_at' => '2026-03-01']);
        $habit->completions()->create(['completed_at' => '2026-03-02']);
        $habit->completions()->create(['completed_at' => '2026-03-03']);

        $this->assertEquals(3, $habit->totalCompletions());
    }

    public function test_weekly_completions_returns_7_day_map(): void
    {
        $habit = Habit::factory()->create();

        $result = $habit->weeklyCompletions();

        $this->assertCount(7, $result);
        $this->assertContainsOnly('bool', $result);
    }

    public function test_streak_milestone_returns_value_at_milestones(): void
    {
        $habit = Habit::factory()->create();

        // Create 7-day streak
        for ($i = 0; $i < 7; $i++) {
            $habit->completions()->create(['completed_at' => today()->subDays($i)]);
        }

        $this->assertEquals(7, $habit->streakMilestone());
    }

    public function test_streak_milestone_returns_null_when_not_at_milestone(): void
    {
        $habit = Habit::factory()->create();
        $habit->completions()->create(['completed_at' => today()]);

        $this->assertNull($habit->streakMilestone());
    }
}
