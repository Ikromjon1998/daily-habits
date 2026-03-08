# Epic 6: Completion Tracking & Streaks

**Status:** Not Started

## Description

Add streak calculation logic, a weekly calendar view, and motivational feedback for maintaining habits.

## Scope

- Calculate streaks correctly (consecutive days of completion)
- Show a mini weekly calendar on the Today screen (Mon-Sun dots, filled for completed days)
- Add a "streak milestone" celebration when hitting 7, 30, 100 day streaks
- Show total completions and longest streak on Settings/Stats screen
- Color-code habits based on streak length (warming colors for longer streaks)

## Files to Create/Modify

- `app/Models/Habit.php` (update streak logic)
- `app/Livewire/Today.php` (update with calendar)
- `resources/views/livewire/today.blade.php` (update)
- `app/Livewire/Settings.php` (add stats)

## Acceptance Criteria

- [ ] Streaks calculate correctly across consecutive days
- [ ] Weekly calendar shows completion dots
- [ ] Streak milestones trigger visual feedback
- [ ] Stats display on settings screen
