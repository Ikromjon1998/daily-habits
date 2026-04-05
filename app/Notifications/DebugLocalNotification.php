<?php

namespace App\Notifications;

use Ikromjon\LocalNotifications\Notifications\HasLocalNotification;
use Ikromjon\LocalNotifications\Notifications\LocalNotificationChannel;
use Ikromjon\LocalNotifications\Notifications\LocalNotificationMessage;
use Illuminate\Notifications\Notification;

class DebugLocalNotification extends Notification implements HasLocalNotification
{
    /** @return array<int, class-string> */
    public function via(object $notifiable): array
    {
        return [LocalNotificationChannel::class];
    }

    public function toLocalNotification(object $notifiable): LocalNotificationMessage
    {
        return LocalNotificationMessage::create()
            ->id('debug-channel')
            ->title('Laravel Channel Test')
            ->body('Sent via Laravel Notification channel')
            ->delay(10)
            ->sound()
            ->action('ok', 'OK')
            ->action('cancel', 'Cancel', destructive: true)
            ->action('snooze', 'Snooze (5m)', snooze: 300)
            ->data(['scenario' => 'laravel-channel', 'ts' => now()->timestamp]);
    }
}
