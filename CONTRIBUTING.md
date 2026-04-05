# Contributing to Daily Habits

Thanks for your interest in contributing! This guide will help you get started.

## Ways to Contribute

- **Bug reports** — Found something broken? Open an issue with steps to reproduce.
- **Feature requests** — Have an idea? Open an issue to discuss it first.
- **Pull requests** — Bug fixes, improvements, and new features are all welcome.
- **Documentation** — Spot a typo or missing info? PRs for docs are appreciated.
- **Forking** — Want to build your own app on top of this? Fork away — that's what open source is for.

## Getting Started

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/YOUR_USERNAME/daily-habits.git
   cd daily-habits
   ```
3. Install dependencies:
   ```bash
   composer install && npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```
4. Create a branch for your changes:
   ```bash
   git checkout -b feature/your-feature-name
   ```

## Development Workflow

### Running Locally

```bash
composer run dev
```

### Running on Device

```bash
php artisan native:run android
# or
php artisan native:run ios
```

### Quality Gates

All of these must pass before submitting a PR:

```bash
composer lint          # Format code with Pint
composer analyse       # PHPStan level 8 static analysis
composer rector:check  # Rector dry-run
composer test          # Run test suite
```

Run `composer lint` to auto-fix formatting. Run the rest to check for errors.

### Running Tests

```bash
# All tests
php artisan test

# Specific test
php artisan test --filter=HabitTest
```

## Code Style

- **PHP 8.4** features — constructor promotion, match expressions, enums, etc.
- **Explicit return types** on all methods
- **Curly braces** for all control structures
- **PHPDoc** `@return` with generics on Eloquent relationships
- **Tailwind CSS 4** syntax — `@theme` in CSS, no tailwind.config.js
- **Livewire** — class-based components only, not Volt
- **PHPUnit** for tests (not Pest)

When in doubt, follow the patterns in existing code. Check sibling files before creating new ones.

## Submitting a Pull Request

1. Make sure all quality gates pass
2. Write a clear PR title and description
3. Reference any related issues (e.g., "Fixes #12")
4. Keep PRs focused — one feature or fix per PR
5. Add tests for new functionality when applicable

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
  views/
    layouts/         Base layout with bottom nav, safe areas, timezone detection
    livewire/        Component views (today, settings, habit-form, notification-debug)
plan/                Epic documents (development roadmap)
tests/
  Feature/           HabitFormTest, TodayTest, PageTest, HabitNotificationServiceTest
  Unit/              HabitTest
```

## Testing Notifications on Device

Notifications require a native build (`php artisan native:run android` or `ios`). They do not work with `php artisan native:jump`.

1. Build and deploy to your device
2. Navigate to **Settings > Notification Debug**
3. Run all 9 test scenarios — each one logs events in real-time
4. Check the Event Log at the bottom for pass/fail results

Scenario 7 tests the Laravel Notification channel integration end-to-end. See `app/Notifications/DebugLocalNotification.php` for the implementation and `app/Livewire/NotificationDebug.php` for all test scenarios.

## Reporting Issues

When reporting a bug, include:

- Device and OS version (e.g., Samsung Galaxy S23, Android 14)
- Steps to reproduce
- Expected vs actual behavior
- Screenshots if applicable

## License

By contributing, you agree that your contributions will be licensed under the MIT License.
