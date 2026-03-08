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
        ])->layout('layouts.app');
    }
}
