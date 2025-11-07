<?php

namespace App\Handlers;

use App\Commands\UpdateAppointmentStatusCommand;
use App\Models\Appointment;
use App\Factories\NotificationFactory;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * CQRS Command Handler - Update Appointment Status
 */
class UpdateAppointmentStatusHandler
{
    /**
     * Handle the command
     *
     * @throws InvalidArgumentException
     */
    public function handle(UpdateAppointmentStatusCommand $command): Appointment
    {
        // Valida o comando
        $errors = $command->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors));
        }

        return DB::transaction(function () use ($command) {
            $appointment = Appointment::findOrFail($command->appointmentId);

            // Atualiza o status
            $appointment->update($command->toArray());

            // Carrega relacionamentos
            $appointment->load(['user', 'pet', 'service']);

            // Envia notificação sobre mudança de status
            $this->sendStatusChangeNotification($appointment);

            return $appointment;
        });
    }

    /**
     * Send notification about status change
     */
    private function sendStatusChangeNotification(Appointment $appointment): void
    {
        try {
            $notification = NotificationFactory::create('email');

            $statusMessages = [
                'confirmed' => 'confirmado',
                'in_progress' => 'em andamento',
                'completed' => 'concluído',
                'cancelled' => 'cancelado'
            ];

            $statusText = $statusMessages[$appointment->status] ?? $appointment->status;

            $notification->send(
                $appointment->user->email,
                'Status do Agendamento Atualizado',
                "Seu agendamento foi {$statusText}."
            );
        } catch (\Exception $e) {
            \Log::warning("Failed to send status notification: {$e->getMessage()}");
        }
    }
}
