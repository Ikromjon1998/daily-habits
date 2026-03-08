# Epic 3: Home Screen - Today's Habits

**Status:** Not Started

## Description

Build the main "Today" screen showing the user's habits for today with completion toggles, streak counters, and a progress ring.

## Scope

- Show a greeting header with today's date
- Display a progress ring/bar showing X of Y habits completed today
- List each habit as a card with:
  - Emoji icon
  - Habit name and description
  - Reminder time
  - Current streak (flame icon + count)
  - Tap to toggle completion (with animation)
- Empty state when no habits exist (with CTA to add first habit)
- Pull-to-refresh or auto-refresh on completion

## Files to Create/Modify

- `app/Livewire/Today.php` (update)
- `resources/views/livewire/today.blade.php` (update)

## Acceptance Criteria

- [ ] Today screen shows all active habits for the current day
- [ ] Habits can be marked complete/incomplete by tapping
- [ ] Progress indicator updates in real-time
- [ ] Streak count displays correctly
- [ ] Empty state is clean and inviting
