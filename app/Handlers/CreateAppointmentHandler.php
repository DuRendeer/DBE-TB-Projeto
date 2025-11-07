<?php

namespace App\Handlers;

use App\Commands\CreateAppointmentCommand;
use App\Models\Appointment;
use App\Factories\NotificationFactory;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * CQRS Command Handler
 *
 * Responsável por executar o comando de criação de agendamento.
 * Aplica Single Responsibility Principle: apenas cria agendamentos.
 */
class CreateAppointmentHandler
{
    /**
     * Handle the command
     *
     * @throws InvalidArgumentException
     */
    public function handle(CreateAppointmentCommand $command): Appointment
    {
        // Valida o comando
        $errors = $command->validate();
        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors));
        }

        // Inicia transação para garantir consistência
        return DB::transaction(function () use ($command) {
            // Cria o agendamento
            $appointment = Appointment::create($command->toArray());

            // Carrega relacionamentos
            $appointment->load(['user', 'pet', 'service']);

            // Envia notificação usando Factory Method
            $this->sendNotification($appointment);

            return $appointment;
        });
    }

    /**
     * Send notification to user (Factory Method Pattern)
     */
    private function sendNotification(Appointment $appointment): void
    {
        try {
            // Usa Factory para criar notificação por email
            $notification = NotificationFactory::create('email');

            $notification->send(
                $appointment->user->email,
                'Agendamento Criado',
                "Seu agendamento para {$appointment->scheduled_at} foi criado com sucesso!"
            );
        } catch (\Exception $e) {
            // Em produção, logar o erro mas não falhar a operação
            \Log::warning("Failed to send notification: {$e->getMessage()}");
        }
    }
}
