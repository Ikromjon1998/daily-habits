# Notification Plugin Bug Report: `repeat` Parameter Issue

## Summary

The `repeat` parameter in local notifications does not work reliably on Android. Notifications scheduled with `repeat: 'daily'` (or any repeat interval) are never delivered, while identical notifications without the `repeat` parameter work correctly.

## Testing Environment

- **App**: Daily Habits (NativePHP Mobile v3)
- **Plugin**: `ikromjon/nativephp-mobile-local-notifications` v1.1.0
- **Platform**: Android (Samsung device, API 33+)
- **PHP Version**: 8.4.1
- **Laravel**: 12

## How We Discovered the Bug

### Initial Problem
The app requires daily habit reminders. We tested by creating habits with reminder times and noticed notifications never fired.

### Testing Approach
We added debug buttons to the Settings page to systematically test different notification configurations:

1. **Test Notification (10 sec)** - Simple notification
2. **Test Now (2 sec)** - Immediate test
3. **Test Habits (15 sec)** - Multiple test notifications with different configurations

### Debug Buttons Added (for testing purposes):

```php
// In app/Livewire/Settings.php
public function rescheduleAllHabitsTest(): void
{
    // Test 1: Simple notification (no repeat, no actions)
    LocalNotifications::schedule([
        'id' => 'simple-test-1',
        'title' => 'Simple Test 1',
        'body' => 'Testing without actions',
        'delay' => 10,
        'sound' => true,
    ]);
    
    // Test 2: With actions (no repeat)
    LocalNotifications::schedule([
        'id' => 'actions-test-1',
        'title' => 'Actions Test',
        'body' => 'Testing with actions',
        'delay' => 15,
        'sound' => true,
        'actions' => [
            ['id' => 'done', 'title' => 'Done'],
            ['id' => 'snooze', 'title' => 'Snooze'],
        ],
    ]);
    
    // Test 3: With repeat
    LocalNotifications::schedule([
        'id' => 'repeat-test-1',
        'title' => 'Repeat Test',
        'body' => 'Testing with repeat',
        'delay' => 20,
        'sound' => true,
        'repeat' => 'daily',
    ]);
}
```

## Test Results

### Test 1: Simple Notification (No Repeat)
```php
LocalNotifications::schedule([
    'id' => 'simple-test-1',
    'title' => 'Simple Test 1',
    'body' => 'Testing without actions',
    'delay' => 10,
    'sound' => true,
]);
```
**Result**: ✅ Works - Notification appeared after 10 seconds

### Test 2: Notification with Actions (No Repeat)
```php
LocalNotifications::schedule([
    'id' => 'actions-test-1',
    'title' => 'Actions Test',
    'body' => 'Testing with actions',
    'delay' => 15,
    'sound' => true,
    'actions' => [
        ['id' => 'done', 'title' => 'Done'],
        ['id' => 'snooze', 'title' => 'Snooze'],
    ],
]);
```
**Result**: ✅ Works - Notification appeared after 15 seconds with action buttons

### Test 3: Notification with Repeat
```php
LocalNotifications::schedule([
    'id' => 'repeat-test-1',
    'title' => 'Repeat Test',
    'body' => 'Testing with repeat',
    'delay' => 20,
    'sound' => true,
    'repeat' => 'daily',
]);
```
**Result**: ❌ **FAILED** - No notification appeared after 20 seconds

## Root Cause Analysis

The plugin uses Android's `AlarmManager.setRepeating()` for repeating alarms:

```kotlin
// From LocalNotificationsFunctions.kt (lines 197-203)
if (repeatMs > 0) {
    alarmManager.setRepeating(
        AlarmManager.RTC_WAKEUP,
        triggerTimeMs,
        repeatMs,
        pendingIntent
    )
} else {
    alarmManager.setExactAndAllowWhileIdle(
        AlarmManager.RTC_WAKEUP,
        triggerTimeMs,
        pendingIntent
    )
}
```

### Known Issues with `setRepeating()`:

1. **Exact alarms require `setExactAndAllowWhileIdle`** - `setRepeating` is inexact by design and may be delayed by the system
2. **Android 12+ (API 31+) changes** - New restrictions on alarm permissions
3. **Battery optimization** - Repeating alarms are more likely to be blocked
4. **The repeat interval must be > 60 seconds** - shorter intervals may not work

## Current Workaround

For the Daily Habits app, we removed the `repeat` parameter and implemented manual rescheduling:

### Step 1: Remove `repeat` from scheduling (app/Services/HabitNotificationService.php):

```php
// BEFORE (broken):
LocalNotifications::schedule([
    'id' => $notificationId,
    'title' => $habit->emoji.' '.$habit->name,
    'body' => $body,
    'delay' => $delay,
    'repeat' => 'daily',  // This breaks the notification!
    'sound' => true,
    'data' => ['habit_id' => $habit->id],
    'actions' => [
        ['id' => 'done', 'title' => 'Done'],
        ['id' => 'snooze', 'title' => 'Snooze'],
    ],
]);

// AFTER (works):
LocalNotifications::schedule([
    'id' => $notificationId,
    'title' => $habit->emoji.' '.$habit->name,
    'body' => $body,
    'delay' => $delay,
    // 'repeat' => 'daily',  // REMOVED - this was causing the bug
    'sound' => true,
    'data' => ['habit_id' => $habit->id],
    'actions' => [
        ['id' => 'done', 'title' => 'Done'],
        ['id' => 'snooze', 'title' => 'Snooze'],
    ],
]);
```

### Step 2: Add Event Listener for Notification Received

When a notification fires, we catch that event and immediately reschedule it for the next day:

```php
// In app/Livewire/Today.php
use Ikromjon\LocalNotifications\Events\NotificationReceived;

// Add event listener
#[OnNative(NotificationReceived::class)]
public function onNotificationReceived(array $data = []): void
{
    $notificationId = $data['id'] ?? '';
    
    // Only reschedule habit notifications (format: "habit-{id}")
    if (! str_starts_with($notificationId, 'habit-')) {
        return;
    }
    
    $habitId = (int) str_replace('habit-', '', $notificationId);
    $habit = Habit::find($habitId);
    
    if ($habit) {
        // Reschedule for tomorrow
        app(HabitNotificationService::class)->schedule($habit);
    }
}
```

### How It Works Now

1. User creates a habit with reminder time (e.g., 7:00 AM)
2. App schedules notification WITHOUT the `repeat` parameter
3. Notification fires at 7:00 AM
4. `NotificationReceived` event fires
5. App catches the event and reschedules for tomorrow at 7:00 AM
6. Repeat infinitely - no native `repeat` needed!

## Recommendations for Plugin Fix

1. **Use `setExactAndAllowWhileIdle` for all alarms** (not just non-repeating)
2. **Implement manual rescheduling** instead of relying on Android's `setRepeating`
3. **Add proper error handling** when scheduling fails
4. **Add debugging/logging** to help diagnose issues
5. **Consider using WorkManager** for reliable background work

## Additional Notes

- Timezone was initially set to UTC but device was in Europe/Berlin - this caused 1 hour offset issues
- Fixed by changing `config/app.php` timezone to `'Europe/Berlin'`
- Notification permission must be granted (Android 13+ requires `POST_NOTIFICATIONS` permission)

## Additional Issue: Scrolling Not Working

### Problem
When there were many habits, the page could not be scrolled down to see all items.

### Root Cause
The body had `overflow-hidden` which prevented native scrolling on mobile.

### Fix (resources/views/layouts/app.blade.php)

```html
<!-- BEFORE (broken) -->
<body class="bg-gray-950 text-white antialiased min-h-screen flex flex-col overflow-hidden">

<!-- AFTER (works) -->
<body class="bg-gray-950 text-white antialiased h-screen flex flex-col">
```

And in the main content area:

```html
<!-- BEFORE -->
<main class="flex-1 overflow-y-auto" ...>

<!-- AFTER -->
<main class="flex-1 overflow-y-auto overscroll-y-none" ...>
```
