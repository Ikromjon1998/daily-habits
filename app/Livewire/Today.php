<?php

namespace App\Livewire;

use App\Models\Habit;
use App\Services\HabitNotificationService;
use Ikromjon\LocalNotifications\Events\NotificationActionPressed;
use Ikromjon\LocalNotifications\Events\NotificationReceived;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;

class Today extends Component
{
    /** @var Collection<int, array{name: string, reminder_time: string, next_notification: string, delay_seconds: int}> */
    public array $notificationDebug = [];

    public function mount(): void
    {
        $this->rescheduleAllNotifications();
        $this->loadNotificationDebug();
    }

    private function loadNotificationDebug(): void
    {
        $habits = Habit::query()->where('is_active', true)->get();
        $now = now();

        $this->notificationDebug = $habits->map(function (Habit $habit) use ($now) {
            $parts = explode(':', $habit->reminder_time);
            $hour = (int) $parts[0];
            $minute = (int) ($parts[1] ?? 0);
            $target = $now->copy()->setTime($hour, $minute, 0);

            if ($target->lte($now)) {
                $target->addDay();
            }

            return [
                'name' => $habit->name,
                'reminder_time' => $habit->reminder_time,
                'next_notification' => $target->format('Y-m-d H:i:s'),
                'delay_seconds' => (int) $now->diffInSeconds($target),
            ];
        })->toArray();
    }

    public function rescheduleAllNotifications(): void
    {
        $habits = Habit::query()->where('is_active', true)->get();
        $notificationService = app(HabitNotificationService::class);

        foreach ($habits as $habit) {
            $notificationService->schedule($habit);
        }
    }

    public function toggleHabit(int $habitId): void
    {
        $habit = Habit::findOrFail($habitId);
        $habit->toggleToday();
    }

    /** @param  array{notificationId?: string, actionId?: string}  $data */
    #[OnNative(NotificationActionPressed::class)]
    public function onActionPressed(array $data = []): void
    {
        $notificationId = $data['notificationId'] ?? '';
        $actionId = $data['actionId'] ?? '';

        // Extract habit ID from notification ID (format: "habit-{id}")
        if (! str_starts_with($notificationId, 'habit-')) {
            return;
        }

        $habitId = (int) str_replace(['habit-', '-snooze'], '', $notificationId);
        $habit = Habit::find($habitId);

        if (! $habit) {
            return;
        }

        if ($actionId === 'done') {
            if (! $habit->isCompletedToday()) {
                $habit->toggleToday();
            }
        } elseif ($actionId === 'snooze') {
            app(HabitNotificationService::class)->snooze($habit);
        }
    }

    /** Reschedule habit notification after it fires (since repeat is buggy) */
    #[OnNative(NotificationReceived::class)]
    public function onNotificationReceived(array $data = []): void
    {
        $notificationId = $data['id'] ?? '';

        // Only reschedule habit notifications
        if (! str_starts_with($notificationId, 'habit-')) {
            return;
        }

        $habitId = (int) str_replace('habit-', '', $notificationId);
        $habit = Habit::find($habitId);

        if ($habit) {
            app(HabitNotificationService::class)->schedule($habit);
        }
    }

    public function render(): View
    {
        /** @var Collection<int, Habit> $allHabits */
        $allHabits = Habit::query()
            ->where('is_active', true)
            ->orderBy('reminder_time')
            ->get();

        $habits = $allHabits->filter(fn (Habit $habit): bool => $habit->shouldShowToday());

        $completed = $habits->filter(fn (Habit $habit): bool => $habit->isCompletedToday())->count();
        $total = $habits->count();
        $percentage = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        return view('livewire.today', [
            'habits' => $habits,
            'completed' => $completed,
            'total' => $total,
            'percentage' => $percentage,
        ])->layout('layouts.app');
    }
}
