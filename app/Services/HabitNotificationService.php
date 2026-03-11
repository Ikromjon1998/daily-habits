<?php

namespace App\Services;

use App\Models\Habit;
use Ikromjon\LocalNotifications\Data\NotificationAction;
use Ikromjon\LocalNotifications\Data\NotificationOptions;
use Ikromjon\LocalNotifications\Enums\RepeatInterval;
use Ikromjon\LocalNotifications\Facades\LocalNotifications;

class HabitNotificationService
{
    public function schedule(Habit $habit, bool $testMode = false): void
    {
        $notificationId = $this->notificationId($habit);

        LocalNotifications::cancel($notificationId);

        if ($testMode) {
            LocalNotifications::schedule(new NotificationOptions(
                id: $notificationId,
                title: $habit->emoji.' '.$habit->name,
                body: $this->buildBody($habit),
                delay: 15,
                sound: true,
                badge: Habit::incompleteCountToday(),
                subtitle: $this->buildSubtitle($habit),
                bigText: $this->buildBigText($habit),
                data: ['habit_id' => $habit->id],
                actions: $this->buildActions(),
            ));

            return;
        }

        LocalNotifications::schedule(new NotificationOptions(
            id: $notificationId,
            title: $habit->emoji.' '.$habit->name,
            body: $this->buildBody($habit),
            at: $this->calculateTargetTimestamp($habit),
            repeat: $this->shouldUseRepeatDaily($habit) ? RepeatInterval::Daily : null,
            repeatDays: $this->buildRepeatDays($habit),
            sound: true,
            badge: Habit::incompleteCountToday(),
            subtitle: $this->buildSubtitle($habit),
            bigText: $this->buildBigText($habit),
            data: ['habit_id' => $habit->id],
            actions: $this->buildActions(),
        ));
    }

    public function cancel(Habit $habit): void
    {
        LocalNotifications::cancel($this->notificationId($habit));
    }

    public function snooze(Habit $habit): void
    {
        LocalNotifications::schedule(new NotificationOptions(
            id: $this->notificationId($habit).'-snooze',
            title: $habit->emoji.' '.$habit->name,
            body: 'Snoozed reminder',
            delay: 600,
            sound: true,
            badge: Habit::incompleteCountToday(),
            data: ['habit_id' => $habit->id],
            actions: $this->buildActions(),
        ));
    }

    private function notificationId(Habit $habit): string
    {
        return 'habit-'.$habit->id;
    }

    /**
     * Build the collapsed notification body (short, one line).
     */
    private function buildBody(Habit $habit): string
    {
        $streak = $habit->currentStreak();

        if ($streak >= 30) {
            return "🔥 {$streak}-day streak — incredible dedication!";
        }

        if ($streak >= 7) {
            return "🔥 {$streak}-day streak — keep it going!";
        }

        if ($streak > 0) {
            return "🔥 {$streak}-day streak — don't break the chain!";
        }

        return 'Time to build your streak!';
    }

    /**
     * Build the subtitle with streak count or frequency.
     */
    private function buildSubtitle(Habit $habit): string
    {
        $streak = $habit->currentStreak();

        if ($streak > 0) {
            return "Streak: {$streak} days";
        }

        return match ($habit->frequency) {
            'weekdays' => 'Weekdays',
            'weekends' => 'Weekends',
            default => 'Daily',
        };
    }

    /**
     * Build the expanded notification text with description and streak info.
     */
    private function buildBigText(Habit $habit): string
    {
        $parts = [];

        if ($habit->description) {
            $parts[] = $habit->description;
        }

        $streak = $habit->currentStreak();

        if ($streak >= 30) {
            $parts[] = "🔥 {$streak}-day streak — incredible dedication!";
        } elseif ($streak >= 7) {
            $parts[] = "🔥 {$streak}-day streak — keep it going!";
        } elseif ($streak > 0) {
            $parts[] = "🔥 {$streak}-day streak — don't break the chain!";
        } else {
            $parts[] = 'Complete today to start your streak!';
        }

        return implode("\n", $parts);
    }

    /**
     * Build the standard action buttons.
     *
     * @return array<int, NotificationAction>
     */
    private function buildActions(): array
    {
        return [
            new NotificationAction(id: 'done', title: 'Done'),
            new NotificationAction(id: 'snooze', title: 'Snooze'),
        ];
    }

    /**
     * Whether to use RepeatInterval::Daily (only for 'daily' frequency).
     */
    private function shouldUseRepeatDaily(Habit $habit): bool
    {
        return $habit->frequency === 'daily';
    }

    /**
     * Build repeatDays array for weekday/weekend frequency.
     *
     * Uses ISO weekday format: 1=Monday through 7=Sunday.
     * Returns null for 'daily' frequency (uses RepeatInterval::Daily instead).
     *
     * @return array<int, int>|null
     */
    private function buildRepeatDays(Habit $habit): ?array
    {
        return match ($habit->frequency) {
            'weekdays' => [1, 2, 3, 4, 5],
            'weekends' => [6, 7],
            default => null,
        };
    }

    /**
     * Calculate the next occurrence as a Unix timestamp.
     *
     * Using `at` instead of `delay` ensures the native platform schedules
     * the notification at an exact clock time. With calendar-based triggers,
     * iOS and Android fire at the precise time — preventing drift that
     * accumulates with delay-based scheduling.
     */
    private function calculateTargetTimestamp(Habit $habit): int
    {
        $parts = explode(':', $habit->reminder_time);
        $hour = (int) $parts[0];
        $minute = (int) ($parts[1] ?? 0);

        $target = now()->setTime($hour, $minute, 0);

        if ($target->lt(now())) {
            $target->addDay();
        }

        return (int) $target->timestamp;
    }
}
