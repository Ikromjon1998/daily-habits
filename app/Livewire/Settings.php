<?php

namespace App\Livewire;

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Services\HabitNotificationService;
use Ikromjon\LocalNotifications\Events\PermissionDenied;
use Ikromjon\LocalNotifications\Events\PermissionGranted;
use Ikromjon\LocalNotifications\Facades\LocalNotifications;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;

class Settings extends Component
{
    public string $permissionStatus = 'unknown';

    public function mount(): void
    {
        $this->checkPermission();
    }

    public function checkPermission(): void
    {
        $result = LocalNotifications::checkPermission();
        $this->permissionStatus = $result['status'] ?? 'unknown';
    }

    public function requestPermission(): void
    {
        LocalNotifications::requestPermission();
    }

    public function sendTestNotification(): void
    {
        $habit = Habit::query()->where('is_active', true)->first();

        if ($habit) {
            app(HabitNotificationService::class)->schedule($habit, testMode: true);
        } else {
            LocalNotifications::schedule([
                'id' => 'test-notification',
                'title' => 'Test Notification',
                'body' => 'If you see this, notifications are working!',
                'delay' => 5,
                'sound' => true,
            ]);
        }
    }

    #[OnNative(PermissionGranted::class)]
    public function onPermissionGranted(): void
    {
        $this->permissionStatus = 'granted';
    }

    #[OnNative(PermissionDenied::class)]
    public function onPermissionDenied(): void
    {
        $this->permissionStatus = 'denied';
    }

    public function render(): View
    {
        $totalHabits = Habit::query()->where('is_active', true)->count();
        $completionsToday = HabitCompletion::query()
            ->whereDate('completed_at', today())
            ->count();
        $longestStreak = Habit::query()
            ->where('is_active', true)
            ->get()
            ->max(fn (Habit $h): int => $h->longestStreak()) ?? 0;

        return view('livewire.settings', [
            'totalHabits' => $totalHabits,
            'completionsToday' => $completionsToday,
            'longestStreak' => $longestStreak,
        ]);
    }
}
