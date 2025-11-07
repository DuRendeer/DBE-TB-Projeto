<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationInterface;
use Illuminate\Support\Facades\Log;

class PushNotification implements NotificationInterface
{
    public function send(string $recipient, string $subject, string $message): bool
    {
        // Simula envio de notificação push
        Log::info("Push notification sent to {$recipient}: {$subject} - {$message}");

        // Em produção, usar Firebase Cloud Messaging ou similar
        // FCM::sendTo($recipient, $message);

        return true;
    }

    public function getChannel(): string
    {
        return 'push';
    }
}
