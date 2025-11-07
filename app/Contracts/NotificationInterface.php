<?php

namespace App\Contracts;

interface NotificationInterface
{
    /**
     * Send a notification message
     *
     * @param string $recipient
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function send(string $recipient, string $subject, string $message): bool;

    /**
     * Get the notification channel name
     *
     * @return string
     */
    public function getChannel(): string;
}
