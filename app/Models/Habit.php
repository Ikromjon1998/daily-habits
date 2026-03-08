<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Habit extends Model
{
    protected $fillable = [
        'name',
        'description',
        'emoji',
        'reminder_time',
        'frequency',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function completions(): HasMany
    {
        return $this->hasMany(HabitCompletion::class);
    }

    public function isCompletedToday(): bool
    {
        return $this->completions()
            ->whereDate('completed_at', Carbon::today())
            ->exists();
    }

    public function isCompletedOn(string $date): bool
    {
        return $this->completions()
            ->whereDate('completed_at', $date)
            ->exists();
    }

    public function toggleToday(): bool
    {
        $today = Carbon::today()->toDateString();

        $existing = $this->completions()
            ->whereDate('completed_at', $today)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        $this->completions()->create(['completed_at' => $today]);
        return true;
    }

    public function currentStreak(): int
    {
        $streak = 0;
        $date = Carbon::today();

        if (! $this->isCompletedOn($date->toDateString())) {
            $date = $date->subDay();
        }

        while ($this->isCompletedOn($date->toDateString())) {
            $streak++;
            $date = $date->subDay();
        }

        return $streak;
    }

    public function longestStreak(): int
    {
        $completions = $this->completions()
            ->orderBy('completed_at')
            ->pluck('completed_at')
            ->map(fn ($date) => Carbon::parse($date));

        if ($completions->isEmpty()) {
            return 0;
        }

        $longest = 1;
        $current = 1;

        for ($i = 1; $i < $completions->count(); $i++) {
            if (abs((int) $completions[$i]->diffInDays($completions[$i - 1])) === 1) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 1;
            }
        }

        return $longest;
    }

    public function shouldShowToday(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $dayOfWeek = Carbon::today()->dayOfWeek;

        return match ($this->frequency) {
            'weekdays' => $dayOfWeek >= 1 && $dayOfWeek <= 5,
            'weekends' => $dayOfWeek === 0 || $dayOfWeek === 6,
            default => true,
        };
    }

    public function totalCompletions(): int
    {
        return $this->completions()->count();
    }
}
