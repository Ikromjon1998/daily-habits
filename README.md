# Daily Habits

[![CI](https://github.com/Ikromjon1998/daily-habits/actions/workflows/ci.yml/badge.svg)](https://github.com/Ikromjon1998/daily-habits/actions/workflows/ci.yml)
[![License](https://img.shields.io/github/license/Ikromjon1998/daily-habits)](LICENSE)

A mobile daily habits tracker built with Laravel, Livewire, and NativePHP Mobile. Runs natively on Android and iOS — no server required, everything works offline.

This project is open source and serves as a real-world example of building a native mobile app with the Laravel/PHP ecosystem. It is also the primary test app for NativePHP Mobile notification plugins — cold-start tap events, warm-start detection, action buttons, and Livewire 4 SPA compatibility are all tested here on real devices.

## Screenshots

<p align="center">
  <img src="screenshots/today.jpeg" width="280" alt="Today Screen" />
  &nbsp;&nbsp;
  <img src="screenshots/settings.jpeg" width="280" alt="Settings Screen" />
</p>

## Features

- Create daily habits with emoji icons, descriptions, and reminder times
- Frequency options: daily, weekdays, or weekends
- Native push notifications with daily repeating reminders
- Mark habits complete directly from notification action buttons (Done / Snooze)
- Track completion streaks with color-coded visual feedback
- Weekly calendar dots (Mon–Sun) on each habit card
- Streak milestone banners (7-day, 30-day, 100-day)
- Progress ring showing daily completion percentage
- Auto-refreshing UI — habits, progress, and stats update every 30 seconds
- Live clock on the Today screen (Alpine.js, no server round-trips)
- Automatic device timezone detection with configurable fallback
- Settings screen with notification permission management, test notification, and statistics
- Dark theme optimized for mobile
- Works completely offline — SQLite database, no API, no authentication

## Tech Stack

- **PHP 8.4** / **Laravel 12** / **Livewire 4**
- **NativePHP Mobile v3** — native Android & iOS builds from a single Laravel codebase
- **Tailwind CSS 4** — dark theme with safe area inset support
- **Alpine.js** — lightweight client-side interactivity (bundled with Livewire)
- **SQLite** — local on-device database
- [`ikromjon/nativephp-mobile-local-notifications`](https://github.com/Ikromjon1998/nativephp-mobile-local-notifications) v1.4.1 — local notification scheduling with repeating intervals, action buttons, cold-start tap events, and rich content

## How It Works

### Timezone Detection

The app detects the device's timezone via JavaScript and stores it in a cookie. The `ApplyDeviceTimezone` middleware reads the cookie on each request and overrides the application timezone. If the cookie is missing or invalid, the configured default (`Europe/Berlin`) is used.

### Notifications

Notifications are scheduled using calendar-based `at` timestamps (Unix epoch) with `RepeatInterval::Daily`. This ensures the native platform (iOS calendar triggers / Android alarm manager) fires at the exact clock time — preventing drift that accumulates with delay-based scheduling.

```php
// app/Providers/NativeServiceProvider.php
use Native\Mobile\Facades\System;

public function boot(): void
{
    System::enablePlugins([
        \Ikromjon\LocalNotifications\LocalNotificationsServiceProvider::class,
    ]);
}
```

```php
// Scheduling a daily repeating notification
use Ikromjon\LocalNotifications\Facades\LocalNotifications;
use Ikromjon\LocalNotifications\Enums\RepeatInterval;

LocalNotifications::schedule([
    'id' => 'habit-1',
    'title' => 'Time to Meditate',
    'body' => 'Your 10-minute session is waiting',
    'at' => now()->setTime(7, 0)->timestamp,
    'repeat' => RepeatInterval::Daily,
    'sound' => true,
    'actions' => [
        ['id' => 'done', 'title' => 'Done'],
        ['id' => 'snooze', 'title' => 'Snooze'],
    ],
]);
```

### Live UI Updates

- **Clock**: Alpine.js updates the displayed time every 10 seconds (client-side only).
- **Habit data**: `wire:poll.30s` refreshes the Today and Settings screens every 30 seconds. Since NativePHP runs a local server, polling has zero network latency and negligible overhead. Livewire automatically throttles polling by 95% when the app is backgrounded.

## Installation

```bash
git clone https://github.com/Ikromjon1998/daily-habits.git
cd daily-habits
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

## Running

**On device (native build):**

```bash
php artisan native:run android
# or
php artisan native:run ios
```

> Notifications require a native build — they do not work with `php artisan native:jump`.

**For local development:**

```bash
composer run dev
```

## Quality Gates

```bash
composer lint          # Format with Pint
composer analyse       # PHPStan level 8
composer rector:check  # Rector dry-run
composer test          # Run test suite (44 tests)
```

## Project Structure

```
app/
  Http/Middleware/    ApplyDeviceTimezone.php
  Livewire/          Today.php, Settings.php, HabitForm.php
  Models/            Habit.php, HabitCompletion.php
  Services/          HabitNotificationService.php
  Providers/         NativeServiceProvider.php
bootstrap/
  app.php            Middleware registration
database/
  factories/         HabitFactory.php
  migrations/        habits, habit_completions
  seeders/           HabitSeeder.php
resources/
  css/app.css        Tailwind @theme + custom animations
  views/
    layouts/         Base layout with bottom nav, safe areas, timezone detection
    livewire/        Component views (today, settings, habit-form)
plan/                Epic documents (development roadmap)
tests/
  Feature/           HabitFormTest, TodayTest, PageTest
```

## Requirements

- PHP 8.3+
- Node.js 18+
- NativePHP Mobile v3+
- Android API 33+ / iOS 18.2+

## Contributing

Contributions are welcome! Whether it's a bug fix, new feature, or improvement — feel free to open an issue or submit a pull request. Please read [CONTRIBUTING.md](CONTRIBUTING.md) before getting started.

If you'd like to use this project as a starting point for your own app, feel free to fork it.

## License

MIT
