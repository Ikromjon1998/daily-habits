# Daily Habits

[![CI](https://github.com/Ikromjon1998/daily-habits/actions/workflows/ci.yml/badge.svg)](https://github.com/Ikromjon1998/daily-habits/actions/workflows/ci.yml)
[![License](https://img.shields.io/github/license/Ikromjon1998/daily-habits)](LICENSE)

A mobile-first daily habits tracker built with Laravel, Livewire, and [NativePHP Mobile](https://nativephp.com). Track your habits, maintain streaks, and get native push notifications — all running locally on your device.

## Features

- Create daily habits with emoji icons and reminder times
- Native local notifications that work offline (no server or Firebase needed)
- Mark habits complete directly from notification action buttons
- Streak tracking with visual progress indicators
- Clean, dark-themed mobile UI with smooth animations
- Survives device reboot on Android

## Tech Stack

- **PHP 8.2+** / **Laravel 12** / **Livewire 4**
- **NativePHP Mobile v3** — native iOS & Android builds
- **[nativephp-mobile-local-notifications](https://github.com/Ikromjon1998/nativephp-mobile-local-notifications)** v1.1.1 — on-device notification scheduling
- **Tailwind CSS 4** — utility-first styling
- **SQLite** — local database, no server required

## Plugin Integration Example

This app demonstrates how to integrate the `ikromjon/nativephp-mobile-local-notifications` plugin in a real NativePHP Mobile app:

```php
use Ikromjon\LocalNotifications\Facades\LocalNotifications;
use Ikromjon\LocalNotifications\Enums\RepeatInterval;

// Schedule a daily repeating notification
LocalNotifications::schedule([
    'id' => 'habit-meditation',
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

See `app/Services/HabitNotificationService.php` for the full implementation.

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 20+
- Android Studio or Xcode (for native builds)

### Setup

```bash
git clone https://github.com/Ikromjon1998/daily-habits.git
cd daily-habits
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

### Run on device

```bash
php artisan native:run android
# or
php artisan native:run ios
```

> Note: Notifications require a native build — they do not work with `php artisan native:run` in Jump mode.

## Quality Tools

```bash
composer lint          # Format with Pint
composer analyse       # PHPStan level 8
composer rector:check  # Rector dry-run
composer test          # Run test suite
```

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

You're also free to fork this repository and use it as a starter for your own app.

## Requirements

- PHP 8.2+ (Laravel 12 + Symfony 7)
- NativePHP Mobile v3
- iOS 18.2+ / Android API 33+

## License

MIT
