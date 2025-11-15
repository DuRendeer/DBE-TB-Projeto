<?php

namespace App\Factories;

use App\Contracts\NotificationInterface;
use App\Services\Notifications\EmailNotification;
use App\Services\Notifications\SmsNotification;
use App\Services\Notifications\PushNotification;
use App\Services\Notifications\WhatsAppNotification;
use InvalidArgumentException;

class NotificationFactory
{
    private static array $channels = [
        'email' => EmailNotification::class,
        'sms' => SmsNotification::class,
        'push' => PushNotification::class,
        'whatsapp' => WhatsAppNotification::class,
    ];

    public static function create(string $channel): NotificationInterface
    {
        $channel = strtolower($channel);

        if (!isset(self::$channels[$channel])) {
            throw new InvalidArgumentException("Notification channel '{$channel}' is not supported.");
        }

        $class = self::$channels[$channel];
        return new $class();
    }

    public static function createMultiple(array $channels): array
    {
        return array_map(fn($channel) => self::create($channel), $channels);
    }

    public static function getAvailableChannels(): array
    {
        return array_keys(self::$channels);
    }

    public static function registerChannel(string $name, string $class): void
    {
        self::$channels[strtolower($name)] = $class;
    }
}

