# Epic 8: Notification V2 — Plugin v1.2.0 Features

**Status:** Done

## Description

Upgrade to `ikromjon/nativephp-mobile-local-notifications` v1.2.0 features to fix frequency-based scheduling, add iOS badge counts, richer notification content, and migrate from raw arrays to type-safe DTOs.

## Problem

The current `HabitNotificationService` uses `RepeatInterval::Daily` for ALL habits regardless of their frequency setting. A "weekdays only" habit still fires notifications on Saturday and Sunday. Only the UI respects frequency — notifications ignore it entirely.

## Scope

### Task 1: Fix frequency-based scheduling with `repeatDays`

**Priority:** Critical (bug fix)

Replace `RepeatInterval::Daily` with `repeatDays` for weekday/weekend habits:
- `daily` → keep `RepeatInterval::Daily` (fires every day)
- `weekdays` → use `repeatDays: [1, 2, 3, 4, 5]` (Mon–Fri, ISO format)
- `weekends` → use `repeatDays: [6, 7]` (Sat–Sun, ISO format)

`repeatDays` requires the `at` parameter (already used). Remove `repeat` when using `repeatDays` since they are mutually exclusive.

**Files to modify:**
- `app/Services/HabitNotificationService.php` — Update `schedule()` to build correct repeat config based on `$habit->frequency`

### Task 2: Migrate to `NotificationOptions` and `NotificationAction` DTOs

**Priority:** High (code quality)

Replace all raw arrays passed to `LocalNotifications::schedule()` with the type-safe `NotificationOptions` DTO and `NotificationAction` DTO. This gives:
- Compile-time type checking (PHPStan level 8)
- Built-in validation from `NotificationValidator`
- Cleaner, self-documenting code

**Files to modify:**
- `app/Services/HabitNotificationService.php` — Refactor `schedule()`, `snooze()` to use DTOs

### Task 3: Add iOS badge count

**Priority:** Medium (UX improvement)

Set the `badge` parameter to show the number of incomplete habits for today on the app icon. Update the badge when:
- A notification is scheduled (set badge = remaining incomplete habits)
- A habit is toggled complete/incomplete (update badge on all scheduled notifications)

**Files to modify:**
- `app/Services/HabitNotificationService.php` — Add badge calculation
- `app/Models/Habit.php` — Add helper `incompleteCountToday()` (static)

### Task 4: Add `bigText` for expanded notifications

**Priority:** Medium (UX improvement)

Use the `bigText` parameter to show richer content when the notification is expanded:
- Include the habit description (if set)
- Include current streak info
- Example: "Run for 30 minutes\n🔥 12-day streak — keep it going!"

**Files to modify:**
- `app/Services/HabitNotificationService.php` — Build `bigText` from habit data

## Implementation Order

1. **Task 1** (repeatDays) — Must be first, it's a correctness fix
2. **Task 2** (DTOs) — Refactor while modifying the service
3. **Task 3** (badge) — Builds on the DTO structure
4. **Task 4** (bigText) — Simple addition to the DTO

All four tasks modify the same file (`HabitNotificationService.php`), so they should be done together in a single branch.

## Acceptance Criteria

- [x] Weekday habits only fire notifications Mon–Fri
- [x] Weekend habits only fire notifications Sat–Sun
- [x] Daily habits fire every day (unchanged behavior)
- [x] All `schedule()` calls use `NotificationOptions` DTO
- [x] All action buttons use `NotificationAction` DTO
- [x] iOS badge shows incomplete habit count
- [x] Expanded notifications show description + streak
- [x] Existing tests pass (44 original)
- [x] New tests cover frequency-based scheduling logic (13 new, 57 total)
- [x] PHPStan level 8 passes
- [x] Plugin version constraint updated to `^1.2`

## Out of Scope

- `getPending()` debug view (future epic)
- `NotificationTapped` navigation handling (future epic)
- `repeatCount`, `image`, `input` actions (not relevant)
- `Monthly` / `Yearly` repeat intervals (not relevant for daily habits)

## Notes

- `repeatDays` uses ISO weekday format: 1=Monday through 7=Sunday
- `repeatDays` is mutually exclusive with `repeat` and `repeatIntervalSeconds`
- `repeatDays` requires the `at` parameter (already used in current implementation)
- The `NotificationOptions` DTO has a `toArray()` method with built-in validation
- Snooze notifications are one-shot (delay-based), so they don't need `repeatDays`
- Test mode notifications are one-shot (delay-based), so they don't need `repeatDays`
