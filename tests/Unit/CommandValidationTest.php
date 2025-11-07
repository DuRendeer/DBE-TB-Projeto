<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Commands\CreateAppointmentCommand;
use App\Commands\UpdateAppointmentStatusCommand;

/**
 * Unit Test - CQRS Commands Validation
 *
 * Testa a validação dos comandos CQRS
 */
class CommandValidationTest extends TestCase
{
    public function test_create_appointment_command_validation_passes_with_valid_data(): void
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

        $errors = $command->validate();

        $this->assertEmpty($errors);
    }

    public function test_create_appointment_command_fails_with_negative_price(): void
    {
        $futureDate = date('Y-m-d H:i:s', strtotime('+1 day'));

        $command = new CreateAppointmentCommand(
            userId: 1,
            petId: 1,
            serviceId: 1,
            scheduledAt: $futureDate,
            totalPrice: -10.00
        );

        $errors = $command->validate();

        $this->assertNotEmpty($errors);
        $this->assertContains('Total price must be positive', $errors);
    }

    public function test_create_appointment_command_fails_with_past_date(): void
    {
        $pastDate = date('Y-m-d H:i:s', strtotime('-1 day'));

        $command = new CreateAppointmentCommand(
            userId: 1,
            petId: 1,
            serviceId: 1,
            scheduledAt: $pastDate,
            totalPrice: 100.00
        );

        $errors = $command->validate();

        $this->assertNotEmpty($errors);
        $this->assertContains('Scheduled date must be in the future', $errors);
    }

    public function test_create_appointment_command_to_array(): void
    {
        $futureDate = date('Y-m-d H:i:s', strtotime('+1 day'));

        $command = new CreateAppointmentCommand(
            userId: 1,
            petId: 2,
            serviceId: 3,
            scheduledAt: $futureDate,
            totalPrice: 150.00,
            notes: 'Special request'
        );

        $array = $command->toArray();

        $this->assertEquals(1, $array['user_id']);
        $this->assertEquals(2, $array['pet_id']);
        $this->assertEquals(3, $array['service_id']);
        $this->assertEquals($futureDate, $array['scheduled_at']);
        $this->assertEquals(150.00, $array['total_price']);
        $this->assertEquals('Special request', $array['notes']);
        $this->assertEquals('pending', $array['status']);
    }

    public function test_update_status_command_validation_passes_with_valid_status(): void
    {
        $validStatuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

        foreach ($validStatuses as $status) {
            $command = new UpdateAppointmentStatusCommand(
                appointmentId: 1,
                status: $status
            );

            $errors = $command->validate();

            $this->assertEmpty($errors, "Status '{$status}' should be valid");
        }
    }

    public function test_update_status_command_fails_with_invalid_status(): void
    {
        $command = new UpdateAppointmentStatusCommand(
            appointmentId: 1,
            status: 'invalid_status'
        );

        $errors = $command->validate();

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid status', $errors[0]);
    }
}
