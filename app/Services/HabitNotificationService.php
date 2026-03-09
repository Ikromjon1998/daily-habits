<?php

namespace App\Services;

use App\Models\Habit;
use Ikromjon\LocalNotifications\Enums\RepeatInterval;
use Ikromjon\LocalNotifications\Facades\LocalNotifications;

class HabitNotificationService
{
    public function schedule(Habit $habit, bool $testMode = false): void
    {
        $notificationId = $this->notificationId($habit);

        // Cancel existing notification first
        LocalNotifications::cancel($notificationId);

        $body = $habit->description ?? 'Time to build your streak!';
        $streak = $habit->currentStreak();

        if ($testMode) {
            LocalNotifications::schedule([
                'id' => $notificationId,
                'title' => $habit->emoji.' '.$habit->name,
                'body' => $body,
                'subtitle' => $streak > 0 ? "Streak: {$streak} days" : 'Start your streak today!',
                'delay' => 15,
                'sound' => true,
                'data' => ['habit_id' => $habit->id],
                'actions' => [
                    ['id' => 'done', 'title' => 'Done'],
                    ['id' => 'snooze', 'title' => 'Snooze'],
                ],
            ]);

            return;
        }

        LocalNotifications::schedule([
            'id' => $notificationId,
            'title' => $habit->emoji.' '.$habit->name,
            'body' => $body,
            'subtitle' => $streak > 0 ? "Streak: {$streak} days" : 'Start your streak today!',
            'at' => $this->calculateTimestamp($habit),
            'repeat' => RepeatInterval::Daily,
            'sound' => true,
            'data' => ['habit_id' => $habit->id],
            'actions' => [
                ['id' => 'done', 'title' => 'Done'],
                ['id' => 'snooze', 'title' => 'Snooze'],
            ],
        ]);
    }

    public function cancel(Habit $habit): void
    {
        LocalNotifications::cancel($this->notificationId($habit));
    }

    public function snooze(Habit $habit): void
    {
        $snoozeId = $this->notificationId($habit).'-snooze';

        LocalNotifications::schedule([
            'id' => $snoozeId,
            'title' => $habit->emoji.' '.$habit->name,
            'body' => 'Snoozed reminder',
            'delay' => 600, // 10 minutes
            'sound' => true,
            'data' => ['habit_id' => $habit->id],
            'actions' => [
                ['id' => 'done', 'title' => 'Done'],
                ['id' => 'snooze', 'title' => 'Snooze'],
            ],
        ]);
    }

    private function notificationId(Habit $habit): string
    {
        return 'habit-'.$habit->id;
    }

    private function calculateTimestamp(Habit $habit): int
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
