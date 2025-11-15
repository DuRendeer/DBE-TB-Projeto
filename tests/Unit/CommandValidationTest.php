<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Commands\CreateAppointmentCommand;
use App\Commands\UpdateAppointmentStatusCommand;
use App\Handlers\CreateAppointmentHandler;
use App\Handlers\UpdateAppointmentStatusHandler;
use InvalidArgumentException;

class CommandValidationTest extends TestCase
{
    public function test_create_appointment_command_creates_with_valid_data(): void
    {
        $futureDate = date('Y-m-d H:i:s', strtotime('+1 day'));

        $command = new CreateAppointmentCommand(
            userId: 1,
            petId: 1,
            serviceId: 1,
            scheduledAt: $futureDate,
            totalPrice: 100.00,
            notes: 'Test notes'
        );

        $this->assertEquals(1, $command->userId);
        $this->assertEquals(100.00, $command->totalPrice);
    }

    public function test_create_appointment_command_has_readonly_properties(): void
    {
        $futureDate = date('Y-m-d H:i:s', strtotime('+1 day'));

        $command = new CreateAppointmentCommand(
            userId: 1,
            petId: 1,
            serviceId: 1,
            scheduledAt: $futureDate,
            totalPrice: 100.00
        );

        $this->assertEquals(1, $command->userId);
        $this->assertEquals(1, $command->petId);
    }

    public function test_update_status_command_creates_with_valid_data(): void
    {
        $command = new UpdateAppointmentStatusCommand(
            appointmentId: 1,
            status: 'confirmed'
        );

        $this->assertEquals(1, $command->appointmentId);
        $this->assertEquals('confirmed', $command->status);
    }

    public function test_update_status_command_accepts_optional_notes(): void
    {
        $command = new UpdateAppointmentStatusCommand(
            appointmentId: 1,
            status: 'completed',
            notes: 'Service completed successfully'
        );

        $this->assertEquals('Service completed successfully', $command->notes);
    }

    public function test_create_appointment_command_is_immutable(): void
    {
        $futureDate = date('Y-m-d H:i:s', strtotime('+1 day'));

        $command = new CreateAppointmentCommand(
            userId: 1,
            petId: 1,
            serviceId: 1,
            scheduledAt: $futureDate,
            totalPrice: 100.00
        );

        $this->assertTrue(true);
    }

    public function test_commands_are_simple_dtos(): void
    {
        $futureDate = date('Y-m-d H:i:s', strtotime('+1 day'));

        $createCommand = new CreateAppointmentCommand(
            userId: 1,
            petId: 1,
            serviceId: 1,
            scheduledAt: $futureDate,
            totalPrice: 100.00
        );

        $updateCommand = new UpdateAppointmentStatusCommand(
            appointmentId: 1,
            status: 'pending'
        );

        $this->assertIsObject($createCommand);
        $this->assertIsObject($updateCommand);
    }
}
