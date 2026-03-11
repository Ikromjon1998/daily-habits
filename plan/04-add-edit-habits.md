# Epic 4: Add & Edit Habits

**Status:** Done

## Description

Create screens to add new habits and edit existing ones, with emoji picker, time selector, and frequency options.

## Scope

- Create "Add Habit" screen accessible from a floating action button on Today screen
- Form fields:
  - Emoji picker (grid of common emojis for habits)
  - Name (text input)
  - Description (optional textarea)
  - Reminder time (time picker)
  - Frequency (daily / weekdays / weekends)
- Edit habit screen (same form, pre-filled)
- Delete habit with confirmation
- Swipe or long-press to access edit/delete on habit cards

## Files to Create/Modify

- `app/Livewire/HabitForm.php`
- `resources/views/livewire/habit-form.blade.php`
- `routes/web.php` (add route)
- Update Today component for FAB and edit actions

## Acceptance Criteria

- [x] Users can create a new habit with all fields
- [x] Users can edit an existing habit
- [x] Users can delete a habit
- [x] Form validation works (name required, etc.)
- [x] After save, user returns to Today screen
