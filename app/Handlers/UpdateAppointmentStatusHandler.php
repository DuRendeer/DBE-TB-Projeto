<?php

namespace App\Handlers;

use App\Commands\UpdateAppointmentStatusCommand;
use App\Models\Appointment;
use App\Factories\NotificationFactory;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UpdateAppointmentStatusHandler
{
    private const VALID_STATUSES = [
        'pending', 'confirmed', 'in_progress', 'completed', 'cancelled'
    ];

    public function handle(UpdateAppointmentStatusCommand $command): Appointment
    {
        $this->validate($command);

        return DB::transaction(function () use ($command) {
            $appointment = Appointment::findOrFail($command->appointmentId);

            $data = ['status' => $command->status];
            if ($command->notes !== null) {
                $data['notes'] = $command->notes;
            }

            $appointment->update($data);
            $appointment->load(['user', 'pet', 'service']);
            $this->sendStatusChangeNotification($appointment);

            return $appointment;
        });
    }

    private function validate(UpdateAppointmentStatusCommand $command): void
    {
        if (!in_array($command->status, self::VALID_STATUSES)) {
            throw new InvalidArgumentException('Invalid status. Valid options: ' . implode(', ', self::VALID_STATUSES));
        }
    }

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
