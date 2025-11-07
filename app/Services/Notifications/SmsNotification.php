<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationInterface;
use Illuminate\Support\Facades\Log;

class SmsNotification implements NotificationInterface
{
    public function send(string $recipient, string $subject, string $message): bool
    {
        // Simula envio de SMS
        Log::info("SMS sent to {$recipient}: {$message}");

        // Em produção, usar serviço como Twilio
        // $twilio->messages->create($recipient, ['body' => $message]);

        return true;
    }

    public function getChannel(): string
    {
        return 'sms';
    }
}
