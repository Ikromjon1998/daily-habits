# Changelog

All notable changes to Daily Habits are documented in this file.

## [1.4.0] - 2026-04-05

### Added

- **Laravel Notification channel integration** — Schedule notifications via Laravel's standard `$notifiable->notify()` pattern using `LocalNotificationChannel` and `LocalNotificationMessage`.
- **`DebugLocalNotification`** — Working example notification class in `app/Notifications/` demonstrating the channel integration.
- **Action buttons on habit notifications** — Done, Skip (destructive), and Snooze buttons on all habit reminders.
- **Notification Debug panel** — 7 test scenarios covering warm/cold start, action buttons (regular, destructive, text input), content/timing updates, schedule/cancel operations, and Laravel Notification channel.
- **Real-time event logging** — All notification events (Scheduled, Received, Tapped, ActionPressed, Updated) logged with timestamps in the debug panel.

### Changed

- Upgraded `ikromjon/nativephp-mobile-local-notifications` from `^1.6` to `^1.7`.
- Fixed `onActionPressed` handler to parse positional event data correctly (`[notificationId, actionId]` instead of associative array).

## [1.3.0] - 2026-03-11

### Added

- **Device timezone detection** — Detects the device's timezone via JavaScript (`Intl.DateTimeFormat`) and applies it server-side through the `ApplyDeviceTimezone` middleware. Configured timezone (`Europe/Berlin`) used as fallback.
- **Live clock on Today screen** — Header time updates in real-time using Alpine.js.
- **Time-based greeting** — "Good morning", "Good afternoon", or "Good evening" based on current time.
- **Auto-refreshing UI** — Today and Settings screens use `wire:poll.30s` for live data.
- **Page entrance animations** — All screens fade in with slide-up animation.
- **Staggered list animations** — Habit cards animate in sequentially.
- **Completion glow effect** — Toggle circle pulses with emerald glow on completion.
- **Celebration shimmer** — Progress card shimmers when all habits completed (100%).
- **FAB entrance animation** — Floating action button bounces in with spring animation.
- **Frequency-aware notifications** — Weekday habits only fire Mon–Fri (`repeatDays: [1,2,3,4,5]`), weekend habits only Sat–Sun (`repeatDays: [6,7]`). Previously all habits fired daily regardless of frequency.
- **iOS badge count** — App icon badge shows number of incomplete habits for today.
- **Expanded notification content** — `bigText` shows description + streak motivation when expanded. Always includes motivational text, even at 0 streak.
- **Type-safe notification DTOs** — All scheduling uses `NotificationOptions` and `NotificationAction` DTOs.
- **`Habit::incompleteCountToday()`** — Static helper for counting remaining habits.
- **13 new notification tests** — Full coverage for frequency scheduling, DTOs, badge, bigText, snooze, and cancel (57 tests total).

### Changed

- Notification body now shows streak motivation (collapsed view) instead of the habit description.
- Notification subtitle shows frequency label ("Daily"/"Weekdays"/"Weekends") at 0 streak instead of "Start your streak today!".
- Test notification in Settings now uses `HabitNotificationService` for full end-to-end testing with DTOs, badge, and bigText.
- Upgraded `ikromjon/nativephp-mobile-local-notifications` from `^1.1` to `^1.2`.
- Improved completion checkmark animation with spring physics.
- Habit card tap feedback more responsive (scale 0.93 vs 0.97).
- Completed habits dim their emoji for clearer visual distinction.
- Stats in Settings use color coding (emerald for completions, orange for streaks).

## [1.1.1] - 2026-03-09

### Added

- Calendar-based `at` scheduling for notifications (prevents drift vs delay-based).
- Bumped `ikromjon/nativephp-mobile-local-notifications` to v1.2.0.

## [1.1.0] - 2026-03-09

### Added

- PHPUnit test suite with 44 tests covering habits, completions, streaks, and pages.
- `HabitFactory` for test data generation.
- GitHub Actions CI workflow (`ci.yml`) for automated testing and static analysis.

## [1.0.0] - 2026-03-08

### Added

- Initial release of Daily Habits.
- Create, edit, and delete daily habits with emoji icons.
- Frequency options: daily, weekdays, weekends.
- Native push notifications with daily repeating reminders (via `ikromjon/nativephp-mobile-local-notifications`).
- Notification action buttons: "Done" (marks complete) and "Snooze" (10-minute delay).
- Completion tracking with streak calculation.
- Weekly calendar dots (Mon–Sun) on each habit card.
- Streak milestones with visual banners (7-day, 30-day, 100-day).
- Progress ring showing daily completion percentage.
- Settings screen with notification permission management, test notification, and statistics.
- Dark theme optimized for mobile with safe area inset support.
- Empty state with call-to-action for new users.
- SQLite database — fully offline, no authentication, no API.
