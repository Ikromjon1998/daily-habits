<?php

namespace App\Livewire;

use App\Models\Habit;
use App\Services\HabitNotificationService;
use Ikromjon\LocalNotifications\Events\NotificationActionPressed;
use Ikromjon\LocalNotifications\Events\NotificationTapped;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;

class Today extends Component
{
    public string $tapBanner = '';

    public function dismissBanner(): void
    {
        $this->tapBanner = '';
    }

    public function toggleHabit(int $habitId): void
    {
        $habit = Habit::findOrFail($habitId);
        $habit->toggleToday();
    }

    /** @param  array<string, mixed>  $data */
    #[OnNative(NotificationTapped::class)]
    public function onNotificationTapped(string $id = '', string $title = '', string $body = '', array $data = []): void
    {
        $this->tapBanner = "Tapped: {$title} (id: {$id})";
        logger()->info('NotificationTapped event received', ['id' => $id, 'title' => $title, 'body' => $body, 'data' => $data]);
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
        } elseif ($actionId === 'skip') {
            logger()->info('Habit skipped via notification', ['habit_id' => $habit->id]);
        } elseif ($actionId === 'snooze') {
            app(HabitNotificationService::class)->snooze($habit);
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
