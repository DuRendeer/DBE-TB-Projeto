<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Factories\NotificationFactory;
use App\Services\Notifications\EmailNotification;
use App\Services\Notifications\SmsNotification;
use App\Services\Notifications\PushNotification;
use InvalidArgumentException;

/**
 * Unit Test - Factory Method Pattern
 *
 * Testa a criação de diferentes tipos de notificações
 */
class NotificationFactoryTest extends TestCase
{
    public function test_can_create_email_notification(): void
    {
        $notification = NotificationFactory::create('email');

        $this->assertInstanceOf(EmailNotification::class, $notification);
        $this->assertEquals('email', $notification->getChannel());
    }

    public function test_can_create_sms_notification(): void
    {
        $notification = NotificationFactory::create('sms');

        $this->assertInstanceOf(SmsNotification::class, $notification);
        $this->assertEquals('sms', $notification->getChannel());
    }

    public function test_can_create_push_notification(): void
    {
        $notification = NotificationFactory::create('push');

        $this->assertInstanceOf(PushNotification::class, $notification);
        $this->assertEquals('push', $notification->getChannel());
    }

    public function test_factory_is_case_insensitive(): void
    {
        $notification = NotificationFactory::create('EMAIL');

        $this->assertInstanceOf(EmailNotification::class, $notification);
    }

    public function test_throws_exception_for_invalid_channel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Notification channel 'invalid' is not supported");

        NotificationFactory::create('invalid');
    }

    public function test_get_available_channels(): void
    {
        $channels = NotificationFactory::getAvailableChannels();

        $this->assertIsArray($channels);
        $this->assertContains('email', $channels);
        $this->assertContains('sms', $channels);
        $this->assertContains('push', $channels);
        $this->assertCount(3, $channels);
    }
}
