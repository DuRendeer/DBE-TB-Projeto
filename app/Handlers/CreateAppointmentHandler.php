<?php

namespace App\Handlers;

use App\Commands\CreateAppointmentCommand;
use App\Models\Appointment;
use App\Factories\NotificationFactory;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateAppointmentHandler
{
    public function handle(CreateAppointmentCommand $command): Appointment
    {
        $this->validate($command);

        return DB::transaction(function () use ($command) {
            $appointment = Appointment::create([
                'user_id' => $command->userId,
                'pet_id' => $command->petId,
                'service_id' => $command->serviceId,
                'scheduled_at' => $command->scheduledAt,
                'total_price' => $command->totalPrice,
                'notes' => $command->notes,
                'status' => 'pending'
            ]);

            $appointment->load(['user', 'pet', 'service']);
            $this->sendNotification($appointment);

            return $appointment;
        });
    }

    private function validate(CreateAppointmentCommand $command): void
    {
        if ($command->totalPrice < 0) {
            throw new InvalidArgumentException('Total price must be positive');
        }

        if (strtotime($command->scheduledAt) <= time()) {
            throw new InvalidArgumentException('Scheduled date must be in the future');
        }
    }

    private function sendNotification(Appointment $appointment): void
    {
        try {
            $notification = NotificationFactory::create('email');
            $notification->send(
                $appointment->user->email,
                'Agendamento Criado',
                "Seu agendamento para {$appointment->scheduled_at} foi criado com sucesso!"
            );
        } catch (\Exception $e) {
            \Log::warning("Failed to send notification: {$e->getMessage()}");
        }
    }
}
