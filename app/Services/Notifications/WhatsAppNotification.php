<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationInterface;
use Illuminate\Support\Facades\Log;

class WhatsAppNotification implements NotificationInterface
{
    public function send(string $recipient, string $subject, string $message): bool
    {
        Log::info("WhatsApp sent to {$recipient}: {$subject} - {$message}");
        return true;
    }
}
