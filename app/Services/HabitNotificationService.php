<?php

namespace App\Services;

use App\Models\Habit;
use Ikromjon\LocalNotifications\Facades\LocalNotifications;

class HabitNotificationService
{
    public function schedule(Habit $habit): void
    {
        $notificationId = $this->notificationId($habit);

        // Cancel existing notification first
        LocalNotifications::cancel($notificationId);

        $body = $habit->description ?? 'Time to build your streak!';
        $streak = $habit->currentStreak();

        LocalNotifications::schedule([
            'id' => $notificationId,
            'title' => $habit->emoji.' '.$habit->name,
            'body' => $body,
            'subtitle' => $streak > 0 ? "Streak: {$streak} days" : 'Start your streak today!',
            'delay' => $this->calculateDelay($habit),
            'repeat' => 'daily',
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

    private function calculateDelay(Habit $habit): int
    {
        $parts = explode(':', $habit->reminder_time);
        $hour = (int) $parts[0];
        $minute = (int) ($parts[1] ?? 0);

        $now = now();
        $target = $now->copy()->setTime($hour, $minute, 0);

        // If the target time has passed today, schedule for tomorrow
        if ($target->lte($now)) {
            $target->addDay();
        }

        return (int) $now->diffInSeconds($target);
    }
}
