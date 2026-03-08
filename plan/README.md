# Daily Habits - Development Plan

A mobile-first daily habits tracker built with Laravel, Livewire, and NativePHP Mobile. Uses the `ikromjon/nativephp-mobile-local-notifications` plugin for habit reminders.

## App Concept

A clean, polished habits app where users can:
- Create daily habits with emoji icons
- Set reminder times and get native notifications
- Mark habits as complete with action buttons directly from notifications
- Track completion streaks
- View progress over time

## Epics

| # | Epic | File | Status |
|---|------|------|--------|
| 1 | Base Layout & App Shell | [01-base-layout.md](01-base-layout.md) | Done |
| 2 | Habit Model & Database | [02-habit-model.md](02-habit-model.md) | Done |
| 3 | Home Screen - Today's Habits | [03-home-screen.md](03-home-screen.md) | Done |
| 4 | Add & Edit Habits | [04-add-edit-habits.md](04-add-edit-habits.md) | Done |
| 5 | Notification Reminders | [05-notifications.md](05-notifications.md) | Done |
| 6 | Completion Tracking & Streaks | [06-streaks.md](06-streaks.md) | Not Started |
| 7 | UI Polish & Final Touches | [07-polish.md](07-polish.md) | Not Started |

## Tech Stack

- Laravel 12 + Livewire 4
- NativePHP Mobile v3
- `ikromjon/nativephp-mobile-local-notifications` v1.1.0
- Tailwind CSS 4
- SQLite
