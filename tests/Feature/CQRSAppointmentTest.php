<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pet;
use App\Models\Service;
use App\Handlers\CreateAppointmentHandler;
use App\Handlers\UpdateAppointmentStatusHandler;
use App\Handlers\GetUserAppointmentsHandler;
use App\Commands\CreateAppointmentCommand;
use App\Commands\UpdateAppointmentStatusCommand;
use App\Queries\GetUserAppointmentsQuery;

/**
 * Feature Test - CQRS Pattern Implementation
 *
 * Testa a integração completa do padrão CQRS
 */
class CQRSAppointmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pet $pet;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria dados de teste
        $this->user = User::factory()->create();

        $this->pet = Pet::create([
            'user_id' => $this->user->id,
            'name' => 'Rex',
            'species' => 'dog',
            'breed' => 'Labrador',
            'age' => 3
        ]);

        $this->service = Service::create([
            'name' => 'Banho e Tosa',
            'description' => 'Serviço completo de banho e tosa',
            'price' => 80.00,
            'duration_minutes' => 120
        ]);
    }

    public function test_can_create_appointment_using_cqrs_command(): void
    {
        $handler = new CreateAppointmentHandler();

        $command = new CreateAppointmentCommand(
            userId: $this->user->id,
            petId: $this->pet->id,
            serviceId: $this->service->id,
            scheduledAt: now()->addDays(1)->format('Y-m-d H:i:s'),
            totalPrice: 80.00,
            notes: 'Pet muito agitado'
        );

        $appointment = $handler->handle($command);

        $this->assertNotNull($appointment);
        $this->assertEquals($this->user->id, $appointment->user_id);
        $this->assertEquals($this->pet->id, $appointment->pet_id);
        $this->assertEquals('pending', $appointment->status);
        $this->assertEquals('Pet muito agitado', $appointment->notes);

        // Verifica que foi salvo no banco
        $this->assertDatabaseHas('appointments', [
            'user_id' => $this->user->id,
            'pet_id' => $this->pet->id,
            'status' => 'pending'
        ]);
    }

    public function test_can_query_user_appointments_using_cqrs(): void
    {
        // Cria alguns agendamentos
        $handler = new CreateAppointmentHandler();

        for ($i = 1; $i <= 3; $i++) {
            $command = new CreateAppointmentCommand(
                userId: $this->user->id,
                petId: $this->pet->id,
                serviceId: $this->service->id,
                scheduledAt: now()->addDays($i)->format('Y-m-d H:i:s'),
                totalPrice: 80.00
            );

            $handler->handle($command);
        }

        // Executa Query
        $queryHandler = new GetUserAppointmentsHandler();
        $query = new GetUserAppointmentsQuery(userId: $this->user->id);

        $appointments = $queryHandler->handle($query);

        $this->assertCount(3, $appointments);
        $this->assertEquals($this->user->id, $appointments->first()->user_id);
    }

    public function test_can_filter_appointments_by_status(): void
    {
        $createHandler = new CreateAppointmentHandler();

        // Cria appointment pending
        $command1 = new CreateAppointmentCommand(
            userId: $this->user->id,
            petId: $this->pet->id,
            serviceId: $this->service->id,
            scheduledAt: now()->addDays(1)->format('Y-m-d H:i:s'),
            totalPrice: 80.00
        );
        $appointment = $createHandler->handle($command1);

        // Atualiza para confirmed
        $updateHandler = new UpdateAppointmentStatusHandler();
        $updateCommand = new UpdateAppointmentStatusCommand(
            appointmentId: $appointment->id,
            status: 'confirmed'
        );
        $updateHandler->handle($updateCommand);

        // Cria outro pending
        $command2 = new CreateAppointmentCommand(
            userId: $this->user->id,
            petId: $this->pet->id,
            serviceId: $this->service->id,
            scheduledAt: now()->addDays(2)->format('Y-m-d H:i:s'),
            totalPrice: 80.00
        );
        $createHandler->handle($command2);

        // Query por status confirmed
        $queryHandler = new GetUserAppointmentsHandler();
        $query = new GetUserAppointmentsQuery(
            userId: $this->user->id,
            status: 'confirmed'
        );

        $confirmedAppointments = $queryHandler->handle($query);

        $this->assertCount(1, $confirmedAppointments);
        $this->assertEquals('confirmed', $confirmedAppointments->first()->status);
    }

    public function test_can_update_appointment_status_using_cqrs_command(): void
    {
        // Cria appointment
        $createHandler = new CreateAppointmentHandler();
        $createCommand = new CreateAppointmentCommand(
            userId: $this->user->id,
            petId: $this->pet->id,
            serviceId: $this->service->id,
            scheduledAt: now()->addDays(1)->format('Y-m-d H:i:s'),
            totalPrice: 80.00
        );
        $appointment = $createHandler->handle($createCommand);

        // Atualiza status
        $updateHandler = new UpdateAppointmentStatusHandler();
        $updateCommand = new UpdateAppointmentStatusCommand(
            appointmentId: $appointment->id,
            status: 'completed',
            notes: 'Serviço concluído com sucesso'
        );

        $updatedAppointment = $updateHandler->handle($updateCommand);

        $this->assertEquals('completed', $updatedAppointment->status);
        $this->assertEquals('Serviço concluído com sucesso', $updatedAppointment->notes);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed'
        ]);
    }

    public function test_command_validation_prevents_invalid_data(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $handler = new CreateAppointmentHandler();

        // Preço negativo deve falhar
        $command = new CreateAppointmentCommand(
            userId: $this->user->id,
            petId: $this->pet->id,
            serviceId: $this->service->id,
            scheduledAt: now()->addDays(1)->format('Y-m-d H:i:s'),
            totalPrice: -50.00 // Inválido
        );

        $handler->handle($command);
    }

    public function test_can_count_user_appointments(): void
    {
        $createHandler = new CreateAppointmentHandler();

        // Cria 5 agendamentos
        for ($i = 1; $i <= 5; $i++) {
            $command = new CreateAppointmentCommand(
                userId: $this->user->id,
                petId: $this->pet->id,
                serviceId: $this->service->id,
                scheduledAt: now()->addDays($i)->format('Y-m-d H:i:s'),
                totalPrice: 80.00
            );

            $createHandler->handle($command);
        }

        $queryHandler = new GetUserAppointmentsHandler();
        $query = new GetUserAppointmentsQuery(userId: $this->user->id);

        $count = $queryHandler->count($query);

        $this->assertEquals(5, $count);
    }

    public function test_query_can_limit_results(): void
    {
        $createHandler = new CreateAppointmentHandler();

        // Cria 10 agendamentos
        for ($i = 1; $i <= 10; $i++) {
            $command = new CreateAppointmentCommand(
                userId: $this->user->id,
                petId: $this->pet->id,
                serviceId: $this->service->id,
                scheduledAt: now()->addDays($i)->format('Y-m-d H:i:s'),
                totalPrice: 80.00
            );

            $createHandler->handle($command);
        }

        $queryHandler = new GetUserAppointmentsHandler();
        $query = new GetUserAppointmentsQuery(
            userId: $this->user->id,
            limit: 5
        );

        $appointments = $queryHandler->handle($query);

        $this->assertCount(5, $appointments);
    }
}
