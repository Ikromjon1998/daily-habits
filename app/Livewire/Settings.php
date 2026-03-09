<?php

namespace App\Livewire;

use App\Models\Habit;
use App\Models\HabitCompletion;
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
        // Test with exact delay
        LocalNotifications::schedule([
            'id' => 'test-notification',
            'title' => 'Test Notification',
            'body' => 'If you see this, notifications are working!',
            'delay' => 10,
            'sound' => true,
        ]);
    }

    public function sendImmediateNotification(): void
    {
        // Test with immediate notification
        LocalNotifications::schedule([
            'id' => 'immediate-test',
            'title' => 'Immediate Test',
            'body' => 'This should appear NOW!',
            'delay' => 2,
            'sound' => true,
        ]);
    }

    public function rescheduleAllHabitsTest(): void
    {
        $habits = Habit::query()->where('is_active', true)->get();

        // First, test a simple notification
        LocalNotifications::schedule([
            'id' => 'simple-test-1',
            'title' => 'Simple Test 1',
            'body' => 'Testing without actions',
            'delay' => 10,
            'sound' => true,
        ]);

        // Now test with actions (like habits)
        LocalNotifications::schedule([
            'id' => 'actions-test-1',
            'title' => 'Actions Test',
            'body' => 'Testing with actions',
            'delay' => 15,
            'sound' => true,
            'actions' => [
                ['id' => 'done', 'title' => 'Done'],
                ['id' => 'snooze', 'title' => 'Snooze'],
            ],
        ]);

        // Now test with repeat
        LocalNotifications::schedule([
            'id' => 'repeat-test-1',
            'title' => 'Repeat Test',
            'body' => 'Testing with repeat',
            'delay' => 20,
            'sound' => true,
            'repeat' => 'daily',
        ]);
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
