<?php

namespace App\Livewire;

use Ikromjon\LocalNotifications\Events\NotificationActionPressed;
use Ikromjon\LocalNotifications\Events\NotificationReceived;
use Ikromjon\LocalNotifications\Events\NotificationScheduled;
use Ikromjon\LocalNotifications\Events\NotificationTapped;
use Ikromjon\LocalNotifications\Events\NotificationUpdated;
use Ikromjon\LocalNotifications\Events\PermissionGranted;
use Ikromjon\LocalNotifications\Facades\LocalNotifications;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;

class NotificationDebug extends Component
{
    /** @var array<int, array{time: string, event: string, data: string}> */
    public array $eventLog = [];

    public string $permissionStatus = 'unknown';

    public function mount(): void
    {
        $this->checkPermission();
    }

    public function checkPermission(): void
    {
        $result = LocalNotifications::checkPermission();
        $this->permissionStatus = $result['status'] ?? 'unknown';
        $this->log('CheckPermission called', 'Status: '.$this->permissionStatus);
    }

    /**
     * Scenario 1: Quick notification (10s) — tap while app is open.
     */
    public function scheduleWarmTest(): void
    {
        LocalNotifications::schedule([
            'id' => 'debug-warm',
            'title' => 'Warm Start Test',
            'body' => 'Tap this while app is OPEN',
            'delay' => 10,
            'sound' => true,
            'data' => ['scenario' => 'warm-start', 'ts' => now()->timestamp],
        ]);
        $this->log('Scheduled', 'debug-warm — fires in 10s (keep app open, then tap)');
    }

    /**
     * Scenario 2: Delayed notification (30s) — kill app, then tap.
     */
    public function scheduleColdTest(): void
    {
        LocalNotifications::schedule([
            'id' => 'debug-cold',
            'title' => 'Cold Start Test',
            'body' => 'KILL the app, then tap this notification',
            'delay' => 30,
            'sound' => true,
            'data' => ['scenario' => 'cold-start', 'ts' => now()->timestamp],
        ]);
        $this->log('Scheduled', 'debug-cold — fires in 30s (kill app, wait, then tap)');
    }

    /**
     * Scenario 3: Notification with action buttons — cold start.
     */
    public function scheduleActionTest(): void
    {
        LocalNotifications::schedule([
            'id' => 'debug-action',
            'title' => 'Action Button Test',
            'body' => 'Kill app, then press an action button',
            'delay' => 30,
            'sound' => true,
            'data' => ['scenario' => 'action-cold', 'ts' => now()->timestamp],
            'actions' => [
                ['id' => 'confirm', 'title' => 'Confirm'],
                ['id' => 'dismiss', 'title' => 'Dismiss', 'destructive' => true],
            ],
        ]);
        $this->log('Scheduled', 'debug-action — fires in 30s with action buttons');
    }

    /**
     * Scenario 4: Schedule then update content only.
     * Schedules a 60s notification, then immediately updates title/body.
     */
    public function scheduleUpdateContentTest(): void
    {
        LocalNotifications::schedule([
            'id' => 'debug-update',
            'title' => 'ORIGINAL Title',
            'body' => 'This should be REPLACED by the update',
            'delay' => 60,
            'sound' => true,
            'data' => ['scenario' => 'update-content', 'ts' => now()->timestamp],
        ]);
        $this->log('Scheduled', 'debug-update — original title/body, fires in 60s');

        LocalNotifications::update('debug-update', [
            'title' => 'UPDATED Title',
            'body' => 'Content was updated successfully!',
        ]);
        $this->log('Updated', 'debug-update — title/body changed, timing preserved');
    }

    /**
     * Scenario 5: Schedule then update timing.
     * Schedules a 120s notification, then updates to fire in 15s.
     */
    public function scheduleUpdateTimingTest(): void
    {
        LocalNotifications::schedule([
            'id' => 'debug-update-timing',
            'title' => 'Update Timing Test',
            'body' => 'Originally 120s, updated to 15s',
            'delay' => 120,
            'sound' => true,
        ]);
        $this->log('Scheduled', 'debug-update-timing — originally 120s delay');

        LocalNotifications::update('debug-update-timing', [
            'delay' => 15,
        ]);
        $this->log('Updated', 'debug-update-timing — rescheduled to 15s');
    }

    /**
     * Scenario 6: getPending after schedule + cancel.
     * Verifies the refactored schedule/cancel still work.
     */
    public function testGetPending(): void
    {
        LocalNotifications::schedule([
            'id' => 'debug-pending-1',
            'title' => 'Pending Test 1',
            'body' => 'Should appear in getPending',
            'delay' => 300,
        ]);
        LocalNotifications::schedule([
            'id' => 'debug-pending-2',
            'title' => 'Pending Test 2',
            'body' => 'Should also appear',
            'delay' => 300,
        ]);

        $pending = LocalNotifications::getPending();
        $this->log('GetPending', json_encode($pending, JSON_THROW_ON_ERROR));

        LocalNotifications::cancel('debug-pending-1');
        LocalNotifications::cancel('debug-pending-2');
        $this->log('Cancelled', 'debug-pending-1 + debug-pending-2');

        $afterCancel = LocalNotifications::getPending();
        $this->log('GetPending after cancel', json_encode($afterCancel, JSON_THROW_ON_ERROR));
    }

    public function clearLog(): void
    {
        $this->eventLog = [];
    }

    #[OnNative(NotificationScheduled::class)]
    public function onScheduled(mixed ...$data): void
    {
        $this->log('NotificationScheduled', json_encode($data, JSON_THROW_ON_ERROR));
    }

    #[OnNative(NotificationReceived::class)]
    public function onReceived(mixed ...$data): void
    {
        $this->log('NotificationReceived', json_encode($data, JSON_THROW_ON_ERROR));
    }

    #[OnNative(NotificationTapped::class)]
    public function onTapped(mixed ...$data): void
    {
        $this->log('NotificationTapped', json_encode($data, JSON_THROW_ON_ERROR));
    }

    #[OnNative(NotificationActionPressed::class)]
    public function onActionPressed(mixed ...$data): void
    {
        $this->log('NotificationActionPressed', json_encode($data, JSON_THROW_ON_ERROR));
    }

    #[OnNative(NotificationUpdated::class)]
    public function onUpdated(mixed ...$data): void
    {
        $this->log('NotificationUpdated', json_encode($data, JSON_THROW_ON_ERROR));
    }

    #[OnNative(PermissionGranted::class)]
    public function onPermissionGranted(): void
    {
        $this->permissionStatus = 'granted';
        $this->log('PermissionGranted', '');
    }

    private function log(string $event, string $data): void
    {
        array_unshift($this->eventLog, [
            'time' => now()->format('H:i:s'),
            'event' => $event,
            'data' => $data,
        ]);

        // Keep last 50 entries
        $this->eventLog = array_slice($this->eventLog, 0, 50);
    }

    public function render(): View
    {
        return view('livewire.notification-debug')
            ->layout('layouts.app');
    }
}
