<?php

namespace App\Notifications;

use Ikromjon\LocalNotifications\Notifications\HasLocalNotification;
use Ikromjon\LocalNotifications\Notifications\LocalNotificationChannel;
use Ikromjon\LocalNotifications\Notifications\LocalNotificationMessage;
use Illuminate\Notifications\Notification;

class DebugLocalNotification extends Notification implements HasLocalNotification
{
    public function __construct(
        private readonly bool $useCustomSound = false,
    ) {}

    /** @return array<int, class-string> */
    public function via(object $notifiable): array
    {
        return [LocalNotificationChannel::class];
    }

    public function toLocalNotification(object $notifiable): LocalNotificationMessage
    {
        $message = LocalNotificationMessage::create()
            ->title($this->useCustomSound ? 'Custom Sound Channel Test' : 'Laravel Channel Test')
            ->body($this->useCustomSound ? 'This should play a custom alert sound via Laravel Channel' : 'Sent via Laravel Notification channel')
            ->delay(10)
            ->action('ok', 'OK')
            ->action('cancel', 'Cancel', destructive: true)
            ->action('snooze', 'Snooze (5m)', snooze: 300);

        if ($this->useCustomSound) {
            $message->id('debug-channel-sound')
                ->sound('alert.wav')
                ->data(['scenario' => 'custom-sound-channel', 'ts' => now()->timestamp]);
        } else {
            $message->id('debug-channel')
                ->sound()
                ->data(['scenario' => 'laravel-channel', 'ts' => now()->timestamp]);
        }

        return $message;
    }
}
