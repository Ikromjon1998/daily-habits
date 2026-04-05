<?php

namespace Tests\Feature;

use App\Models\Habit;
use App\Services\HabitNotificationService;
use Ikromjon\LocalNotifications\Data\NotificationAction;
use Ikromjon\LocalNotifications\Data\NotificationOptions;
use Ikromjon\LocalNotifications\Enums\RepeatInterval;
use Ikromjon\LocalNotifications\Facades\LocalNotifications;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private HabitNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HabitNotificationService;
    }

    public function test_daily_habit_uses_repeat_daily(): void
    {
        $habit = Habit::factory()->create(['frequency' => 'daily']);

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(function (NotificationOptions $options) {
                return $options->repeat === RepeatInterval::Daily
                    && $options->repeatDays === null;
            });

        $this->service->schedule($habit);
    }

    public function test_weekday_habit_uses_repeat_days_1_through_5(): void
    {
        $habit = Habit::factory()->create(['frequency' => 'weekdays']);

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(function (NotificationOptions $options): bool {
                return $options->repeat === null
                    && $options->repeatDays === [1, 2, 3, 4, 5];
            });

        $this->service->schedule($habit);
    }

    public function test_weekend_habit_uses_repeat_days_6_and_7(): void
    {
        $habit = Habit::factory()->create(['frequency' => 'weekends']);

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(function (NotificationOptions $options): bool {
                return $options->repeat === null
                    && $options->repeatDays === [6, 7];
            });

        $this->service->schedule($habit);
    }

    public function test_schedule_uses_notification_options_dto(): void
    {
        $habit = Habit::factory()->create();

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(fn (NotificationOptions $options): bool => true);

        $this->service->schedule($habit);
    }

    public function test_schedule_uses_notification_action_dtos(): void
    {
        $habit = Habit::factory()->create();

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(function (NotificationOptions $options): bool {
                return count($options->actions) === 3
                    && $options->actions[0] instanceof NotificationAction
                    && $options->actions[0]->id === 'done'
                    && $options->actions[1] instanceof NotificationAction
                    && $options->actions[1]->id === 'skip'
                    && $options->actions[2] instanceof NotificationAction
                    && $options->actions[2]->id === 'snooze';
            });

        $this->service->schedule($habit);
    }

    public function test_schedule_includes_badge_count(): void
    {
        $habit = Habit::factory()->create();

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(fn (NotificationOptions $options): bool => $options->badge !== null);

        $this->service->schedule($habit);
    }

    public function test_schedule_includes_big_text_when_description_set(): void
    {
        $habit = Habit::factory()->create(['description' => 'Run for 30 minutes']);

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(function (NotificationOptions $options): bool {
                return $options->bigText !== null
                    && str_contains($options->bigText, 'Run for 30 minutes');
            });

        $this->service->schedule($habit);
    }

    public function test_schedule_big_text_includes_starter_message_when_no_streak(): void
    {
        $habit = Habit::factory()->create(['description' => null]);

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(function (NotificationOptions $options): bool {
                return $options->bigText !== null
                    && str_contains($options->bigText, 'Complete today to start your streak!');
            });

        $this->service->schedule($habit);
    }

    public function test_test_mode_uses_delay_not_at(): void
    {
        $habit = Habit::factory()->create();

        LocalNotifications::shouldReceive('cancel')->once();
        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(function (NotificationOptions $options): bool {
                return $options->delay === 15
                    && $options->at === null
                    && $options->repeat === null
                    && $options->repeatDays === null
                    && $options->badge !== null;
            });

        $this->service->schedule($habit, testMode: true);
    }

    public function test_snooze_uses_dto_with_delay(): void
    {
        $habit = Habit::factory()->create();

        LocalNotifications::shouldReceive('schedule')->once()
            ->withArgs(function (NotificationOptions $options): bool {
                return str_contains($options->id, '-snooze')
                    && $options->delay === 600
                    && $options->body === 'Snoozed reminder';
            });

        $this->service->snooze($habit);
    }

    public function test_cancel_calls_cancel_with_correct_id(): void
    {
        $habit = Habit::factory()->create();

        LocalNotifications::shouldReceive('cancel')->once()
            ->with('habit-'.$habit->id);

        $this->service->cancel($habit);
    }

    public function test_incomplete_count_today(): void
    {
        Habit::factory()->create(['frequency' => 'daily']);
        Habit::factory()->create(['frequency' => 'daily']);
        $completed = Habit::factory()->create(['frequency' => 'daily']);
        $completed->completions()->create(['completed_at' => today()]);

        $this->assertSame(2, Habit::incompleteCountToday());
    }

    public function test_incomplete_count_excludes_inactive_habits(): void
    {
        Habit::factory()->create(['is_active' => true]);
        Habit::factory()->create(['is_active' => false]);

        $this->assertSame(1, Habit::incompleteCountToday());
    }
}
