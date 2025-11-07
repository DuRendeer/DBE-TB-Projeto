<?php

namespace App\Factories;

use App\Contracts\NotificationInterface;
use App\Services\Notifications\EmailNotification;
use App\Services\Notifications\SmsNotification;
use App\Services\Notifications\PushNotification;
use InvalidArgumentException;

/**
 * Factory Method Pattern
 *
 * Responsável por criar instâncias de diferentes tipos de notificações
 * sem expor a lógica de criação ao cliente.
 *
 * Benefícios:
 * - Desacopla a criação de objetos do código cliente
 * - Facilita a adição de novos tipos de notificação (Open/Closed Principle)
 * - Centraliza a lógica de criação em um único lugar (Single Responsibility)
 */
class NotificationFactory
{
    /**
     * Create a notification instance based on the channel type
     *
     * @param string $channel
     * @return NotificationInterface
     * @throws InvalidArgumentException
     */
    public static function create(string $channel): NotificationInterface
    {
        return match (strtolower($channel)) {
            'email' => new EmailNotification(),
            'sms' => new SmsNotification(),
            'push' => new PushNotification(),
            default => throw new InvalidArgumentException("Notification channel '{$channel}' is not supported."),
        };
    }

    /**
     * Get all available notification channels
     *
     * @return array
     */
    public static function getAvailableChannels(): array
    {
        return ['email', 'sms', 'push'];
    }
}
