<?php

namespace App\Livewire;

use App\Models\Habit;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Today extends Component
{
    public function toggleHabit(int $habitId): void
    {
        $habit = Habit::findOrFail($habitId);
        $habit->toggleToday();
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
