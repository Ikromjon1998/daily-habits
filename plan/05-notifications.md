# Epic 5: Notification Reminders

**Status:** Done

## Description

Integrate `ikromjon/nativephp-mobile-local-notifications` to schedule daily reminders for each habit with action buttons to complete directly from the notification.

## Scope

- Request notification permission on first app launch (Settings screen)
- Show permission status on Settings screen
- When a habit is created/updated, schedule a daily repeating notification:
  - Title: habit emoji + name
  - Body: habit description or motivational message
  - Action buttons: "Done" and "Snooze"
  - Rich content with subtitle showing streak count
- When a habit is deleted, cancel its notification
- Handle `NotificationActionPressed` event:
  - "Done" action: mark habit as completed for today
  - "Snooze" action: reschedule for 10 minutes later
- Handle `NotificationTapped` event: navigate to the app
- Show notification status per habit on the Today screen

## Files to Create/Modify

- `app/Livewire/Settings.php` (update)
- `resources/views/livewire/settings.blade.php` (update)
- `app/Livewire/Today.php` (update for action handling)
- `app/Livewire/HabitForm.php` (update for scheduling)

## Acceptance Criteria

- [ ] Permission request works on both platforms
- [ ] Notifications schedule correctly when habits are created
- [ ] "Done" action button marks habit complete
- [ ] Notifications cancel when habits are deleted
- [ ] Settings screen shows permission status
