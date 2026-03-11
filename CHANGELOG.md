# Changelog

All notable changes to Daily Habits are documented in this file.

## [Unreleased]

### Added

- **Device timezone detection** — The app now detects the device's timezone via JavaScript (`Intl.DateTimeFormat`) and applies it server-side through the `ApplyDeviceTimezone` middleware. The configured timezone (`Europe/Berlin`) is used as a fallback when the device timezone is unavailable.
- **Live clock on Today screen** — The header time now updates in real-time using Alpine.js instead of showing a static server-rendered timestamp.
- **Auto-refreshing UI** — Both the Today and Settings screens use `wire:poll.30s` to keep data (habits, progress, streaks, permission status) up to date without manual navigation.

### Changed

- The `device_timezone` cookie is excluded from Laravel's cookie encryption since it is set by client-side JavaScript.

### Files Added

- `app/Http/Middleware/ApplyDeviceTimezone.php`
- `CHANGELOG.md`

### Files Modified

- `bootstrap/app.php` — Registered `ApplyDeviceTimezone` middleware and cookie encryption exception.
- `resources/views/layouts/app.blade.php` — Added timezone cookie script.
- `resources/views/livewire/today.blade.php` — Added Alpine.js clock and `wire:poll.30s`.
- `resources/views/livewire/settings.blade.php` — Added `wire:poll.30s`.

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
