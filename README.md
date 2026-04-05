# Daily Habits

[![CI](https://github.com/Ikromjon1998/daily-habits/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/Ikromjon1998/daily-habits/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![Release](https://img.shields.io/github/v/release/Ikromjon1998/daily-habits)](https://github.com/Ikromjon1998/daily-habits/releases)
[![Notification Plugin](https://img.shields.io/badge/local--notifications-v1.9.0-ff6f00)](https://github.com/Ikromjon1998/nativephp-mobile-local-notifications)

A mobile daily habits tracker built with Laravel, Livewire, and NativePHP Mobile. Runs natively on Android and iOS — no server required, everything works offline.

This project is open source and serves as a real-world example of building a native mobile app with the Laravel/PHP ecosystem. It is also the primary test app for the [`nativephp-mobile-local-notifications`](https://github.com/Ikromjon1998/nativephp-mobile-local-notifications) plugin — cold-start tap events, warm-start detection, action buttons, native snooze rescheduling, and Livewire 4 SPA compatibility are all tested here on real devices.

If you're interested in trying out the notification plugin, this app is a great place to start. Contributions and testing feedback are very welcome — see [Contributing](#contributing).

## Screenshots

<p align="center">
  <img src="screenshots/today.jpeg" width="280" alt="Today Screen" />
  &nbsp;&nbsp;
  <img src="screenshots/settings.jpeg" width="280" alt="Settings Screen" />
</p>

## Features

- Create daily habits with emoji icons, descriptions, and reminder times
- Frequency options: daily, weekdays, or weekends
- Custom notification sounds — pick from bundled presets or add your own to `resources/sounds/`
- Native push notifications with daily repeating reminders
- Mark habits complete directly from notification action buttons (Done / Skip / Snooze)
- Laravel Notification channel integration — schedule via `$notifiable->notify()`
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

- **PHP 8.3+** / **Laravel 12** / **Livewire 4**
- **NativePHP Mobile v3** — native Android & iOS builds from a single Laravel codebase
- **Tailwind CSS 4** — dark theme with safe area inset support
- **Alpine.js** — lightweight client-side interactivity (bundled with Livewire)
- **SQLite** — local on-device database
- [`ikromjon/nativephp-mobile-local-notifications`](https://github.com/Ikromjon1998/nativephp-mobile-local-notifications) v1.9.0 — local notification scheduling with repeating intervals, action buttons, native snooze rescheduling, cold-start event delivery, rich content, custom sounds, and Laravel Notification channel integration

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

### Laravel Notification Channel

The app includes a working example of the Laravel Notification channel integration. Instead of calling `LocalNotifications::schedule()` directly, you can use Laravel's standard notification pattern:

```php
// app/Notifications/DebugLocalNotification.php
use Ikromjon\LocalNotifications\Notifications\HasLocalNotification;
use Ikromjon\LocalNotifications\Notifications\LocalNotificationChannel;
use Ikromjon\LocalNotifications\Notifications\LocalNotificationMessage;
use Illuminate\Notifications\Notification;

class DebugLocalNotification extends Notification implements HasLocalNotification
{
    public function via(object $notifiable): array
    {
        return [LocalNotificationChannel::class];
    }

    public function toLocalNotification(object $notifiable): LocalNotificationMessage
    {
        return LocalNotificationMessage::create()
            ->id('my-notification')
            ->title('Hello')
            ->body('Sent via Laravel Notification channel')
            ->delay(10)
            ->sound()
            ->action('ok', 'OK')
            ->action('cancel', 'Cancel', destructive: true)
            ->action('snooze', 'Snooze (5m)', snooze: 300);
    }
}

// Send it
(new AnonymousNotifiable)->notify(new DebugLocalNotification);
```

### Notification Debug Panel

Navigate to **Settings > Notification Debug** to access 9 test scenarios that verify the entire notification pipeline on a real device:

| # | Scenario | What it tests |
|---|----------|---------------|
| 1 | Warm Start (10s) | Schedule, receive, and tap while app is open |
| 2 | Cold Start (30s) | Tap detection after app is killed |
| 3a | Action Buttons — Warm (10s) | 3 action buttons (regular + destructive + native snooze) while app is open |
| 3b | Action Buttons — Cold (30s) | Same buttons after app is killed — snooze works natively even when app is dead |
| 3c | Text Input Action (10s) | Reply-style action with text input field |
| 4 | Update Content (60s) | Updating title/body while preserving timing |
| 5 | Update Timing (120s > 15s) | Rescheduling to fire sooner |
| 6 | Schedule + Cancel + GetPending | Instant schedule/cancel/list operations |
| 7 | Laravel Notification Channel (10s) | Full end-to-end via `$notifiable->notify()` |
| 8a | Custom Sound — Direct API (10s) | Custom sound via Facade with `soundName` parameter |
| 8b | Custom Sound — Laravel Channel (10s) | Custom sound via `->sound('alert.wav')` fluent builder |

All events are logged in real-time at the bottom of the debug panel.

### Custom Notification Sounds

Each habit can have its own notification sound. The sound picker in the habit form auto-discovers files from `resources/sounds/`:

```
resources/sounds/
  alert.wav       # Two-tone alert
  bell.wav        # Single bell ring
  chime.wav       # Ascending chime
  gentle.wav      # Soft two-note
  urgent.wav      # Rapid triple beep
```

**To add a custom sound:** Drop a `.wav`, `.mp3`, `.ogg`, `.caf`, or `.aiff` file into `resources/sounds/` — it will automatically appear in the habit form picker. For Android, also copy the file to `nativephp/android/app/src/main/res/raw/`.

> **Naming:** Use lowercase letters, digits, and underscores only (e.g. `my_sound.wav`). Hyphens and uppercase break Android resource lookup. See the [plugin docs](https://github.com/Ikromjon1998/nativephp-mobile-local-notifications/blob/main/docs/custom-sounds.md) for full details.

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
composer test          # Run test suite (57 tests)
```

## Project Structure

```
app/
  Http/Middleware/    ApplyDeviceTimezone.php
  Livewire/          Today.php, Settings.php, HabitForm.php, NotificationDebug.php
  Models/            Habit.php, HabitCompletion.php
  Notifications/     DebugLocalNotification.php (Laravel Notification channel example)
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
  sounds/            Notification sound presets (alert, bell, chime, gentle, urgent)
  views/
    layouts/         Base layout with bottom nav, safe areas, timezone detection
    livewire/        Component views (today, settings, habit-form, notification-debug)
plan/                Epic documents (development roadmap)
tests/
  Feature/           HabitFormTest, TodayTest, PageTest, HabitNotificationServiceTest
  Unit/              HabitTest
```

## Requirements

- PHP 8.3+
- Node.js 18+
- NativePHP Mobile v3+
- Android API 33+ / iOS 18.2+

## Contributing

Contributions are welcome! Whether it's a bug fix, new feature, or improvement — feel free to open an issue or submit a pull request. Please read [CONTRIBUTING.md](CONTRIBUTING.md) before getting started.

This project also serves as the primary test app for the [`nativephp-mobile-local-notifications`](https://github.com/Ikromjon1998/nativephp-mobile-local-notifications) plugin. If you have an Android or iOS device and want to help test notification features (action buttons, snooze, cold-start events, etc.), your feedback is especially valuable.

If you'd like to use this project as a starting point for your own app, feel free to fork it.

## License

MIT
