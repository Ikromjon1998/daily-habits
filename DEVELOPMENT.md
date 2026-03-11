# Development Guide

## Quick Start

```bash
composer install && npm install
php artisan migrate --seed
npm run build
composer run dev
```

## Composer Scripts

| Command | Description |
|---------|-------------|
| `composer lint` | Format code with Pint |
| `composer lint:check` | Check formatting without fixing |
| `composer analyse` | Run PHPStan level 8 |
| `composer rector` | Apply Rector refactoring |
| `composer rector:check` | Preview Rector changes (dry-run) |
| `composer quality` | Run lint + analyse |
| `composer test` | Run test suite |
| `composer dev` | Start all dev servers |

## Project Structure

```
app/
  Livewire/         # Page components (Today, Settings, HabitForm)
  Models/           # Habit, HabitCompletion
  Providers/
    NativeServiceProvider.php  # NativePHP plugin registration
database/
  migrations/
  seeders/          # HabitSeeder with 5 sample habits
resources/
  css/app.css       # Tailwind @theme + custom animations
  views/
    layouts/app.blade.php      # Base layout, bottom nav, safe areas
    livewire/                  # Component views
plan/               # Epic documents
```

## Notification Plugin API

```php
use Ikromjon\LocalNotifications\Facades\LocalNotifications;
use Ikromjon\LocalNotifications\Enums\RepeatInterval;

LocalNotifications::schedule([
    'id' => 'habit-1',
    'title' => 'Habit Reminder',
    'body' => 'Time to do your habit!',
    'subtitle' => 'Streak: 5 days',
    'at' => now()->setTime(9, 0)->timestamp,
    'repeat' => RepeatInterval::Daily,
    'sound' => true,
    'badge' => 1,
    'data' => ['habit_id' => 1],
    'image' => 'https://example.com/img.jpg',
    'bigText' => 'Extended text...',
    'actions' => [
        ['id' => 'done', 'title' => 'Done'],
        ['id' => 'snooze', 'title' => 'Snooze'],
    ],
]);

LocalNotifications::cancel('habit-1');
LocalNotifications::cancelAll();
LocalNotifications::getPending();
LocalNotifications::requestPermission();
LocalNotifications::checkPermission();
```

### Events (use with `#[OnNative(EventClass::class)]`)

| Event | Properties |
|-------|------------|
| `NotificationScheduled` | `id`, `title`, `body` |
| `NotificationReceived` | `id`, `title`, `body`, `data` |
| `NotificationTapped` | `id`, `title`, `body`, `data` |
| `NotificationActionPressed` | `notificationId`, `actionId`, `inputText` |
| `PermissionGranted` | — |
| `PermissionDenied` | — |
